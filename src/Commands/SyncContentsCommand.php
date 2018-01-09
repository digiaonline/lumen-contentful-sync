<?php

namespace Digia\Lumen\ContentfulSync\Commands;

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
    public function handle()
    {
        parent::handle();

        // Parse options and arguments
        $this->ignoreErrors = (bool)$this->option('ignoreErrors');
        $contentType        = $this->argument('contentType');

        // Synchronize all content types in a particular order unless a specific content type was specified
        $contentTypes = $contentType !== null ? [$contentType] : $this->contentTypes;

        foreach ($contentTypes as $contentType) {
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

        $numSynchronized = 0;
        $skip            = 0;

        $this->output->progressStart($this->getTotal($contentType));

        do {
            // Build the query
            $query = new Query();
            $query->setSkip($skip);
            $query->setContentType($contentType);
            $entries = $this->getClient()->getEntries($query);

            // Process the current batch
            foreach ($entries as $entry) {
                // Optionally catch exceptions, depending on whether errors should be ignored
                try {
                    $this->getContentfulSyncService()->publishEntry(
                        $contentType,
                        JsonEncoder::encode($entry),
                        $this->ignoreExisting
                    );

                    $numSynchronized++;

                    $this->output->progressAdvance();
                } catch (\Throwable $e) {
                    $this->error("  Failed to synchronize {$entry->getId()}: {$e->getMessage()}");

                    if (!$this->ignoreErrors) {
                        throw $e;
                    }
                }
            }

            // Move on to the next batch
            $skip += $entries->getLimit();
        } while ($skip < $entries->getTotal());

        $this->output->progressFinish();

        $this->info("Done, synchronized {$numSynchronized} entries");
    }

    /**
     * @param string $contentType
     *
     * @return int
     *
     * @throws \RangeException
     */
    private function getTotal(string $contentType): int
    {
        $query = new Query();
        $query->setLimit(1);
        $query->setContentType($contentType);
        $entries = $this->getClient()->getEntries($query);

        return $entries->getTotal();
    }

}
