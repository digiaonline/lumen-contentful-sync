<?php

namespace Digia\Lumen\ContentfulSync\Commands;

use Contentful\Delivery\Client;
use Digia\Lumen\ContentfulSync\Traits\HandlesContentfulSync;
use Illuminate\Console\Command;
use Jalle19\Laravel\LostInterfaces\Console\Command as CommandInterface;
use Nord\Lumen\Contentful\HandlesContentful;

/**
 * Class AbstractSyncCommand
 * @package Digia\Lumen\ContentfulSync\Console\Commands
 */
abstract class AbstractSyncCommand extends Command implements CommandInterface
{

    use HandlesContentful;
    use HandlesContentfulSync;

    /**
     * @var boolean
     */
    protected $ignoreExisting;

    /**
     * @var array
     */
    protected $contentTypes;

    /**
     * AbstractSyncCommand constructor.
     *
     * @param array $contentTypes
     */
    public function __construct(array $contentTypes)
    {
        parent::__construct();
        
        $this->contentTypes = $contentTypes;
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
        return $this->getContentfulService()->getClient();
    }
}
