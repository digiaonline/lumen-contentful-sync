<?php

namespace Digia\Lumen\ContentfulSync\Console\Commands;

use Contentful\Delivery\Query;
use Digia\JsonHelpers\JsonEncoder;

/**
 * Class SyncContentsCommand
 * @package Digia\Lumen\ContentfulSync\Console\Commands
 */
class SyncContentsCommand extends AbstractSyncCommand
{

    /**
     * @var string
     */
    protected $signature = 'contentful:contents:sync 
                            {contentTypes?* : The content types, e.g. "article" or "brand". You can specify multiple content types. Omit to synchronize all content types.}
                            {--batchSize=100 : The number of items to request from Contentful in one batch. Defaults to 100.} 
                            {--ignoreErrors : Whether to ignore errors when synchronizing, useful to get around circular references.}
                            {--ignoreExisting : Whether to ignore existing entries, i.e. only synchronize new entries.}';

    /**
     * @var string
     */
    protected $description = 'Synchronizes content from Contentful';

    /**
     * @var boolean
     */
    protected $ignoreErrors;

    /**
     * @inheritdoc
     */
    protected function getQuery(?string $contentType = null): Query
    {
        $query = new Query();
        $query->setSkip($this->skip);
        $query->setLimit($this->batchSize);
        $query->setContentType($contentType);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        parent::handle();

        // Parse options and arguments
        $this->ignoreErrors = (bool)$this->option('ignoreErrors');
        $contentTypes       = $this->argument('contentTypes');

        // Synchronize all content types in a particular order unless specific content types were specified
        $contentTypes = empty($contentTypes) ? $this->contentTypes : $contentTypes;

        foreach ($contentTypes as $contentType) {
            // Reset counters before each content type
            $this->resetCounters();

            $this->synchronizeContentType($contentType);
        }
    }

    /**
     * @param string $contentType
     *
     * @throws \Throwable
     */
    protected function synchronizeContentType(string $contentType): void
    {
        $this->info('Synchronizing content of type "' . $contentType . '"...');

        $this->output->progressStart($this->getClient()->getEntries($this->getTotalQuery($contentType))->getTotal());

        do {
            $entries = $this->getClient()->getEntries($this->getQuery($contentType));

            // Process the current batch
            foreach ($entries as $entry) {
                // Optionally catch exceptions, depending on whether errors should be ignored
                try {
                    $this->contentfulSyncService->publishEntry(
                        $contentType,
                        JsonEncoder::encode($entry),
                        $this->ignoreExisting
                    );

                    $this->numSynchronized++;

                    $this->output->progressAdvance();
                } catch (\Throwable $e) {
                    $this->error("  Failed to synchronize {$entry->getId()}: {$e->getMessage()}");

                    if (!$this->ignoreErrors) {
                        throw $e;
                    }
                }
            }

            // Move on to the next batch
            $this->skip += $entries->getLimit();
        } while ($this->skip < $entries->getTotal());

        $this->output->progressFinish();

        $this->info("Done, synchronized {$this->numSynchronized} entries");
    }
}
