<?php
/**
 * Created by PhpStorm.
 * User: kessler
 * Date: 02/02/16
 * Time: 15:20.
 */
namespace Skimia\Assets\Scanner;

use Illuminate\Contracts\Foundation\Application;
use Skimia\Assets\Events\BeforeMergeCollectionFiles;
use Symfony\Component\Finder\Finder;
use File;

class Scanner
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $directories;

    /**
     * @var array
     */
    protected $directories_options;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function getScannedPath()
    {
        return $this->app['path.storage'].'/framework/assets.generation.scanned.php';
    }

    public function isScanned()
    {
        return $this->app['files']->exists($this->getScannedPath());
    }

    public function loadScanned()
    {
        if ($this->isScanned()) {
            require $this->getScannedPath();

            return true;
        }

        return false;
    }

    public function setDirectoriesToScan($directories)
    {
        $dirsToScan = [];
        foreach ($directories as $path => $directory) {
            if (is_string($path) && File::exists($path)) {
                $dirsToScan[] = $path;
                $this->directories_options[$path] = $directory;
            } elseif (is_string($directory) && File::exists($directory)) {
                $dirsToScan[] = $directory;
            }
        }
        $this->directories = $dirsToScan;
    }

    public function scan()
    {
        file_put_contents(
            $this->getScannedPath(), '<?php '.$this->getDefinitions()
        );
    }

    protected function getOrderedFileDefinitions()
    {
        $files_defs = [];
        foreach ($this->directories as $path) {
            if (! \File::exists($path)) {
                continue;
            }

            $finder = Finder::create()->files()->ignoreDotFiles(false)->in($path);

            if (isset($this->directories_options[$path]['max_depth'])) {
                $finder->depth($this->directories_options[$path]['max_depth']);
            } else {
                $finder->depth($this->app['config']->get('assets.max_depth', 3));
            }

            $files = $finder->name('.assets.json');
            foreach ($files as $file) {
                $content = $this->filterFile(json_decode($file->getContents(), true));
                $content['__dir'] = dirname($file->getRealpath());
                $files_defs[$content['name']] = $content;
            }
        }

        return $this->orderBydeps($files_defs);
    }

    protected function filterFile($file)
    {
        $file = array_merge(
            [
                'name' => 'must_be_defined',
                'alias' => 'directory',
            ],
            $file
        );

        return $file;
    }

    public function getDefinitions()
    {
        $output = 'function makeCollections($container){'.PHP_EOL;

        $files = $this->getOrderedFileDefinitions();

        $event = new BeforeMergeCollectionFiles($files);
        event($event);
        //$describe($files_defs['js-stac']);
        $collections = $this->mergeFiles($files);

        foreach ($collections as $name => $assets) {
            $output .= $this->buildCollection($name, $assets);
        }

        $output .= '}'.PHP_EOL;
        $output .= $this->makeGroups();

        return trim($output);
    }

    protected function getAssetsGroups()
    {
        $groups = [];
        $config = $this->app['config']->get('assets.groups', []);

        if (! isset($config['default'])) {
            return['default'];
        }

        foreach ($config as $groupName => $groupConfig) {
            $groups[] = $groupName;
        }

        return $groups;
    }

    protected function makeGroups()
    {
        $output = '';

        $groups = $this->getAssetsGroups();

        foreach ($groups as $groupName) {
            $output .= 'makeCollections(\''.$groupName.'\');'.PHP_EOL;
        }

        return $output;
    }

    protected function mergeFiles($files)
    {
        $collections = [];

        foreach ($files as $file) {
            $file = $this->updateFile($file);
            $file_collections = $file['collections'];
            $collections = array_merge($collections, $file_collections);
        }

        return $collections;
    }

    protected function updateFile($file)
    {
        $collections = $file['collections'];
        $file['__collections'] = $collections;
        foreach ($collections as $name => &$files) {
            foreach ($files as &$f) {

                //do not prefix si c'est une collection
                if (\File::exists($file['__dir'].'/'.$f)) {
                    $f = $file['alias'].'#'.$f;
                }
            }
        }

        $file['collections'] = $collections;

        $this->copyCollections($file);

        return $file;
    }

    protected function copyCollections($file)
    {
        $mode = $this->app['config']->get('assets.copy_mode', 'copy');
        $collections_dir = $this->app['config']->get('assets.collections_dir', 'collections');
        $collections_dir = public_path($collections_dir);

        if (isset($file['copy'])) {
            foreach ($file['copy'] as $directory) {
                $input = $file['__dir'].'/'.$directory;

                $output = $collections_dir.'/'.$file['alias'].'/'.$directory;

                if ($mode == 'copy') {
                    $this->copyAssets($input, $output);
                } else {
                    $this->symlinkAssets($input, $output);
                }
            }
        }
    }

    protected function copyAssets($in, $out)
    {
        $out_dir = dirname($out);
        \File::makeDirectory($out_dir, 0777, true, true);
        \File::copyDirectory($in, $out);

        return true;
    }

    protected function symlinkAssets($in, $out)
    {
        $out_dir = dirname($out);
        \File::makeDirectory($out_dir, 0777, true, true);
        symlink($in, $out);

        return true;
    }

    protected function orderBydeps($list)
    {
        $resolved = [];
        $seen = [];
        $element = [
            'name' => false,
            'require' => array_keys($list),
        ];
        $this->dep_resolve($list, $element, $resolved, $seen);

        return $resolved;
    }

    protected function dep_resolve($list, $node, &$resolved, &$unresolved)
    {
        //echo $node['name']."<br/>";
        $unresolved[] = $node;
        if (isset($node['require'])) {
            foreach ($node['require'] as $required) {
                if (! in_array($list[$required], $resolved)) {
                    if (in_array($list[$required], $unresolved)) {
                        throw new \Exception('Circular reference detected: '.$node['name'].' -> '.$list[$required]['name']);
                    }
                    $this->dep_resolve($list, $list[$required], $resolved, $unresolved);
                }
            }
        }
        if ($node['name']) {
            $resolved[] = $node;
        }
        unset($unresolved[array_search($node, $unresolved)]);
    }

    protected function buildCollection($name, $files)
    {
        return sprintf('	Assets::group($container)->registerCollection(\'%s\', %s);'.PHP_EOL,
            $name,
            var_export($files, true));
    }
}
