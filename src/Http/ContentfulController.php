<?php

namespace Digia\Lumen\ContentfulSync\Http;

use Digia\Lumen\ContentfulSync\Exceptions\ContentfulSyncException;
use Digia\Lumen\ContentfulSync\Http\Middleware\NewRelicMiddleware;
use Digia\Lumen\ContentfulSync\Traits\HandlesContentfulSync;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use Nord\Lumen\Contentful\HandlesContentful;

/**
 * Handles incoming webhooks from Contentful
 *
 * @package Digia\Lumen\ContentfulSync\Controllers
 */
class ContentfulController extends Controller
{

    use HandlesContentful;
    use HandlesContentfulSync;

    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_PUBLISH   = 'ContentManagement.Asset.publish';
    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_UNPUBLISH = 'ContentManagement.Asset.unpublish';
    protected const TOPIC_CONTENT_MANAGEMENT_ASSET_DELETE    = 'ContentManagement.Asset.delete';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_PUBLISH   = 'ContentManagement.Entry.publish';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_UNPUBLISH = 'ContentManagement.Entry.unpublish';
    protected const TOPIC_CONTENT_MANAGEMENT_ENTRY_DELETE    = 'ContentManagement.Entry.delete';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws ContentfulSyncException if the webhook cannot be handled
     */
    public function handleIncomingWebhook(Request $request): Response
    {
        // Parse the payload into a Contentful SDK resource object
        $resource = $this->getContentfulService()->getClient()->reviveJson($request->getContent());

        // Instrument the request so the middleware can do its job
        $contentfulTopic = $this->getContentfulTopic($request);
        $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_TOPIC, $contentfulTopic);

        // Handle different topics differently
        switch ($contentfulTopic) {
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_PUBLISH:
                $this->getContentfulSyncService()->publishAsset($request->getContent());
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_UNPUBLISH:
            case self::TOPIC_CONTENT_MANAGEMENT_ASSET_DELETE:
                $this->getContentfulSyncService()->deleteAsset($resource->getId());
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_PUBLISH:
                $contentType = $resource->getContentType()->getId();

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->getContentfulSyncService()->publishEntry($contentType, $request->getContent());
                break;
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_UNPUBLISH:
            case self::TOPIC_CONTENT_MANAGEMENT_ENTRY_DELETE:
                $contentType = $resource->getContentType()->getId();

                $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, $contentType);

                $this->getContentfulSyncService()->deleteEntry($contentType, $resource->getId());
                break;
            default:
                throw new ContentfulSyncException(sprintf('Unknown topic "%s"', $contentfulTopic));
        }

        return new Response();
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
