<div align="center">
    <h1>Artisan Toolbox Core</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/artisan-toolbox/core"><img src="https://img.shields.io/packagist/v/artisan-toolbox/core.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://github.com/artisan-toolbox/core/actions"><img alt="GitHub Workflow Status (1.x)" src="https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square"></a>
</p>

Artisan Toolbox Core provides shared foundations, contracts, utilities, and common components for Artisan Toolbox packages.

## Documentation

The complete installation, resource publishing, and compatibility documentation is available at [artisantoolbox.wsssoftware.com.br/packages/core](https://artisantoolbox.wsssoftware.com.br/packages/core/).

## Installation

```bash
composer require artisan-toolbox/core
```

Most applications receive Core through another Artisan Toolbox package. When using it directly, publish its resources with:

```bash
php artisan vendor:publish --tag="core"
```

## Resources

- [Documentation](https://artisantoolbox.wsssoftware.com.br/packages/core/)
- [Changelog](CHANGELOG.md)
- [Contributing](.github/CONTRIBUTING.md)
- [Security policy](.github/SECURITY.md)
- [License](LICENSE.md)
