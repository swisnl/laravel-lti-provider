# Laravel LTI Provider

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Buy us a tree][ico-treeware]][link-treeware]
[![Build Status][ico-github-actions]][link-github-actions]
[![Total Downloads][ico-downloads]][link-downloads]
[![Made by SWIS][ico-swis]][link-swis]

This packages provides a bridge between the [Celtic LTI Package](https://github.com/celtic-project/LTI-PHP) and Laravel
models, by implementing a DataConnector for the Celtic LTI Package.

## Install

Via Composer

``` bash
$ composer require swisnl/laravel-lti-provider
```

Then run command to copy the required files (including the migrations) into your project.

```bash
php artisan lti-provider:install
```

If you have Laravel package auto discovery disabled, add the service provider to your `config/app.php` file:

```php
'providers' => [
    // ...
    Swis\Laravel\Lti\Providers\LtiProviderServiceProvider::class,
];
```

Finally run the migrations.

```bash
php artisan migrate
```

## Cron jobs

The package comes with a command to clean up expired LTI nonces. To run this command, add the following to your
`app/Console/Kernel.php` file:

```php
    protected function schedule(Schedule $schedule)
    {
        // ...
        $schedule->command('lti-provider:delete-expired-nonces')->daily();
    }
```

## Usage

Define a model that you use as an LTI environment. This model should implement the 
`Swis\Laravel\Lti\Contracts\LtiEnvironment`. The packages scopes all other models to the current LTI environment. 

Using this this environment, you can create a new `ModelDataConnector` instance. This instance can be used like the
`DataConnector` from the Celtic LTI Package.

```php
    $environment = MyLtiEnvironment::find($id);
    $dataConnector = new ModelDataConnector($environment);
```

## Customization

This package allows overriding most of the models. We use this to override some models to use UUIDs instead of numeric
ids, and to add some extra functionality (examples of this extra functionality: adding logging to `UserResult`; and
using the same `Client` model for both LTI and [Laravel Passport](https://laravel.com/docs/10.x/passport)). 

To override a model (except the client), create a new class that extends the original model and register the new model
in the `config/lti-provider.php` file. For example, to override the `UserResult` model, create a new class that extends
the `Swis\Laravel\Lti\Models\UserResult` class and change the following to your `config/lti-provider.php` file:

```php
        'models' => [
            'user-result' => 'REFERENCE TO YOUR NEW CLASS',
        ],
```

Clients are a bit different, because you don't need to extend from an existing class. To override the client, create a
new class that implements the `Swis\Laravel\Lti\Contracts\Client` interface and register the new model in the
`config/lti-provider.php` file.

For inspiration on how to implement your own client, take a look at the `Swis\Laravel\Lti\Models\SimpleClient` class
(a very basic implementation) or the `\Workbench\App\OverrideModels\Client` class (this is a more complex example used
in the tests to check if it is possible to override the default implementation and if the package can handle clients 
with UUIDs instead of numeric ids).

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email security@swis.nl instead of using the issue tracker.

## Credits

- [Thomas Wijnands][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**][link-treeware] to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.

## SWIS :heart: Open Source

[SWIS][link-swis] is a web agency from Leiden, the Netherlands. We love working with open source software. 

[ico-version]: https://img.shields.io/packagist/v/swisnl/laravel-lti-provider.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-treeware]: https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen.svg?style=flat-square
[ico-github-actions]: https://img.shields.io/github/actions/workflow/status/swisnl/laravel-lti-provider/tests.yml?label=tests&branch=master&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/swisnl/laravel-lti-provider.svg?style=flat-square
[ico-swis]: https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%230737A9.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/swisnl/laravel-lti-provider
[link-github-actions]: https://github.com/swisnl/laravel-lti-provider/actions/workflows/tests.yml
[link-downloads]: https://packagist.org/packages/swisnl/laravel-lti-provider
[link-treeware]: https://plant.treeware.earth/swisnl/laravel-lti-provider
[link-author]: https://github.com/tommie1001
[link-contributors]: ../../contributors
[link-swis]: https://www.swis.nl
