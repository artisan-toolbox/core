<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Brasilian\Rules;

use ArtisanToolbox\Core\Brasilian\Document;
use ArtisanToolbox\Core\Brasilian\DocumentType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class DocumentRule implements ValidationRule
{
    /** @var list<DocumentType> */
    private array $types;

    public function __construct(DocumentType ...$types)
    {
        $this->types = array_values($types);
    }

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Document::isValid($value, ...$this->types)) {
            $fail('core::validation.document')->translate();
        }
    }
}
