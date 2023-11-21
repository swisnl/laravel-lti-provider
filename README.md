# Laravel LTI Provider

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Buy us a tree][ico-treeware]][link-treeware]
[![Build Status][ico-github-actions]][link-github-actions]
[![Total Downloads][ico-downloads]][link-downloads]
[![Made by SWIS][ico-swis]][link-swis]

This packages provides a bridge between the Celtic LTI Provider and Laravel models.

## Install & Setup

Via Composer

``` bash
$ composer require swisnl/laravel-lti-provider
```

Then run command to copy the required files into your project.

```bash
php artisan lti-service-provider:install
```

If you have Laravel package auto discovery disabled, add the service provider to your `config/app.php` file:

```php
'providers' => [
    // ...
    Swis\Laravel\Lti\Providers\LtiServiceProvider::class,
];
```

The package comes with a very basic client implementation, but allows for overriding this implementation. To do so,
create a new class that implements the `Swis\Laravel\Lti\Contracts\LtiClient` interface and change the following to your
`config/lti-provider.php` file:

```php
        'lti-client' => 'REFERENCE TO YOUR NEW CLASS',
```

For inspiration on how to implement your own client, take a look at the `Swis\Laravel\Lti\Models\SimpleClient` class
(the very basic implementation) or the `\Workbench\App\OverrideModels\Client` class (this is a more complex example used
in the tests to check if it is possible to override the default implementation and if the package can handle clients 
with UUIDs instead of numeric ids).

After you have set up your client, you can run the migrations to create the required tables:

```bash
php artisan migrate
```

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
