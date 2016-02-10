<?php

namespace Skimia\Assets;

use Illuminate\Support\ServiceProvider;
use Skimia\Assets\Console\Commands\GenerateCollectionsCommand;
use Skimia\Assets\Scanner\ScannerServiceProvider;
use Skimia\Assets\Providers\StolzAssetsServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->register(StolzAssetsServiceProvider::class);
        $this->app->register(ScannerServiceProvider::class);

        // Register the Artisan command binding
        $this->app->bind('skimia.assets.command.generate', function ($app) {
            return new GenerateCollectionsCommand();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('assets.php'),
        ]);

        // Merge user's configuration with the default package config file
        $this->mergeConfigFrom(__DIR__.'/config.php', 'assets');

        $this->commands('skimia.assets.command.generate');

        if ($this->app['config']->get('assets.file_prediction', false) === true) {
            throw new \InvalidArgumentException('the configuration value `assets.file_prediction` must be false this functionality is not implemented');
        }
    }
}
