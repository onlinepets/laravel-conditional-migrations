# Deprecated:
## We don't maintain this version anymore, checkout [Laravel conditional migrations](https://github.com/mll-lab/laravel-conditional-migrations) for the latest version

---

## Laravel Conditional Migrations

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This package allows you to configure migrations to run based on a condition. We
expose a `ConditionalMigration` interface, which you can have your migrations
implement to determine whether or not it should run.

## Index
- [Installation](#installation)
  - [Downloading](#downloading)
  - [Registering the service provider](#registering-the-service-provider)
- [Usage](#usage)
  - [Nightly cronjob](#nightly-cronjob)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)

## Installation
You'll have to follow a couple of steps to install this package.

### Downloading
Via [composer](http://getcomposer.org):

```bash
$ composer require onlinepets/laravel-conditional-migrations
```

Or add the package to your dependencies in `composer.json` and run
`composer update` on the command line to download the package:

```json
{
    "require": {
        "onlinepets/laravel-conditional-migrations": "^1.0"
    }
}
```


### Registering the service provider
If you're **not** using [auto discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518),
register the `\Onlinepets\ConditionalMigrations\ServiceProvider` in `config/app.php`:

```php
'providers' => [
    // ...
    Onlinepets\ConditionalMigrations\ServiceProvider::class,
];
```

## Usage
To make sure a migration only runs between 1 AM and 2 AM, implement the `ConditionalMigration`
interface and its `->shouldRun()` method:

```php
use Onlinepets\ConditionalMigrations\Contracts\ConditionalMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DoSomethingVeryIntensive extends Migration implements ConditionalMigration
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

    public function shouldRun(): bool
    {
        return (new Carbon('1 AM'))->lessThan(now())
            && (new Carbon('2 AM'))->greaterThan(now());
    }
}
```

The code snippet above will make sure the `do_something_very_intensive` migration
will be skipped unless it is executed between 1 AM and 2 AM. This can be useful
if your migration does something that should not be run during the daytime, like
adding an index to a table containing lots of data.

### Nightly cronjob
To take full advantage of this package, you can schedule a task to migrate the
database during the "whitelisted" times. **This package does not implement this**.

## Configuration
You can optionally publish the configuration file:

```bash
$ php artisan vendor:publish --provider="Onlinepets\ConditionalMigrations\ServiceProvider"
```

This will create the file `config/conditional-migrations.php`, which is where you can
configure whether your migrations should run, _regardless of individual configuration_:

```php
return [
    
    'always_run' => env('APP_DEBUG', false),
    
];
``` 

You can also use a closure if you want to do more advanced calculations:

```php
return [

    'always_run' => function (): bool {
        // calculate whether it should run
    },

];
```

## Contributing
All contributions (pull requests, issues and feature requests) are
welcome. Make sure to read through the [CONTRIBUTING.md](CONTRIBUTING.md) first,
though. See the [contributors page](../../graphs/contributors) for all contributors.

## License
`onlinepets/laravel-conditional-migrations` is licensed under the MIT License (MIT). Please
see the [license file](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/127100183/shield

[link-packagist]: https://packagist.org/packages/onlinepets/laravel-conditional-migrations
[link-downloads]: https://packagist.org/packages/onlinepets/laravel-conditional-migrations
[link-travis]: https://travis-ci.org/onlinepets/laravel-conditional-migrations
[link-styleci]: https://styleci.io/repos/127100183
