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
                            {contentType? : The content type, e.g. "article" or "brand". Omit to synchronize all content types.} 
                            {--ignoreErrors : Whether to ignore errors when synchronizing, useful to get around circular references.}
                            {--ignoreExisting : Whether to ignore existing entries, i.e. only synchronize new entries.}';

    /**
     * @var string
     */
    protected $description = 'Synchronizes content from Contentful';

    /**
     * @var boolean
     */
    private $ignoreErrors;

    /**
     * @inheritdoc
     */
    protected function getQuery(?string $contentType = null): Query
    {
        $query = new Query();
        $query->setSkip($this->skip);
        $query->setContentType($contentType);

        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalQuery(?string $contentType = null): Query
    {
        $query = new Query();
        $query->setLimit(1);
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
        $contentType        = $this->argument('contentType');

        // Synchronize all content types in a particular order unless a specific content type was specified
        $contentTypes = $contentType !== null ? [$contentType] : $this->contentTypes;

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
    private function synchronizeContentType(string $contentType)
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
