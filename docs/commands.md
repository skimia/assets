# Artisan commands


## Regenerate & copy collections (Dump)

```json
php artisan asset:dump-collections
```

first this command regenerate all the collections, réxecute files merging, and copy the right files in the public collections dir
> this command must run on all add/update/delete `.assets.json` file & if the `assets.copy_mode` is set to `copy` on all (js/css) modification 
second if a collection is added this command ask you if you wants to add this new collection for all defined groups in the config file
thirdy if a collection is removed this command remove the collection if is required by any group

### Options

- --silent(-s) : you can use this option if you dont want to auto manage collections, this option dont check new or removed collection. 