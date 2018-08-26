Config loader for PHP
=====================

An agnostics configuration loader with support built-in loader for YAML, TOML and JSON.

[![Build Status](https://travis-ci.org/yosymfony/config-loader.png?branch=master)](https://travis-ci.org/yosymfony/Config-loader)
[![Latest Stable Version](https://poser.pugx.org/yosymfony/config-loader/v/stable.png)](https://packagist.org/packages/yosymfony/config-loader)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yosymfony/Config-loader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yosymfony/Config-loader/?branch=master)

Installation
------------

**Requires PHP >= 7.2.**

Use [Composer](http://getcomposer.org/) to install this package:

```bash
composer require yosymfony/config-loader
```

Usage
-----

### Initialization
The class `ConfigLoader` is in charge of load your configuration resources. It expects a list of configuration
loaders in its constructor. You can pass to it only those loader you need:

```php
use Yosymfony\ConfigLoader\FileLocator;
use Yosymfony\ConfigLoader\ConfigLoader;

// The file locator uses an array of pre-defined paths to find files
$locator = new FileLocator(['/path1', '/path2']);

// Set up the ConfigLoader to work with YAML and TOML configuration files:
$config = new ConfigLoader([
    new YamlLoader($locator),
    new TomlLoader($locator),
]);
```

#### Loader available
##### Yaml loader
  **Requires**: Symfony YAML component:
  ```bash
  composer require symfony/yaml
  ```

  Initialization:
  ```php
  $config = new ConfigLoader([
    new YamlLoader($locator),
  ]);
  ```

##### Toml loader
  **Requires**: Symfony YAML component:
  ```bash
  composer require yosymfony/toml
  ```

  Initialization:
  ```php
  $config = new ConfigLoader([
    new TomlLoader($locator),
  ]);
  ```
##### Json loader
  Initialization:
  ```php
  $config = new ConfigLoader([
    new JsonLoader($locator),
  ]);
  ```
### Loading configuration files:

```php
// Search this file in "path1" and "path2":
$config->load('user.yml');
// or load a file using its absolute path:
$config->load('/var/config/user1.yml');
```

#### *.dist* files

This library has support for `.dist` files. The filename is resolved following the next hierarchy:

1. filename.ext
2. filename.ext.dist (if `filename.ext` does not exist)

### Loading inline configuration:

To parse inline configurations you just need to set the configuration text as first argument instead of the filename 
and set the syntax type as second argument:

```php    
$repository = $config->load('server: "your-name.com"', YamlLoader::TYPE);
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
// Returns the value associeted with key "name" or the default value in case not found
$repository->get('name', 'default');

// Do the same that the previous sentence but using array notation
$repository['name'];
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

Creating a blank repository is too easy. You just need to create a instance of
a `Repository` class:

```php
use Yosymfony\Config-loader\Repository;

//...

$repository = new Repository([
  'name' => 'Yo! Symfony',
]);

$repository->set('server', 'your-name.com');
```

Unit tests
----------

You can run the unit tests with the following command:

```bash
$ cd toml
$ composer test
```

License
-------

This library is open-sourced software licensed under the
[MIT license](http://opensource.org/licenses/MIT).
