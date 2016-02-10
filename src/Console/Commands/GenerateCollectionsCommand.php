<?php

namespace Skimia\Assets\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Config;

class GenerateCollectionsCommand extends Command implements SelfHandling
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'asset:generate-collections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate collection & copy files to public dir.';

    /**
     * Class constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct()//Config $config(See NOTE below)
    {
        parent::__construct();

        // NOTE: Dependency injection for Artisan commands constructor was not introduced until Laravel 5.1 (LST).
        // In order to keep compatibility with Laravel 5.0 we manually resolve the dependencies

        //$this->app = $app;
        //$this->config = $config;

        $this->app = app();
        $this->config = app(Config::class);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $this->comment('Regeneration of assets collections...');
        $directories = $this->getDirectories();
        if (empty($directories)) {
            $this->comment('no directories to scan, abort');

            return;
        }

        //dd($directories);
        $scanner = $this->getScanner();

        $scanner->setDirectoriesToScan($directories);

        $scanner->scan();
        $this->comment('done');
    }

    protected function getDirectories()
    {
        return $this->app['config']->get('assets.directories', []);
    }

    /**
     * @return Scanner
     */
    protected function getScanner()
    {
        return $this->app->make('skimia.assets.scanner');
    }
}
