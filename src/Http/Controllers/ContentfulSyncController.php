<?php

namespace Digia\Lumen\ContentfulSync\Http\Controllers;

use Contentful\Core\Resource\ResourceArray;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Entry;
use Contentful\Delivery\Resource\DeletedEntry;
use Digia\Lumen\ContentfulSync\Contracts\ContentfulSyncServiceContract;
use Digia\Lumen\ContentfulSync\Exceptions\ContentfulSyncException;
use Digia\Lumen\ContentfulSync\Http\Middleware\NewRelicMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use Nord\Lumen\Contentful\ContentfulServiceContract;

/**
 * Handles incoming webhooks from Contentful
 *
 * @package Digia\Lumen\ContentfulSync\Http\Controllers
 */
class ContentfulSyncController extends Controller
{

    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_PUBLISH   = 'ContentManagement.Asset.publish';
    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_UNPUBLISH = 'ContentManagement.Asset.unpublish';
    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_DELETE    = 'ContentManagement.Asset.delete';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_PUBLISH   = 'ContentManagement.Entry.publish';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_UNPUBLISH = 'ContentManagement.Entry.unpublish';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_DELETE    = 'ContentManagement.Entry.delete';

    /**
     * @var ContentfulServiceContract
     */
    private $contentfulService;

    /**
     * @var ContentfulSyncServiceContract
     */
    private $contentfulSyncService;

    /**
     * ContentfulSyncController constructor.
     *
     * @param ContentfulServiceContract     $contentfulService
     * @param ContentfulSyncServiceContract $contentfulSyncService
     */
    public function __construct(
        ContentfulServiceContract $contentfulService,
        ContentfulSyncServiceContract $contentfulSyncService
    ) {
        $this->contentfulService     = $contentfulService;
        $this->contentfulSyncService = $contentfulSyncService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \InvalidArgumentException if the space or environment ID is invalid
     * @throws ContentfulSyncException if the webhook cannot be handled
     */
    public function handleIncomingWebhook(Request $request): Response
    {
        $requestContent = (string)$request->getContent();

        // Instrument the request so the middleware can do its job
        $contentfulTopic = $this->getContentfulTopic($request);
        $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_TOPIC, $contentfulTopic);

        // Handle different topics differently
        switch ($contentfulTopic) {
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_PUBLISH:
                $this->contentfulSyncService->publishAsset($requestContent);
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_UNPUBLISH:
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_DELETE:
                $resource = $this->parseRequestContent($requestContent);
                $this->contentfulSyncService->deleteAsset($this->getResourceId($resource));
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_PUBLISH:
                /** @var Entry $resource */
                $resource    = $this->parseRequestContent($requestContent);
                $contentType = $this->getEntryContentType($resource);

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->contentfulSyncService->publishEntry($contentType, $requestContent);
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_UNPUBLISH:
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_DELETE:
                /** @var Entry $resource */
                $resource    = $this->parseRequestContent($requestContent);
                $contentType = $this->getDeletedEntryContentType($resource);

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->contentfulSyncService->deleteEntry($contentType, $this->getResourceId($resource));
                break;
            default:
                throw new ContentfulSyncException(sprintf('Unknown topic "%s"', $contentfulTopic));
        }

        return new Response();
    }

    /**
     * Parse the specified request content into a Contentful SDK resource
     *
     * @param string $resourceContent
     *
     * @return ResourceInterface|ResourceArray
     */
    private function parseRequestContent(string $resourceContent)
    {
        /** @var Client $client */
        $client = $this->contentfulService->getClient();

        return $client->parseJson($resourceContent);
    }

    /**
     * @param Entry $entry
     *
     * @return string
     */
    private function getEntryContentType(Entry $entry): string
    {
        return $entry->getSystemProperties()->getContentType()->getId();
    }

    /**
     * @param DeletedEntry $deletedEntry
     *
     * @return string
     */
    private function getDeletedEntryContentType(DeletedEntry $deletedEntry): string
    {
        return $deletedEntry->getSystemProperties()->getContentType()->getId();
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return string
     */
    private function getResourceId(ResourceInterface $resource): string
    {
        return $resource->getSystemProperties()->getId();
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getContentfulTopic(Request $request): string
    {
        return $request->header('X-Contentful-Topic', '');
    }
}
