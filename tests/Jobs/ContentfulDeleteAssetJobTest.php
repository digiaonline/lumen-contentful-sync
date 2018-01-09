<?php

namespace Digia\Lumen\ContentfulSync\Tests\Jobs;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteAssetJob;

class ContentfulDeleteAssetJobTest extends ContentfulSyncJobTestCase
{

    /**
     * Tests that the job is carried out successfully.
     */
    public function testHandle()
    {
        $this->mockSyncServiceMethod('handleAssetDeleted');

        (new ContentfulDeleteAssetJob('some_file_137826'))->handle();
    }
}
