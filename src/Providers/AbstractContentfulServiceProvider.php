<?php

namespace Digia\Lumen\ContentfulSync\Providers;

use Digia\Lumen\ContentfulSync\Commands\SyncAssetsCommand;
use Digia\Lumen\ContentfulSync\Commands\SyncContentsCommand;
use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Illuminate\Support\ServiceProvider;
use Jalle19\Laravel\LostInterfaces\Providers\ServiceProvider as ServiceProviderInterface;
use Laravel\Lumen\Application;
use Nord\Lumen\Contentful\ContentfulService;

/**
 * Class ContentfulServiceProvider
 * @package Digia\Lumen\ContentfulSync\Providers
 */
abstract class AbstractContentfulServiceProvider extends ServiceProvider implements ServiceProviderInterface
{

    /**
     * @param Application $app
     */
    abstract protected function registerContentfulSyncServiceBindings(Application $app);

    /**
     * @inheritDoc
     */
    public function register()
    {
        app()->configure('contentfulSync');

        // Configure how AbstractContentfulSyncService should be built
        $this->registerContentfulSyncServiceBindings(app());

        // Configure how commands should be built
        app()->bind(SyncAssetsCommand::class, function (Application $app) {
            return new SyncAssetsCommand(config('contentfulSync.content_types'), $app->make(ContentfulService::class),
                $app->make(AbstractContentfulSyncService::class));
        });

        app()->bind(SyncContentsCommand::class, function (Application $app) {
            return new SyncContentsCommand(config('contentfulSync.content_types'), $app->make(ContentfulService::class),
                $app->make(AbstractContentfulSyncService::class));
        });
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }
}
