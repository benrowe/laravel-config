# Laravel Config

[![Build Status](https://travis-ci.org/benrowe/laravel-config.svg?branch=feature%2F1-setup-build-environment&format=flat-square)](https://travis-ci.org/benrowe/laravel-config)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/benrowe/laravel-config/badges/quality-score.png?b=master&format=flat-square)](https://scrutinizer-ci.com/g/benrowe/laravel-config/?branch=master)
[![Total Downloads](https://poser.pugx.org/benrowe/laravel-config/d/total.svg?format=flat-square)](https://packagist.org/packages/benrowe/laravel-config)
[![Latest Stable Version](https://poser.pugx.org/benrowe/laravel-config/v/stable.svg?format=flat-square)](https://packagist.org/packages/benrowe/laravel-config)
[![Latest Unstable Version](https://poser.pugx.org/benrowe/laravel-config/v/unstable.svg?format=flat-square)](https://packagist.org/packages/benrowe/laravel-config)
[![License](https://poser.pugx.org/benrowe/laravel-config/license.svg?format=flat-square)](https://packagist.org/packages/benrowe/laravel-config)

A Laravel __runtime__ configuration handler that supports hierarchical configuration,
however when stored, the data is flattened to basic key/value pairs (this allows for more possible storage options)

```php
<?php

use Benrowe\Laravel\Config\Config;

$config = new Config([
    'foo.bar[0]' => 'Hello',
    'foo.bar[1]' => 'World',
    'foo.key'    => 'Value'
]);

$foo = $config->get('foo'); // => ['bar' => ['Hello', 'World'], 'key' => 'Value']
$bar = $config->get('foo.bar'); // => ['Hello', 'World']

```

## Features

- Ability to store the configuration data into any persistent data store (file, db, etc).
  - Provided Storage Adapters include Db, File, Redis.
- Dot notation syntax for configuration hierarchy.
- Values can be simple strings, or arrays of strings.
- Modifier support. Modifers can be registered to manipulate the value at runtime. (aka storing json, datetimes, booleans, etc).
- [ServiceProvider][2] included to configure the component based on the supplied configuration
- Facade support


## Installation

Install the library using [composer][1]. Add the following to your `composer.json`:

```json
{
    "require": {
        "benrowe/laravel-config": "0.1.*"
    }
}
```

Now run the `install` command.

```sh
$ composer.phar install
```

This will provide access to the component via PSR-4. To configure the package as a laravel service, the service provider must be registered with the provided ServiceProvider.

## Configuring Laravel

Once the package has been installed via composer, you need to register the service provider. To do this, edit your `config/app.php` and add a new option under the `providers` key.

```php
Benrowe\Laravel\Config\ServiceProvider::class
```

Additionally you can register the provided facade.

```php
'RuntimeConfig' => Benrowe\Laravel\Config\Facdes\Config::class,
```
With the service provider registered, this will give access to the config component, however it is _not_ configured to persist any changes you make to the config. To do this, you need to publish the provided config file.

```
php artisan vendor:publish --provider="Benrowe\Laravel\Config\ServiceProvider" --tag="config"
```

This will publish a `config.php` file into your `config` directory. At this point you will need to edit the file and setup how you want to persist your configuration.

Additionally if you plan to store your configuration in a database (such as mysql, etc) you will need to publish the migration which stores the config schema

```
php artisan vendor:publish --provider="Benrowe\Laravel\Config\ServiceProvider" --tag="migrations"
```

[1]: http://getcomposer.org/
[2]: https://laravel.com/docs/master/providers
