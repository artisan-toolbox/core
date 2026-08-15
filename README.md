<div align="center">
    <h1>Artisan Toolbox Core</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/artisan-toolbox/core"><img src="https://img.shields.io/packagist/v/artisan-toolbox/core.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://github.com/artisan-toolbox/core/actions"><img alt="GitHub Workflow Status (1.x)" src="https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square"></a>
</p>

ArtisanToolbox Core provides shared foundations, contracts, utilities, and common components for all ArtisanToolbox packages.

## Installation

You can install the package via Composer:

```bash
composer require artisan-toolbox/core
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="core"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="core-config"
```

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="core-migrations"
php artisan migrate
```

### Publishing the Views

```bash
php artisan vendor:publish --tag="core-views"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="core-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="core-assets"
```

## Usage

<!-- Add a basic usage example here. -->

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Artisan Toolbox Core! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Allan Mariucci Carvalho](https://github.com/artisan-toolbox)
- [All Contributors](../../contributors)

## License

Artisan Toolbox Core is open-sourced software licensed under the [MIT license](LICENSE.md).
