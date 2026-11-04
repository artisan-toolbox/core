<!-- MAINTAINER:README_HEAD:START -->
<div id="maintainer-title" align="center">
    <img src="logo/core-icon.svg" alt="" width="88">
    <h1>Artisan Toolbox Core</h1>
</div>

<p id="maintainer-badges" align="center">
<a href="https://github.com/artisan-toolbox/core/releases"><img src="https://img.shields.io/github/v/release/artisan-toolbox/core?label=Release&amp;style=flat-square" alt="Release"></a>
<a href="https://artisantoolbox.wsssoftware.com.br/packages/core/#requirements"><img src="https://img.shields.io/badge/PHP-%5E8.5-777BB4?style=flat-square" alt="PHP"></a>
<a href="https://artisantoolbox.wsssoftware.com.br/packages/core/#requirements"><img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square" alt="Laravel"></a>
<a href="https://github.com/artisan-toolbox/core/actions/workflows/tests.yml?query=branch%3A1.x"><img src="https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&amp;label=Tests&amp;style=flat-square" alt="Tests"></a>
<a href="https://artisantoolbox.wsssoftware.com.br/packages/core/"><img src="https://img.shields.io/badge/Docs-Read-blue?style=flat-square" alt="Docs"></a>
</p>

<p id="maintainer-description" align="center">
    Shared foundations, contracts, utilities, and common components for Artisan Toolbox packages.
</p>
<!-- MAINTAINER:README_HEAD:END -->

## Installation

```bash
composer require artisan-toolbox/core
```

Requires PHP 8.5, Laravel 13, and Inertia Laravel 3.x. Most applications receive Core through another Artisan Toolbox package.

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
