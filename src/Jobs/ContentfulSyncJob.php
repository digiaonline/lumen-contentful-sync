<?php

namespace Digia\Lumen\ContentfulSync\Jobs;

use Digia\Lumen\ContentfulSync\Traits\HandlesContentfulSync;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * The base class for all Contentful synchronization jobs, as in publishing and deleting of assets and entries.
 * This class implements the `ShouldQueue` interface which allows us to place the job in a queue that can later be
 * processed by one of the worker instances, which in turn speeds up the synchronization process tremendously.
 *
 * @package Digia\Lumen\ContentfulSync\Jobs
 */
abstract class ContentfulSyncJob implements ShouldQueue
{

    use HandlesContentfulSync;

    /**
     * Performs the actual work.
     */
    abstract public function handle();
}
