<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulUnarchiveEntryJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulUnarchiveEntryJob extends ContentfulSyncJob
{

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $entryJson;

    /**
     * ContentfulUnarchiveEntryJob constructor.
     *
     * @param string $contentType
     * @param string $entryJson
     */
    public function __construct(string $contentType, string $entryJson)
    {
        $this->contentType = $contentType;
        $this->entryJson   = $entryJson;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->getContentfulSyncService()->handleEntryUnarchived($this->contentType, $this->entryJson);
    }
}
