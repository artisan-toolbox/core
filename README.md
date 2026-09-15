<div align="center">
    <h1>Artisan Toolbox Core</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/artisan-toolbox/core"><img src="https://img.shields.io/packagist/v/artisan-toolbox/core.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://github.com/artisan-toolbox/core/actions"><img alt="GitHub Workflow Status (1.x)" src="https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square"></a>
</p>

Artisan Toolbox Core provides shared foundations, contracts, utilities, and common components for Artisan Toolbox packages.

## Installation

```bash
composer require artisan-toolbox/core
```

Requires PHP 8.5 and Laravel 13. Most applications receive Core through another Artisan Toolbox package.

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

The [complete documentation](https://artisantoolbox.wsssoftware.com.br/packages/core/) covers Sift's timing and nested iterable behavior, translated attributes, multi-domain Inertia visits, and published resources.

## Resources

- [Documentation](https://artisantoolbox.wsssoftware.com.br/packages/core/)
- [Changelog](CHANGELOG.md)
- [Contributing](.github/CONTRIBUTING.md)
- [Security policy](.github/SECURITY.md)
- [License](LICENSE.md)
