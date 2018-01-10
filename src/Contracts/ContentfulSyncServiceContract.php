<?php

namespace Digia\Lumen\ContentfulSync\Contracts;

/**
 * Interface ContentfulSyncServiceContract
 * @package Digia\Lumen\ContentfulSync\Contracts
 */
interface ContentfulSyncServiceContract
{

    /**
     * @param string $contentType
     * @param string $entryJson
     * @param bool   $ignoreExisting
     */
    public function handleEntryPublished(string $contentType, string $entryJson, bool $ignoreExisting);

    /**
     * @param string $contentType
     * @param string $entryId
     */
    public function handleEntryDeleted(string $contentType, string $entryId);

    /**
     * @param string $assetJson
     * @param bool   $ignoreExisting
     */
    public function handleAssetPublished(string $assetJson, bool $ignoreExisting);

    /**
     * @param string $assetId
     */
    public function handleAssetDeleted(string $assetId);

    /**
     * @param string $contentType
     * @param string $entryJson
     * @param bool   $ignoreExisting
     */
    public function publishEntry(string $contentType, string $entryJson, bool $ignoreExisting = false): void;

    /**
     * @param string $contentType
     * @param string $entryId
     */
    public function deleteEntry(string $contentType, string $entryId): void;

    /**
     * @param string $assetJson
     * @param bool   $ignoreExisting
     */
    public function publishAsset(string $assetJson, bool $ignoreExisting = false): void;

    /**
     * @param string $assetId
     */
    public function deleteAsset(string $assetId): void;
}
