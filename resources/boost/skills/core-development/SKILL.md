---
name: core-development
description: >
  Apply Artisan Toolbox Core conditional arrays, translated model attributes, cross-domain Inertia middleware, and Brazilian CPF, CNPJ, and phone validation in Laravel applications.
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

- confirm the app uses PHP 8.5 and Laravel 13. If Core is missing, run `composer require artisan-toolbox/core` from the application root
- inspect the target code paths where the package should be applied

### 2. Build conditional arrays

- import the namespaced `ArtisanToolbox\Core\sift_when` helper explicitly
- wrap conditional values with `sift_when($condition, $value)` and pass the complete iterable to `Illuminate\Support\Arr::sift()`, or call `sift()` on a Collection or LazyCollection
- use a closure as the second argument when its value is expensive and should only be resolved after the condition passes
- expect arrays and Collections to retain their types and keys, and LazyCollections to remain lazy
- remember that a passing condition invokes the closure at the `sift_when()` call; an ordinary array is built eagerly, while a LazyCollection generator runs during iteration

### 3. Translate stored Eloquent attribute keys

- use `ArtisanToolbox\Core\Casts\Translated` in the model's `casts()` method
- store a Laravel translation key such as `plans.starter`, not its translated display value
- query and persist using the untranslated key; attribute reads and serialization use the current application locale
- keep translation values scalar strings because the cast rejects translation groups that resolve to arrays

### 4. Handle cross-domain Inertia visits

- prepend `ArtisanToolbox\Core\Http\Middleware\HandleInertiaCrossDomainVisits` to the application's `web` middleware group when one Inertia application serves multiple trusted hosts
- configure CORS for the exact trusted origins and expose `x-inertia` and `x-inertia-location`
- do not register the middleware for applications that do not perform cross-host Inertia navigation

### 5. Validate Brazilian documents and phone numbers

- check that the installed Core build contains the `ArtisanToolbox\Core\Brasilian` APIs. They are available in the development preview
- import `DocumentRule` and `PhoneRule` from `ArtisanToolbox\Core\Brasilian\Rules`
- use `DocumentRule` with `DocumentType::Cpf` or `DocumentType::Cnpj`. The CNPJ case covers both numeric and alphanumeric values
- use `PhoneRule` with `PhoneType` cases that match the formats the form accepts. Use `RegionalLandline` or `RegionalMobile` to require a DDD
- pass one or several enum cases to a rule, or omit them to accept every supported type. Use Laravel's `required` or `nullable` for presence
- parse the validated string with `Document::from()` or `Phone::from()`, passing the same enum cases. Persist `unmask()` as a string and use `mask()` for display
- use `tryFrom()` for a nullable result, `isValid()` for a boolean, and `fake($type)` for tests. These APIs check structure and document check digits. Registry status and phone reachability require separate checks

## Rules, References, and Templates

Read before executing:

- `https://atb.dev.br/packages/core/conditional-arrays/`
- `https://atb.dev.br/packages/core/translated-attributes/`
- `https://atb.dev.br/packages/core/multi-domain-inertia/`
- `https://atb.dev.br/docs/core/brazilian-values`

## Example

```php
use Illuminate\Support\Arr;

use function ArtisanToolbox\Core\sift_when;

$payload = Arr::sift([
    'name' => 'Ada',
    'email' => sift_when(false, fn (): string => 'ada@example.com'),
]);
```

The payload is `['name' => 'Ada']`. The email closure stays unevaluated.

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

In a form request for a company contact, restrict the tax ID to CNPJ and accept
either a landline or mobile number with DDD.

```php
use ArtisanToolbox\Core\Brasilian\DocumentType;
use ArtisanToolbox\Core\Brasilian\PhoneType;
use ArtisanToolbox\Core\Brasilian\Rules\DocumentRule;
use ArtisanToolbox\Core\Brasilian\Rules\PhoneRule;

return [
    'tax_id' => ['required', new DocumentRule(DocumentType::Cnpj)],
    'contact_phone' => [
        'required',
        new PhoneRule(PhoneType::RegionalLandline, PhoneType::RegionalMobile),
    ],
];
```

`12.ABC.345/01DE-35` and `(11) 98765-4321` pass. The validated input retains its
original format. Call `Document::from($value)->unmask()` or
`Phone::from($value)->unmask()` to get the storage string.

## Anti-patterns

- do not evaluate expensive conditional values before passing them to `sift_when()`; pass a closure instead
- do not pass a `sift_when(false, ...)` result to the client without calling `sift()` on the enclosing iterable
- do not mutate a source Collection after calling `sift()` and expect the already returned Collection to change
- do not store translated display values when a stable translation key is available
- do not query a cast attribute using its translated value
- do not use the translated string cast for user-authored multilingual content
- do not apply cross-domain middleware without configuring the corresponding trusted CORS origins
