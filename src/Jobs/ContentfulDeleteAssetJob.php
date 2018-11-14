<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

/**
 * Class ContentfulDeleteAssetJob
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
final class ContentfulDeleteAssetJob extends ContentfulSyncJob
{

    /**
     * @var string
     */
    private $assetId;

    /**
     * ContentfulDeleteAssetJob constructor.
     *
     * @param string $assetId
     */
    public function __construct(string $assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * @inheritdoc
     */
    public function handle(): void
    {
        $this->getContentfulSyncService()->handleAssetDeleted($this->assetId);
    }
}
