<?php

namespace Digia\Lumen\ContentfulSync\Providers;

use Digia\Lumen\ContentfulSync\Console\Commands\SyncAssetsCommand;
use Digia\Lumen\ContentfulSync\Console\Commands\SyncContentsCommand;
use Digia\Lumen\ContentfulSync\Contracts\ContentfulSyncServiceContract;
use Illuminate\Support\ServiceProvider;
use Jalle19\Laravel\LostInterfaces\Providers\ServiceProvider as ServiceProviderInterface;
use Laravel\Lumen\Application;
use Nord\Lumen\Contentful\ContentfulServiceContract;

/**
 * Class AbstractContentfulSyncServiceProvider
 * @package Digia\Lumen\ContentfulSync\Providers
 */
abstract class AbstractContentfulSyncServiceProvider extends ServiceProvider implements ServiceProviderInterface
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

        // Configure how ContentfulSyncServiceContract should be built
        $this->registerContentfulSyncServiceBindings(app());

        // Configure how commands should be built
        app()->bind(SyncAssetsCommand::class, function (Application $app) {
            return new SyncAssetsCommand(config('contentfulSync.content_types'),
                $app->make(ContentfulServiceContract::class),
                $app->make(ContentfulSyncServiceContract::class));
        });

        app()->bind(SyncContentsCommand::class, function (Application $app) {
            return new SyncContentsCommand(config('contentfulSync.content_types'),
                $app->make(ContentfulServiceContract::class),
                $app->make(ContentfulSyncServiceContract::class));
        });
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }
}
