<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Brasilian\Rules;

use ArtisanToolbox\Core\Brasilian\Phone;
use ArtisanToolbox\Core\Brasilian\PhoneType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class PhoneRule implements ValidationRule
{
    /** @var list<PhoneType> */
    private array $types;

    public function __construct(PhoneType ...$types)
    {
        $this->types = array_values($types);
    }

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Phone::isValid($value, ...$this->types)) {
            $fail('core::validation.phone')->translate();
        }
    }
}
