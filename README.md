# laravel-elastic

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Buy us a tree][ico-treeware]][link-treeware]
[![Build Status][ico-github-actions]][link-github-actions]
[![Total Downloads][ico-downloads]][link-downloads]
[![Made by SWIS][ico-swis]][link-swis]


Laravel package that implements elasticsearch in an easy way. Index various models via the `SyncsWithIndex` trait and customize further by extending the `Document` and `SearchResult` class.



## Install

Via Composer

``` bash
$ composer require swisnl/laravel-elasticsearch
```
## Usage
implemnent the LtiClient interface to your oauth2 client model. Implement the methods and add the following attributes to the model
```
'lti_platform_id',
'lti_client_id',
'lti_deployment_id',
'lti_version',
'lti_signature_method',
'lti_profile',
'lti_settings',
'lti_user_type'
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

[ico-version]: https://img.shields.io/packagist/v/swisnl/laravel-elasticsearch.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-treeware]: https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen.svg?style=flat-square
[ico-github-actions]: https://img.shields.io/github/actions/workflow/status/swisnl/laravel-elasticsearch/tests.yml?label=tests&branch=master&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/swisnl/laravel-elasticsearch.svg?style=flat-square
[ico-swis]: https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%230737A9.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/swisnl/laravel-elasticsearch
[link-github-actions]: https://github.com/swisnl/laravel-elasticsearch/actions/workflows/tests.yml
[link-downloads]: https://packagist.org/packages/swisnl/laravel-elasticsearch
[link-treeware]: https://plant.treeware.earth/swisnl/laravel-elasticsearch
[link-author]: https://github.com/tommie1001
[link-contributors]: ../../contributors
[link-swis]: https://www.swis.nl
