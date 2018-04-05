<?php

namespace Digia\Lumen\ContentfulSync\Console\Commands;

use Contentful\Delivery\Asset;
use Contentful\Delivery\Query;
use Contentful\ResourceArray;
use Digia\JsonHelpers\JsonEncoder;

/**
 * Class SyncAssetsCommand
 * @package Digia\Lumen\ContentfulSync\Console\Commands
 */
class SyncAssetsCommand extends AbstractSyncCommand
{

    /**
     * @var string
     */
    protected $signature = 'contentful:assets:sync
                            {--ignoreExisting : Whether to ignore existing entries, i.e. only synchronize new entries.}
                            {--batchSize=100 : The number of items to request from Contentful in one batch. Defaults to 100.}';

    /**
     * @var string
     */
    protected $description = 'Synchronizes assets/media from Contentful';

    /**
     * @inheritdoc
     */
    protected function getQuery(?string $contentType = null): Query
    {
        $query = new Query();
        $query->setSkip($this->skip);
        $query->setLimit($this->batchSize);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        parent::handle();

        $this->info('Synchronizing assets/media...');

        $this->output->progressStart($this->getClient()->getAssets($this->getTotalQuery())->getTotal());

        do {
            /** @var Asset[]|ResourceArray $assets */
            $assets = $this->getClient()->getAssets($this->getQuery());

            // Process the current batch
            foreach ($assets as $asset) {
                $this->contentfulSyncService->publishAsset(
                    JsonEncoder::encode($asset),
                    $this->ignoreExisting
                );

                $this->numSynchronized++;

                $this->output->progressAdvance();
            }

            // Move on to the next batch
            $this->skip += $assets->getLimit();
        } while ($this->skip < $assets->getTotal());

        $this->output->progressFinish();

        $this->info("Done, synchronized {$this->numSynchronized} assets");
    }
}
