<?php

namespace Digia\Lumen\ContentfulSync\Traits;

use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;

/**
 * Trait SynchronizesContentfulEntities
 * @package Digia\Lumen\ContentfulSync\Traits
 */
trait HandlesContentfulSync
{

    /**
     * @return AbstractContentfulSyncService
     */
    public function getContentfulSyncService(): AbstractContentfulSyncService
    {
        return app(AbstractContentfulSyncService::class);
    }
}
