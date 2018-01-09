<?php

namespace Digia\Lumen\ContentfulSync\Commands;

use Contentful\Delivery\Client;
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
        // Parse options
        $this->ignoreExisting = (bool)$this->option('ignoreExisting');
    }

    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return $this->contentfulService->getClient();
    }
}
