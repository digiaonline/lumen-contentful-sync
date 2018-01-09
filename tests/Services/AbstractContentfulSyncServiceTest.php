<?php

namespace Digia\Lumen\ContentfulSync\Tests\Services;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulSyncJob;
use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Digia\Lumen\ContentfulSync\Tests\TestCase;
use Illuminate\Contracts\Queue\Queue;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractContentfulSyncServiceTest
 * @package Digia\Lumen\ContentfulSync\Tests\Services
 */
class AbstractContentfulSyncServiceTest extends TestCase
{

    /**
     * Tests that the publish entry job is pushed to the queue.
     */
    public function testPublishEntry()
    {
        $service = $this->getMockedService($this->getMockedQueue(new ContentfulPublishEntryJob('contentType',
            '{}',
            true)));
        $service->publishEntry('contentType', '{}', true);
    }

    /**
     * Tests that the delete entry job is pushed to the queue
     */
    public function testDeleteEntry()
    {
        $service = $this->getMockedService($this->getMockedQueue(new ContentfulDeleteEntryJob('contentType',
            'some ID')));
        $service->deleteEntry('contentType', 'some ID');
    }

    /**
     * Tests that the publish asset job is pushed to the queue.
     */
    public function testPublishAsset()
    {
        $service = $this->getMockedService($this->getMockedQueue(new ContentfulPublishAssetJob('{}', false)));
        $service->publishAsset('{}');
    }

    /**
     * Tests that the delete asset job is pushed to the queue
     */
    public function testDeleteAsset()
    {
        $service = $this->getMockedService($this->getMockedQueue(new ContentfulDeleteAssetJob('some ID')));
        $service->deleteAsset('some ID');
    }

    /**
     * @param ContentfulSyncJob $expectedJob
     *
     * @return Queue|MockObject
     */
    private function getMockedQueue(ContentfulSyncJob $expectedJob)
    {
        $queue = $this->getMockBuilder(Queue::class)
                      ->setMethods(['push'])
                      ->getMockForAbstractClass();

        $queue->expects($this->once())
              ->method('push')
              ->with($expectedJob);

        return $queue;
    }

    /**
     * @param Queue $queue
     *
     * @return AbstractContentfulSyncService|MockObject
     */
    private function getMockedService(Queue $queue)
    {
        $service = $this->getMockBuilder(AbstractContentfulSyncService::class)
                        ->setConstructorArgs([$queue])
                        ->getMockForAbstractClass();

        return $service;
    }
}
