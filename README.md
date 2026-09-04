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

## Usage

Build arrays with conditionally included values using the `Arr::sift()` macro and namespaced `sift_when()` helper:

```php
use Illuminate\Support\Arr;

use function ArtisanToolbox\Core\sift_when;

$payload = Arr::sift([
    'name' => $user->name,
    'email' => sift_when($canViewEmail, fn (): string => $user->email),
]);
```

The macro is also available as `$collection->sift()` and `$lazyCollection->sift()`, returning new instances without mutating or eagerly consuming their sources.

See the [conditional arrays documentation](https://artisantoolbox.wsssoftware.com.br/packages/core/conditional-arrays/) for recursive iterable behavior and key-preservation details.

## Resources

- [Documentation](https://artisantoolbox.wsssoftware.com.br/packages/core/)
- [Changelog](CHANGELOG.md)
- [Contributing](.github/CONTRIBUTING.md)
- [Security policy](.github/SECURITY.md)
- [License](LICENSE.md)
