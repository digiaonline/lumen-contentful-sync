<?php

namespace Digia\Lumen\ContentfulSync\Console\Commands;

use Contentful\Delivery\Client;
use Contentful\Delivery\Query;
use Digia\Lumen\ContentfulSync\Contracts\ContentfulSyncServiceContract;
use Illuminate\Console\Command;
use Jalle19\Laravel\LostInterfaces\Console\Command as CommandInterface;
use Nord\Lumen\Contentful\ContentfulServiceContract;

/**
 * Class AbstractSyncCommand
 * @package Digia\Lumen\ContentfulSync\Console\Commands
 */
abstract class AbstractSyncCommand extends Command implements CommandInterface
{

    /**
     * @var boolean
     */
    protected $ignoreExisting;

    /**
     * @var ContentfulServiceContract
     */
    protected $contentfulService;

    /**
     * @var ContentfulSyncServiceContract
     */
    protected $contentfulSyncService;

    /**
     * @var array
     */
    protected $contentTypes;

    /**
     * @var int
     */
    protected $numSynchronized;

    /**
     * @var int
     */
    protected $skip;

    /**
     * @param null|string $contentType the content type, or null if not applicable
     *
     * @return Query the query used to fetch all entries/assets
     */
    abstract protected function getQuery(?string $contentType = null): Query;

    /**
     * @param null|string $contentType the content type, or null if not applicable
     *
     * @return Query the query used to get the total number of items
     */
    abstract protected function getTotalQuery(?string $contentType = null): Query;

    /**
     * AbstractSyncCommand constructor.
     *
     * @param array                         $contentTypes
     * @param ContentfulServiceContract     $contentfulService
     * @param ContentfulSyncServiceContract $contentfulSyncService
     */
    public function __construct(
        array $contentTypes,
        ContentfulServiceContract $contentfulService,
        ContentfulSyncServiceContract $contentfulSyncService
    ) {
        parent::__construct();

        $this->contentTypes          = $contentTypes;
        $this->contentfulService     = $contentfulService;
        $this->contentfulSyncService = $contentfulSyncService;
    }

    /**
     * @inheritdoc
     *
     * @throws \Throwable
     */
    public function handle()
    {
        // Parse options and reset counters
        $this->ignoreExisting = (bool)$this->option('ignoreExisting');

        $this->resetCounters();
    }

    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return $this->contentfulService->getClient();
    }

    /**
     * Resets the "skip" and "numSynchronized" counters
     */
    protected function resetCounters(): void
    {
        $this->numSynchronized = 0;
        $this->skip            = 0;
    }
}
