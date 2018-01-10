<?php

namespace Digia\Lumen\ContentfulSync\Services;

use Digia\Lumen\ContentfulSync\Contracts\ContentfulSyncServiceContract;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishEntryJob;
use Illuminate\Contracts\Queue\Queue;

/**
 * Class ContentfulSyncServiceContract
 * @package Digia\Lumen\ContentfulSync\Services
 */
abstract class AbstractContentfulSyncService implements ContentfulSyncServiceContract
{

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @inheritdoc
     */
    abstract public function handleEntryPublished(string $contentType, string $entryJson, bool $ignoreExisting);

    /**
     * @inheritdoc
     */
    abstract public function handleEntryDeleted(string $contentType, string $entryId);

    /**
     * @inheritdoc
     */
    abstract public function handleAssetPublished(string $assetJson, bool $ignoreExisting);

    /**
     * @inheritdoc
     */
    abstract public function handleAssetDeleted(string $assetId);

    /**
     * ContentfulSyncService constructor.
     *
     * @param Queue $queue
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @inheritdoc
     */
    public function publishEntry(string $contentType, string $entryJson, bool $ignoreExisting = false): void
    {
        $this->queue->push(new ContentfulPublishEntryJob($contentType, $entryJson, $ignoreExisting));
    }

    /**
     * @inheritdoc
     */
    public function deleteEntry(string $contentType, string $entryId): void
    {
        $this->queue->push(new ContentfulDeleteEntryJob($contentType, $entryId));
    }

    /**
     * @inheritdoc
     */
    public function publishAsset(string $assetJson, bool $ignoreExisting = false): void
    {
        $this->queue->push(new ContentfulPublishAssetJob($assetJson, $ignoreExisting));
    }

    /**
     * @inheritdoc
     */
    public function deleteAsset(string $assetId): void
    {
        $this->queue->push(new ContentfulDeleteAssetJob($assetId));
    }
}
