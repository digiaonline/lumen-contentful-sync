<?php

namespace Digia\Lumen\ContentfulSync\Tests\Jobs;

use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Digia\Lumen\ContentfulSync\Tests\TestCase;

class ContentfulSyncJobTestCase extends TestCase
{

    /**
     * @param $method
     */
    protected function mockSyncServiceMethod($method)
    {
        $mock = $this->getMockBuilder(AbstractContentfulSyncService::class)
                     ->disableOriginalConstructor()
                     ->setMethods([$method])
                     ->getMockForAbstractClass();

        $mock->expects($this->once())
             ->method($method);

        // Replace the service instance in the container with the mock.
        $this->app->instance(AbstractContentfulSyncService::class, $mock);
    }
}
