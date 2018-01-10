<?php

namespace Digia\Lumen\ContentfulSync\Traits;

use Digia\Lumen\ContentfulSync\Contracts\ContentfulSyncServiceContract;

/**
 * Trait SynchronizesContentfulEntities
 * @package Digia\Lumen\ContentfulSync\Traits
 */
trait HandlesContentfulSync
{

    /**
     * @return ContentfulSyncServiceContract
     */
    public function getContentfulSyncService(): ContentfulSyncServiceContract
    {
        return app(ContentfulSyncServiceContract::class);
    }
}
