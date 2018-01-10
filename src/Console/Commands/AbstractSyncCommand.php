<?php

namespace Digia\Lumen\ContentfulSync\Console\Commands;

use Contentful\Delivery\Client;
use Contentful\Delivery\Query;
use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Illuminate\Console\Command;
use Jalle19\Laravel\LostInterfaces\Console\Command as CommandInterface;
use Nord\Lumen\Contentful\ContentfulService;

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
     * @var ContentfulService
     */
    protected $contentfulService;

    /**
     * @var AbstractContentfulSyncService
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
     * @param ContentfulService             $contentfulService
     * @param AbstractContentfulSyncService $contentfulSyncService
     */
    public function __construct(
        array $contentTypes,
        ContentfulService $contentfulService,
        AbstractContentfulSyncService $contentfulSyncService
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
        // Parse options and set defaults
        $this->ignoreExisting  = (bool)$this->option('ignoreExisting');
        $this->numSynchronized = 0;
        $this->skip            = 0;
    }

    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return $this->contentfulService->getClient();
    }
}
