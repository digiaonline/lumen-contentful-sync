<?php

namespace Digia\Lumen\ContentfulSync\Providers;

use Digia\Lumen\ContentfulSync\Commands\SyncAssetsCommand;
use Digia\Lumen\ContentfulSync\Commands\SyncContentsCommand;
use Digia\Lumen\ContentfulSync\Services\AbstractContentfulSyncService;
use Illuminate\Support\ServiceProvider;
use Jalle19\Laravel\LostInterfaces\Providers\ServiceProvider as ServiceProviderInterface;
use Laravel\Lumen\Application;

/**
 * Class ContentfulServiceProvider
 * @package Digia\Lumen\ContentfulSync\Providers
 */
abstract class AbstractContentfulServiceProvider extends ServiceProvider implements ServiceProviderInterface
{

    /**
     * @param Application $app
     */
    abstract protected function registerContentfulSyncService(Application $app);

    /**
     * @inheritDoc
     */
    public function register()
    {
        app()->configure('contentfulSync');

        // Configure how commands should be built
        app()->bind(SyncAssetsCommand::class, function () {
            return new SyncAssetsCommand(config('contentfulSync.content_types'));
        });

        app()->bind(SyncContentsCommand::class, function () {
            return new SyncContentsCommand(config('contentfulSync.content_types'));
        });

        // Register the service
        app()->singleton(AbstractContentfulSyncService::class, function (Application $app) {
            return $this->registerContentfulSyncService($app);
        });
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }
}
