<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulDeleteEntryJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulDeleteEntryJob extends ContentfulSyncJob
{

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $entryId;

    /**
     * ContentfulDeleteEntryJob constructor.
     *
     * @param string $contentType
     * @param string $entryId
     */
    public function __construct(string $contentType, string $entryId)
    {
        $this->contentType = $contentType;
        $this->entryId     = $entryId;
    }

    /**
     * @inheritdoc
     */
    public function handle(): void
    {
        $this->getContentfulSyncService()->handleEntryDeleted($this->contentType, $this->entryId);
    }
}
