<?php

namespace Digia\Lumen\ContentfulSync\Tests\Jobs;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishAssetJob;

class ContentfulPublishAssetJobTest extends ContentfulSyncJobTestCase
{

    /**
     * Tests that the job is carried out successfully.
     */
    public function testHandle()
    {
        $this->mockSyncServiceMethod('handleAssetPublished');

        (new ContentfulPublishAssetJob('{}', false))->handle();
    }
}
