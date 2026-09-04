---
name: core-development
description: >
  Apply Artisan Toolbox Core conditional arrays, translated model attributes, and cross-domain Inertia middleware in Laravel applications.
license: MIT
metadata:
  author: Allan Mariucci Carvalho
---

# Artisan Toolbox Core

Use this skill when a Laravel application needs to integrate the Artisan Toolbox Core package.

## Primary Goal

- apply the `artisan-toolbox/core` package's public APIs in the smallest correct way

## Workflow

### 1. Inspect the Laravel app context

- confirm the app is a Laravel project
- inspect the target code paths where the package should be applied

### 2. Build conditional arrays

- import the namespaced `ArtisanToolbox\Core\sift_when` helper explicitly
- wrap conditional values with `sift_when($condition, $value)` and pass the complete iterable to `Illuminate\Support\Arr::sift()`, or call `sift()` on a Collection or LazyCollection
- use a closure as the second argument when its value is expensive and should only be resolved after the condition passes
- expect arrays and Collections to retain their types and keys, and LazyCollections to remain lazy

### 3. Translate stored Eloquent attribute keys

- use `ArtisanToolbox\Core\Casts\Translated` in the model's `casts()` method
- store a Laravel translation key such as `plans.starter`, not its translated display value
- query and persist using the untranslated key; attribute reads and serialization use the current application locale
- keep translation values scalar strings because the cast rejects translation groups that resolve to arrays

### 4. Handle cross-domain Inertia visits

- prepend `ArtisanToolbox\Core\Http\Middleware\HandleInertiaCrossDomainVisits` to the application's `web` middleware group when one Inertia application serves multiple trusted hosts
- configure CORS for the exact trusted origins and expose `x-inertia` and `x-inertia-location`
- do not register the middleware for applications that do not perform cross-host Inertia navigation

## Rules, References, and Templates

Read before executing:

- `https://artisantoolbox.wsssoftware.com.br/packages/core/conditional-arrays/`
- `https://artisantoolbox.wsssoftware.com.br/packages/core/translated-attributes/`
- `https://artisantoolbox.wsssoftware.com.br/packages/core/multi-domain-inertia/`

## Example

```php
use Illuminate\Support\Arr;

use function ArtisanToolbox\Core\sift_when;

$payload = Arr::sift([
    'name' => $user->name,
    'email' => sift_when($canViewEmail, fn (): string => $user->email),
]);
```

```php
use ArtisanToolbox\Core\Casts\Translated;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected function casts(): array
    {
        return [
            'label' => Translated::class,
        ];
    }
}
```

## Anti-patterns

- do not evaluate expensive conditional values before passing them to `sift_when()`; pass a closure instead
- do not mutate a source Collection after calling `sift()` and expect the already returned Collection to change
- do not store translated display values when a stable translation key is available
- do not query a cast attribute using its translated value
- do not use the translated string cast for user-authored multilingual content
- do not apply cross-domain middleware without configuring the corresponding trusted CORS origins
