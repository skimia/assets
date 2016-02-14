---
title: Getting started
---

# Getting started with Skimia/Assets

Welcome! This guide will help you get started with using Skimia/Assets in your project.

## Installation

Install Skimia\\Assets with [Composer](http://getcomposer.org/doc/00-intro.md):

```json
composer require skimia/assets
```

Open `config/app.php` and register the required service provider above your application providers.

```php
'providers' => [
    Skimia\Assets\AssetsServiceProvider::class
]
```

### Automatic assets collection génération

if you wanth to regenerate automatically the collections if you install a package by composer/npm or bower you can add the artisan command `asset:dump-collections` to after install / update for all deps manager.

#### for Composer

```json
{
    "scripts": {
        "post-install-cmd":[
            "php artisan asset:dump-collections --silent"
        ],
        "post-update-cmd": [
            "php artisan asset:dump-collections"
        ],
    }
}
```

> checks the `--silent` option for dont prompt for auto add the newly added collections to default groups (multitenant feature of stolz), for more information go to [commands doc chapiter](commands.html)

## Configuration

Skimia/Assets uses [Stolz/Api](https://github.com/Stolz/Assets), but just for manager class, you must not configure the stolz package if you don't want conflict beetween two packages.

`Stolz\Assets` is preinstalled & preconfigured by the skimia package 

> skimia/assets & stolz/assets uses the same configuration file

```
php artisan vendor:publish --provider="Skimia\Assets\AssetsServiceProvider"
```

##### continue to [Configuration chapiter](configuration.html)