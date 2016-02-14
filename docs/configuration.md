# Configuration

<div class="alert alert-info">
  The <strong>Stolz/Assets</strong> configuration is under the groups key in this config file 
</div>


## `assets.collections_dir` 
<blockquote>
  <p>Directory contening copied assets in the public directory</p>
  <p>directory : project_root/public/{assets.collections_dir}/{asset_file_alias}/</p>
  <small>default set to <cite title="Source Title">collections</cite></small>
</blockquote>


## `assets.directories` 
<blockquote>
  <p>List of directories contening packages with .assets.json files</p>
  <p>you can set options by directory, see bellow</p>
  <small>default set to</small>
  <small><cite title="Source Title">project_root/vendor</cite></small>
  <small><cite title="Source Title">project_root/node_modules</cite></small>
  <small><cite title="Source Title">project_root/bower_components</cite></small>
</blockquote>
```php
 return [
    'directories'=>[
        base_path('vendor') => [
            'max_depth'=>3
        ],
        base_path('node_modules'),
        base_path('bower_components'),
    ],
 ];
```
> you can set the directory path in key for provide options in the value array or setting simply the path in value with no options

### `assets.directories.{options}`

#### `max_depth`
<blockquote>
  <p>for performance reason you can limit the depth in scanned directories</p>
  <small>default is set by the `assets.max_depth` options & her default value is <cite title="Source Title">3</cite></small>
</blockquote>


## `assets.max_depth` 
<blockquote>
  <p>Max depth in scanned directories</p>
  <p>for performance reason you can limit the depth in scanned directories, it can be override by directories options</p>
  <small>default set to <cite title="Source Title">3</cite></small>
  <small>with 3, the max is  <cite title="Source Title">{directory_path}<strong>/first_sub</strong>:{vendor}<strong>/second_sub</strong>:{package}<strong>/third_sub</strong>:{assets_dir}/<strong>.assets.json</strong></cite></small>
</blockquote>


## `assets.file_prediction` 
<blockquote>
  <p>this option is unused for now & it must is set to false, if not false the package throw an error</p>
</blockquote>

## `assets.copy_mode` 
<blockquote>
  <p> This option controls the copy mode of packages assets for make it accesible from public dir.</p>
  <p>2 options (in lowercase)</p>
  <ul>
    <li><strong>copy</strong> : simply copy the selected directories by the asset file in the public dir, if you modify one of the inclued files you must regenerate with command `php artisan asset:dump-collections`</li>
    <li><strong>symlink</strong> : symlink the dirs for avoid to run the command for all modification but only if the assets file is modified</li>
  </ul>
  <small>default set to <cite title="Source Title">copy</cite></small>
</blockquote>

## `assets.generate_when_local` 
<blockquote>
  <p>Autoregenerate & Copy assets collections for all request on local env</p>
  <p>Warning this config if set to true, drastically slow application performance</p>
  <small>default set to <cite title="Source Title">false</cite></small>
</blockquote>