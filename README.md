# Laravel Config

[![Build Status](https://travis-ci.org/benrowe/laravel-config.svg?branch=feature%2F1-setup-build-environment)](https://travis-ci.org/benrowe/laravel-config)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/benrowe/laravel-config/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/benrowe/laravel-config/?branch=dev)
[![Total Downloads](https://poser.pugx.org/benrowe/laravel-config/d/total.svg)](https://packagist.org/packages/benrowe/laravel-config)
[![Latest Stable Version](https://poser.pugx.org/benrowe/laravel-config/v/stable.svg)](https://packagist.org/packages/benrowe/laravel-config)
[![Latest Unstable Version](https://poser.pugx.org/benrowe/laravel-config/v/unstable.svg)](https://packagist.org/packages/benrowe/laravel-config)
[![License](https://poser.pugx.org/benrowe/laravel-config/license.svg)](https://packagist.org/packages/benrowe/laravel-config)

A Laravel runtime configuration handler that supports hierarchical configuration,
however when stored, the data is flattened to basic key/value pairs (for storage in a simple db structure)

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
- Modifier support. Modifers can be registered to manipulate the value at runtime. (aka storing json, datetimes, etc).
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

This will provide access to the component via PSR-4. To configure the package as a laravel service, the service provider must be registered.

[1]: http://getcomposer.org/
[2]: https://laravel.com/docs/master/providers
