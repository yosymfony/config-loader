Config loader for PHP
=====================

A configuration loader with support for YAML, TOML (0.4.0) and JSON.

Inspired by [Yosymfony\ConfigServiceProvider](https://github.com/yosymfony/ConfigServiceProvider).

[![Build Status](https://travis-ci.org/yosymfony/Config-loader.png?branch=master)](https://travis-ci.org/yosymfony/Config-loader)
[![Latest Stable Version](https://poser.pugx.org/yosymfony/config-loader/v/stable.png)](https://packagist.org/packages/yosymfony/config-loader)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yosymfony/Config-loader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yosymfony/Config-loader/?branch=master)

Installation
------------

**Requires PHP >= 7.1.**

Use [Composer](http://getcomposer.org/) to install this package:

```bash
composer require yosymfony/config-loader
```

Usage
-----

```php
use Yosymfony\ConfigLoader\FileLocator;
use Yosymfony\ConfigLoader\ConfigLoader;

// Set up the paths
$locator = new FileLocator(array('/path-to-your-files-1', '/path-to-your-files-2'));

// Set up the config loader you need:
$config = new ConfigLoader([
    new TomlLoader($locator),
    new YamlLoader($locator),
    new JsonLoader($locator),
]);
```

### Loading a configuration file:

```php
$config->load('user.yml');
// or load with absolute path:
$config->load('/var/config/user1.yml');
```

#### *.dist* files

This library has support for `.dist` files. The filename is resolved following the next hierarchy:

1. filename.ext
2. filename.ext.dist (if `filename.ext` does not exist)

### Loading inline configuration:

```php    
$repository = $config->load('server: "mail.yourname.com"', Config::TYPE_YAML);
// or
$repository = $config->load('server = "mail.yourname.com"', Config::TYPE_TOML);
```

### Importing files

This library has support for importing files.
The example below shows a YAML file importing three files:

```yaml
---
imports:
  - config-imported.yml
  - config-imported.toml
  - config-imported.json
```

Similar example using JSON syntax:

```json
{
  "imports": [
    "config.json"
  ]
}
```

An example using TOML syntax:

```
imports = [
    "config.toml"
]
```

Repository
----------

A configuration file is loaded into a repository. A repository is a wrapper
that implements the array access interface and exposes methods for working
with configuration values

```php
$repository->get('name', 'noname'); // If 'name' doesn't exists it returns 'noname'
$repository['name']; // Get the element in 'name' key
```

### Operations

#### Unions

You can performs the union of a repository A with another B into C as result:

```php
$resultC = $repositoryA->union($repositoryB);
```

The values of `$repositoryB` have less priority than values in `$repositoryA`.

#### Intersections

You can performs the intersection of a repository A with another B into C as result:
```php
$resultC = $repositoryA->intersection($repositoryB);
```

The values of `$repositoryB` have less priority than values `$repositoryA`.

### Creating a blank repository

Creating a blank repository is too easy. You only need to create a instance of
a `Repository` class:

```php
use Yosymfony\Config-loader\Repository;

//...

$repository = new Repository();
$repository->set('key1', 'value1');
// or
$repository['key1'] = 'value1';
```

Unit tests
----------

You can run the unit tests with the following command:

```bash
$ cd toml
$ composer test
```

## License

This library is open-sourced software licensed under the
[MIT license](http://opensource.org/licenses/MIT).
