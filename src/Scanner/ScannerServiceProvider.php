<?php

namespace Skimia\Assets\Scanner;

use Illuminate\Support\ServiceProvider;

class ScannerServiceProvider extends ServiceProvider
{

    /**
     * Determines if we will auto-scan in the local environment.
     *
     * @var bool
     */
    protected $scanWhenLocal = false;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerScanner();
    }

    public function boot()
    {
        $this->loadScanned();
    }

    protected function getDirectories(){
        return $this->app['config']->get('assets.directories', []);
    }

    /**
     * Scan the events for the application.
     *
     * @return void
     */
    protected function scanDirectories()
    {
        $directories = $this->getDirectories();
        if (empty($directories)) {
            return;
        }



        //dd($directories);
        $scanner = $this->getScanner();

        $scanner->setDirectoriesToScan($directories);

        $scanner->scan();
    }

    /**
     * Load the scanned assets.
     *
     * @return void
     */
    public function loadScanned()
    {
        if ($this->app->environment('local') && $this->app['config']->get('assets.generate_when_local', false)) {
            $this->scanDirectories();
        }

        $scans = $this->getDirectories();

        if (! empty($scans) && $this->getScanner()->isScanned()) {
            $this->getScanner()->loadScanned();
        }
    }

    protected function registerScanner()
    {
        $this->app->bindShared('skimia.assets.scanner', function ($app) {
            $scanner = new Scanner($app, []);

            return $scanner;
        });
    }

    /**
     * @return Scanner
     */
    protected function getScanner()
    {
        return $this->app->make('skimia.assets.scanner');
    }



}
