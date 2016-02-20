<?php

namespace Skimia\Assets\Console\Commands;

use Stolz\Assets\Laravel\FlushPipelineCommand as BaseCommand;

class FlushPipelineCommand extends BaseCommand
{
    /**
     * Get the pipeline directories of the groups.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    protected function getPipelineDirectories()
    {
        // Parse configured groups
        $config = $this->config->get('assets.groups', []);
        $groups = (isset($config['default'])) ? $config : ['default' => $config];
        if (! is_null($group = $this->option('group'))) {
            $groups = array_only($groups, $group);
        }
        // Parse pipeline directories of each group
        $directories = [];
        foreach ($groups as $group => $config) {
            $pipelineDir = (isset($config['pipeline_dir'])) ? $config['pipeline_dir'] : 'min';
            $publicDir = (isset($config['public_dir'])) ? $config['public_dir'] : public_path();
            $publicDir = rtrim($publicDir, DIRECTORY_SEPARATOR);
            $cssDir = (isset($config['css_dir'])) ? $config['css_dir'] : 'css';
            $directories[] = implode(DIRECTORY_SEPARATOR, [$publicDir, $cssDir, $pipelineDir]);
            $jsDir = (isset($config['js_dir'])) ? $config['js_dir'] : 'js';
            $directories[] = implode(DIRECTORY_SEPARATOR, [$publicDir, $jsDir, $pipelineDir]);
        }
        // Clean results
        $directories = array_unique($directories);
        sort($directories);

        return $directories;
    }
}
