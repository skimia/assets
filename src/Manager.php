<?php

namespace Skimia\Assets;

use Stolz\Assets\Manager as ManagerBase;

class Manager extends ManagerBase
{
    protected $collections_dir = 'collections';

    /**
     * Set up configuration options.
     *
     * All the class properties except 'js' and 'css' are accepted here.
     * Also, an extra option 'autoload' may be passed containing an array of
     * assets and/or collections that will be automatically added on startup.
     *
     * @param  array   $config Configurable options.
     * @return Manager
     */
    public function config(array $config)
    {
        if (isset($config['collections_dir'])) {
            $this->collections_dir = $config['collections_dir'];
        } else {
            return parent::config($config); // @codeCoverageIgnore
        }
    }

    /**
     * Determine whether an asset is normal or from an asset collection.
     *
     * @param  string $asset
     * @return bool|array
     */
    protected function assetIsFromCollection($asset)
    {
        if (preg_match('{^([A-Za-z0-9_.-]+)\#(.*)$}', $asset, $matches)) {
            return array_slice($matches, 1, 2);
        }

        return false;
    }

    /**
     * Build link to local asset.
     *
     * Detects packages links.
     *
     * @param  string $asset
     * @param  string $dir
     * @return string the link
     */
    protected function buildLocalLink($asset, $dir)
    {
        $collection = $this->assetIsFromCollection($asset);
        if ($collection !== false) {
            return $this->collections_dir.'/'.$collection[0].'/'.ltrim($dir, '/').'/'.$collection[1];
        }

        return parent::buildLocalLink($asset, $dir);
    }

    public function getCollections()
    {
        return $this->collections;
    }
}
