<?php

namespace Digia\Lumen\ContentfulSync\Tests\Http\Controllers;

use Digia\Lumen\ContentfulSync\Http\Controllers\ContentfulSyncController;
use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Digia\Lumen\ContentfulSync\Tests\TestCase;
use Illuminate\Http\Request;
use Nord\Lumen\Contentful\ContentfulService;

/**
 * Class ContentfulSyncControllerTest
 * @package Digia\Lumen\ContentfulSync\Tests\Http\Controllers
 */
class ContentfulSyncControllerTest extends TestCase
{

    /**
     * @expectedException \Digia\Lumen\ContentfulSync\Exceptions\ContentfulSyncException
     */
    public function testUnknownTopic()
    {
        $request = new Request();
        $request->headers->set('X-Contentful-Topic', 'foo');

        $controller = new ContentfulSyncController($this->getMockedService(), $this->getMockedSyncService());
        $controller->handleIncomingWebhook($request);
    }

    /**
     * @return ContentfulService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockedService()
    {
        return $this->getMockBuilder(ContentfulService::class)
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    /**
     * @return AbstractContentfulSyncService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockedSyncService()
    {
        return $this->getMockBuilder(AbstractContentfulSyncService::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
    }
}
