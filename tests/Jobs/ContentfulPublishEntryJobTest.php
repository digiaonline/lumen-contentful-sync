<?php

namespace Digia\Lumen\ContentfulSync\Tests\Jobs;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishEntryJob;

class ContentfulPublishEntryJobTest extends ContentfulSyncJobTestCase
{

    /**
     * Tests that the job is carried out successfully.
     */
    public function testHandle()
    {
        $this->mockSyncServiceMethod('handleEntryPublished');

        (new ContentfulPublishEntryJob('article', '{}', false))->handle();
    }
}
