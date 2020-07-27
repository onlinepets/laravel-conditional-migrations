# Laravel Conditional Migrations

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

[ico-version]: https://img.shields.io/packagist/v/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/onlinepets/laravel-conditional-migrations.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/127100183/shield

[link-packagist]: https://packagist.org/packages/onlinepets/laravel-conditional-migrations
[link-downloads]: https://packagist.org/packages/onlinepets/laravel-conditional-migrations
[link-travis]: https://travis-ci.org/onlinepets/laravel-conditional-migrations
[link-styleci]: https://styleci.io/repos/127100183

Run migrations only if a condition is true.

## Installation

Via [composer](http://getcomposer.org):

    composer require onlinepets/laravel-conditional-migrations

## Usage

To run a migration conditionally, implement the `ConditionalMigration`
interface and its `->shouldRun()` method:

```php
use Onlinepets\ConditionalMigrations\Contracts\ConditionalMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class DoSomethingVeryIntensive extends Migration implements ConditionalMigration
{
    public function up() { ... }
    public function down() { ... }

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

## Configuration

You can optionally publish the configuration file:

    php artisan vendor:publish --tags=conditional-migrations-config

This will create the file `config/conditional-migrations.php`.

The `always_run` option allows you to overrule the conditions set in individual migrations.

```php
'always_run' => env('APP_DEBUG', false),
``` 

You can also use a closure if you want to do more advanced calculations:

```php
'always_run' => function (): bool {
    // calculate if migrations should always run
},
```

## Contributing

Contributions are welcome, see [CONTRIBUTING.md](CONTRIBUTING.md).

## License

See [LICENSE.md](LICENSE.md).
