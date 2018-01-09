<?php

namespace Digia\Lumen\ContentfulSync\Services;

use Digia\Lumen\ContentfulSync\Jobs\ContentfulArchiveEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulDeleteEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishAssetJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulPublishEntryJob;
use Digia\Lumen\ContentfulSync\Jobs\ContentfulUnarchiveEntryJob;
use Illuminate\Contracts\Queue\Queue;
use Nord\Lumen\Contentful\HandlesContentful;

/**
 * Class AbstractContentfulSyncService
 * @package Digia\Lumen\ContentfulSync\Services
 */
abstract class AbstractContentfulSyncService
{

    use HandlesContentful;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param string $contentType
     * @param string $entryJson
     * @param bool   $ignoreExisting
     */
    abstract public function handleEntryPublished(string $contentType, string $entryJson, bool $ignoreExisting);

    /**
     * @param string $contentType
     * @param string $entryId
     */
    abstract public function handleEntryDeleted(string $contentType, string $entryId);

    /**
     * @param string $contentType
     * @param string $entryJson
     */
    abstract public function handleEntryUnarchived(string $contentType, string $entryJson);

    /**
     * @param string $contentType
     * @param string $entryJson
     */
    abstract public function handleEntryArchived(string $contentType, string $entryJson);

    /**
     * @param string $assetJson
     * @param bool   $ignoreExisting
     */
    abstract public function handleAssetPublished(string $assetJson, bool $ignoreExisting);

    /**
     * @param string $assetId
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
     * @param string $contentType
     * @param string $entryJson
     * @param bool   $ignoreExisting
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function publishEntry(string $contentType, string $entryJson, bool $ignoreExisting = false): void
    {
        $this->queue->push(new ContentfulPublishEntryJob($contentType, $entryJson, $ignoreExisting));
    }

    /**
     * @param string $contentType
     * @param string $entryId
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function deleteEntry(string $contentType, string $entryId): void
    {
        $this->queue->push(new ContentfulDeleteEntryJob($contentType, $entryId));
    }

    /**
     * @param string $contentType
     * @param string $entryJson
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function archiveEntry(string $contentType, string $entryJson): void
    {
        $this->queue->push(new ContentfulArchiveEntryJob($contentType, $entryJson));
    }

    /**
     * @param string $contentType
     * @param string $entryJson
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function unarchiveEntry(string $contentType, string $entryJson): void
    {
        $this->queue->push(new ContentfulUnarchiveEntryJob($contentType, $entryJson));
    }

    /**
     * @param string $assetJson
     * @param bool   $ignoreExisting
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function publishAsset(string $assetJson, bool $ignoreExisting = false): void
    {
        $this->queue->push(new ContentfulPublishAssetJob($assetJson, $ignoreExisting));
    }

    /**
     * @param string $assetId
     *
     * @suppress PhanTypeMismatchArgument see https://github.com/laravel/framework/pull/21248
     */
    public function deleteAsset(string $assetId): void
    {
        $this->queue->push(new ContentfulDeleteAssetJob($assetId));
    }
}
