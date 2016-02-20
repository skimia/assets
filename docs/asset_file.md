# Assets Collections File

this file manage the list of contained assets & the order for inclusion

## fields
all field keys must be in lowercase, fields marked by a star are required

### Name*

this is the readable name of this assets collection file
(if the alias field is not provided the name field is used but is not recomanded)

### Alias*

this is the system name of this assets collection file
is used for override

### Description

provide the description of this assets collection file

### Require

for override feature this field ensure this collection is set after the required in mergin stack
is an array with values are the alias of other `.asset;json` files

### Copy*

you must define the folders that will be copied to the public folder
is an array with values are folders relative to the asset file.

### Collections*
is similar to `Stolz\Assets` collections but all files can have an alias for easly overrides, is not required but highly recomanded

contain the list of collections in key the name and in value the array listing js/css/other_collections of the collection

#### Alias asset
for alias a file you prefix the path of the file by the alias with a `@` for separate both

`"main@node_modules/angular/angular.js"` file `angular.js` with prefix `main`

#### example collection

this example define a collection named `angularjs` and before the inclusion of aliased `main` file (`node_modules/angular/angular.js`) that require the `jquery` collection

```json
{
    "angularjs": [
      "jquery",
      "main@node_modules/angular/angular.js"
    ],
}
```

## example file

```json
{
  "name": "Api Fusion JS Client",
  "alias": "fusion-client",
  "description":"The javascript Client to connect to the fusion api",
  "collections": {
    "jquery": [
      "main@vendors/jquery.js"
    ],
    "angularjs": [
      "jquery",
      "main@node_modules/angular/angular.js"
    ],
    "js-data": [
      "main@node_modules/js-data/dist/js-data.js"
    ],
    "js-data-angular": [
      "js-data",
      "main@node_modules/js-data-angular/dist/js-data-angular.js"
    ],
    "angular-local-storage": [
      "main@vendors/angular-local-storage.js"
    ],
    "fusion-client": [
      "angularjs",
      "js-data-angular",
      "angular-local-storage",
      "main_css@dist/css/apifusion.css",
      "main_js@dist/js/fusion.core.js",
      "main_templates@dist/js/fusion.core.templates.js"
    ]
  },
  "copy":[
      "vendors",
      "node_modules",
      "dist",
  ]

}
```



## Override other assets collection file

First you must define the `require` field for example if you wants override the exemple file above
```json
{
    "require" : [ "fusion-client" ]
}
```
all ovveride are done by redefine the collection in the "sub" assets collection file


### Override entire collection (replace)

if you want to totally redefine all files(js/css or collections) of this collection
for this you add a new "file" with the `@overwrite` value for example:

```json
{
    "angularjs": [
      "@overwrite",
      "jquerydep@vendor/jquery2.js",
      "angularjs@vendor/angular.js"
    ],
}
```
this replace the all collection, before have one collection and one file, now we have 2 files with differents alias

### Override one file (replace)
if you want to change one file (js/css)
simply by set a file with the same alias for example :

```json
{
    "angularjs": [
      "main@vendor/angular2.js"
    ],
}
```
this replace the base main angularjs file with the new one

### Remove one file 

if you want to remove one file (js/css)
simply by set a value of `removed` with the same alias for example :

```json
{
    "fusion-client": [
      "main_css@removed"
    ],
}
```
this remove the main css file from this collection

### Add one file 

if you want to add one file (js/css)
simply by set an alias not defined in the parent collection

```json
{
    "fusion-client": [
      "dark_css@css/dark_client.css"
    ],
}
```
this add a new file to the collection