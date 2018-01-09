<?php

namespace Digia\Lumen\ContentfulSync\Http\Middleware;

use Illuminate\Http\Request;
use Nord\Lumen\NewRelic\NewRelicMiddleware as BaseNewRelicMiddleware;

/**
 * Class NewRelicMiddleware
 * @package Digia\Lumen\ContentfulSync\Http\Middleware
 */
class NewRelicMiddleware extends BaseNewRelicMiddleware
{

    /**
     * Attribute parameters that the controller supplies in the request
     */
    public const ATTRIBUTE_TOPIC        = __CLASS__ . '_topic';
    public const ATTRIBUTE_CONTENT_TYPE = __CLASS__ . '_content';

    /**
     * @inheritDoc
     */
    public function getTransactionName(Request $request): string
    {
        // Extract some attributes from the request
        $topic       = $request->attributes->get(self::ATTRIBUTE_TOPIC);
        $contentType = $request->attributes->get(self::ATTRIBUTE_CONTENT_TYPE);

        if ($contentType !== null) {
            return "{$topic}@{$contentType}";
        }

        return "{$topic}";
    }

}
