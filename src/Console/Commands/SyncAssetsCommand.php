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
                            {--ignoreExisting : Whether to ignore existing entries, i.e. only synchronize new entries.}';

    /**
     * @var string
     */
    protected $description = 'Synchronizes assets/media from Contentful';

    /**
     * @inheritdoc
     */
    public function handle()
    {
        parent::handle();

        $this->info('Synchronizing assets/media...');

        $numSynchronized = 0;
        $skip            = 0;

        $this->output->progressStart($this->getTotal());

        do {
            // Build the query
            $query = new Query();
            $query->setSkip($skip);
            /** @var Asset[]|ResourceArray $assets */
            $assets = $this->getClient()->getAssets($query);

            // Process the current batch
            foreach ($assets as $asset) {
                $this->contentfulSyncService->publishAsset(
                    JsonEncoder::encode($asset),
                    $this->ignoreExisting
                );

                $numSynchronized++;

                $this->output->progressAdvance();
            }

            // Move on to the next batch
            $skip += $assets->getLimit();
        } while ($skip < $assets->getTotal());

        $this->output->progressFinish();

        $this->info("Done, synchronized {$numSynchronized} assets");
    }

    /**
     * @return int
     *
     * @throws \RangeException
     */
    private function getTotal(): int
    {
        $query = new Query();
        $query->setLimit(1);
        $assets = $this->getClient()->getAssets($query);

        return $assets->getTotal();
    }

}
