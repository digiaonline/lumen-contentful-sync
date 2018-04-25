<?php

namespace Digia\Lumen\ContentfulSync\Http\Controllers;

use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\SystemProperties;
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

        // Parse the payload into a Contentful SDK resource object
        $resource = $this->contentfulService->getClient()->parseJson($requestContent);

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
                $this->contentfulSyncService->deleteAsset($this->getResourceId($resource));
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_PUBLISH:
                $contentType = $this->getResourceContentType($resource);

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->contentfulSyncService->publishEntry($contentType, $requestContent);
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_UNPUBLISH:
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_DELETE:
                $contentType = $this->getResourceContentType($resource);

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->contentfulSyncService->deleteEntry($contentType, $this->getResourceId($resource));
                break;
            default:
                throw new ContentfulSyncException(sprintf('Unknown topic "%s"', $contentfulTopic));
        }

        return new Response();
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getResourceContentType(ResourceInterface $resource): string
    {
        /** @var SystemProperties $systemProperties */
        $systemProperties = $resource->getSystemProperties();
        $contentType      = $systemProperties->getContentType();

        if ($contentType === null) {
            throw new \InvalidArgumentException('Resource does not have a content type');
        }

        return $contentType->getId();
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
