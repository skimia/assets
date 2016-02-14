# Skimia Assets

[![Build Status](https://img.shields.io/travis/skimia/assets/master.svg?style=flat-square)](http://travis-ci.org/skimia/assets)
[![Coverage Status](https://img.shields.io/codecov/c/github/skimia/assets.svg?branch=master&style=flat-square)](https://codecov.io/github/skimia/assets?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/skimia/assets.svg?style=flat-square)](https://packagist.org/packages/skimia/assets)
[![Total Downloads](https://img.shields.io/packagist/dt/skimia/assets.svg?style=flat-square)](https://packagist.org/packages/skimia/assets)
[![License](https://img.shields.io/packagist/l/skimia/assets.svg?style=flat-square)](https://packagist.org/packages/skimia/assets)
[![StyleCI](https://styleci.io/repos/51383045/shield)](https://styleci.io/repos/51383045)

This package is based on [Stolz/Assets](https://github.com/Stolz/Assets) for managing assets but in thos package all asset must be configured in one configuration file.

exemple for add a new lib in my project
- composer or npm require package
- - composer install the dep
- go to assets configuration file & add a new collection with the correct files
- - find the documentation of the lib & extract all needed files
- - create a new collection based on this
- go to your view or the global config of groups for add the newly defined collection

with skimia/assets (inspired by meteor)

- composer or npm require package
- - composer install the dep
- - composer call the cmd for regenerate packages
- - if a newly defined collection is found the cmd prompt group by group if you wants to add this.
- done
- alternativelly it "merge" the assets if you add a "themed" asset.

it do this magic with one file in the root of the package (you can add more if you place it in multiples dirs) with all options.

this file must be written by the package author but a "replacement" api is on the launch, it provides (if not present in the package directory) the right .asset.json file according to the repository url( override for all deps manager) or the deps manager key.
## Documentation

Please refer to our [online documentation](http://skimia.github.io/assets/) for more information.

## License

This package is licensed under the [GNU GENERAL PUBLIC LICENSE](LICENSE).
