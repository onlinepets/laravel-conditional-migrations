# Laravel Timed Migrations

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-circleci]][link-circleci]
[![StyleCI][ico-styleci]][link-styleci]

This package allows you to configure migrations to run when you want them to. We
expose a `RunsInTimeframe` interface, which you can have your migrations
implement to determine when it should be ran.

## Index
- [Installation](#installation)
  - [Downloading](#downloading)
  - [Registering the service provider](#registering-the-service-provider)
- [Usage](#usage)
  - [Nightly cronjob](#nightly-cronjob)
- [Contributing](#contributing)
- [License](#license)

## Installation
You'll have to follow a couple of steps to install this package.

### Downloading
Via [composer](http://getcomposer.org):

```bash
$ composer require onlinepets/laravel-timed-migrations
```

Or add the package to your dependencies in `composer.json` and run
`composer update` on the command line to download the package:

```json
{
    "require": {
        "onlinepets/laravel-timed-migrations": "^1.0"
    }
}
```


### Registering the service provider
If you're **not** using [auto discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518),
register the `\Onlinepets\TimedMigrations\ServiceProvider` in `config/app.php`:

```php
'providers' => [
    // ...
    Onlinepets\TimedMigrations\ServiceProvider::class,
];
```

## Usage
To flag a migration to run only between 1AM and 2AM, implement the `RunsInTimeframe`
interface and flesh out the `->getTimesToRunBetween()` method:

```php
<?php

use Onlinepets\TimedMigrations\Contracts\RunsInTimeframe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DoSomethingVeryIntensive extends Migration implements RunsInTimeframe
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    public function getTimesToRunBetween(): array
    {
        return [
            new Carbon('1 AM'),
            new Carbon('2 AM'),
        ];
    }
}
```

The code snippet above will make sure the `do_something_very_intensive` migration
will only run successfully when executed between 1AM and 2AM. This is mostly useful
if your migration does something that should not be run during the daytime, like
adding an index to a table containing lots of data.

### Nightly cronjob
To take full advantage of this package, you can schedule a task to migrate the
database during the "whitelisted" times. **This package does not implement this**. 

## Contributing
All contributions (pull requests, issues and feature requests) are
welcome. Make sure to read through the [CONTRIBUTING.md](CONTRIBUTING.md) first,
though. See the [contributors page](../../graphs/contributors) for all contributors.

## License
`onlinepets/laravel-timed-migrations` is licensed under the MIT License (MIT). Please
see the [license file](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/onlinepets/laravel-timed-migrations.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/onlinepets/laravel-timed-migrations.svg?style=flat-square
[ico-circleci]: https://img.shields.io/circleci/project/github/onlinepets/laravel-timed-migrations.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/:styleci/shield

[link-packagist]: https://packagist.org/packages/onlinepets/laravel-timed-migrations
[link-downloads]: https://packagist.org/packages/onlinepets/laravel-timed-migrations
[link-circleci]: https://circleci.com/gh/onlinepets/laravel-timed-migrations
[link-styleci]: https://styleci.io/repos/:styleci
