<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulPublishAssetJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulPublishAssetJob extends ContentfulSyncJob
{

    /**
     * @var string
     */
    private $assetJson;

    /**
     * @var bool
     */
    private $ignoreExisting;

    /**
     * ContentfulPublishEntryJob constructor.
     *
     * @param string $assetJson
     * @param bool   $ignoreExisting
     */
    public function __construct(string $assetJson, bool $ignoreExisting)
    {
        $this->assetJson      = $assetJson;
        $this->ignoreExisting = $ignoreExisting;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->getContentfulSyncService()->handleAssetPublished($this->assetJson, $this->ignoreExisting);
    }
}
