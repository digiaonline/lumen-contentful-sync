<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulPublishEntryJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulPublishEntryJob extends ContentfulSyncJob
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
     * @var bool
     */
    private $ignoreExisting;

    /**
     * ContentfulPublishEntryJob constructor.
     *
     * @param string $contentType
     * @param string $entryJson
     * @param bool   $ignoreExisting
     */
    public function __construct(string $contentType, string $entryJson, bool $ignoreExisting)
    {
        $this->contentType    = $contentType;
        $this->entryJson      = $entryJson;
        $this->ignoreExisting = $ignoreExisting;
    }

    /**
     * @inheritdoc
     */
    public function handle(): void
    {
        $this->getContentfulSyncService()
             ->handleEntryPublished($this->contentType, $this->entryJson, $this->ignoreExisting);
    }
}
