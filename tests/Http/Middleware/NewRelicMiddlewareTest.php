<?php

namespace Digia\Lumen\ContentfulSync\Tests\Http\Middleware;

use Digia\Lumen\ContentfulSync\Http\Middleware\NewRelicMiddleware;
use Digia\Lumen\ContentfulSync\Tests\TestCase;
use Illuminate\Http\Request;
use Intouch\Newrelic\Newrelic;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class NewRelicMiddlewareTest
 * @package Digia\Lumen\ContentfulSync\Tests\Http\Middleware
 */
class NewRelicMiddlewareTest extends TestCase
{

    /**
     * Tests that the transaction name is correctly determined
     */
    public function testGetTransactionName()
    {
        $request = new Request();

        /** @var Newrelic|MockObject $newrelic */
        $newrelic = $this->getMockBuilder(Newrelic::class)
                         ->getMock();

        /** @var NewRelicMiddleware $middleware */
        $middleware = new NewRelicMiddleware($newrelic);

        // Test without a content type
        $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_TOPIC, 'Contentful.Asset.publish');
        $this->assertEquals('Contentful.Asset.publish', $middleware->getTransactionName($request));

        // Test with a content type
        $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_TOPIC, 'Contentful.Entry.publish');
        $request->attributes->set(NewRelicMiddleware::ATTRIBUTE_CONTENT_TYPE, 'article');
        $this->assertEquals('Contentful.Entry.publish@article', $middleware->getTransactionName($request));
    }

}
