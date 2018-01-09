<?php

namespace Digia\Lumen\ContentfulSync\Tests\Jobs;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteEntryJob;

class ContentfulDeleteEntryJobTest extends ContentfulSyncJobTestCase
{

    /**
     * Tests that the job is carried out successfully.
     */
    public function testHandle()
    {
        $this->mockSyncServiceMethod('handleEntryDeleted');

        (new ContentfulDeleteEntryJob('article', 'some_article_21910'))->handle();
    }
}
