<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulArchiveEntryJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulArchiveEntryJob extends ContentfulSyncJob
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
     * ContentfulArchiveEntryJob constructor.
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
        $this->getContentfulSyncService()->handleEntryArchived($this->contentType, $this->entryJson);
    }
}
