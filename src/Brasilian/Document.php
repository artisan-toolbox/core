<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Brasilian;

use InvalidArgumentException;
use Random\Randomizer;

final readonly class Document
{
    private const string DIGITS = '0123456789';

    private const string LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private function __construct(
        public DocumentType $type,
        private string $raw,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public static function from(string $value, DocumentType ...$types): self
    {
        return self::tryFrom($value, ...$types)
            ?? throw new InvalidArgumentException('Invalid Brazilian document.');
    }

    public static function tryFrom(string $value, DocumentType ...$types): ?self
    {
        $value = strtoupper(trim($value, " \t\n\r\v\f"));
        $raw = str_replace(['.', '/', '-'], '', $value);

        foreach ($types ?: DocumentType::cases() as $type) {
            $pattern = match ($type) {
                DocumentType::Cpf => '/\A[0-9]{11}\z/',
                DocumentType::Cnpj => '/\A[0-9A-Z]{12}[0-9]{2}\z/',
            };

            if (preg_match($pattern, $raw) !== 1 || $raw === str_repeat($raw[0], strlen($raw))) {
                continue;
            }

            if ($value !== $raw && $value !== self::format($raw, $type)) {
                continue;
            }

            $body = substr($raw, 0, -2);

            if ($raw === self::addCheckDigits($body, $type)) {
                return new self($type, $raw);
            }
        }

        return null;
    }

    public static function isValid(string $value, DocumentType ...$types): bool
    {
        return self::tryFrom($value, ...$types) !== null;
    }

    /**
     * Generate a structurally valid test value. Generated CNPJ values contain a letter.
     */
    public static function fake(DocumentType $type): self
    {
        $random = new Randomizer;

        if ($type === DocumentType::Cpf) {
            do {
                $body = $random->getBytesFromString(self::DIGITS, 9);
            } while ($body === str_repeat($body[0], 9));
        } else {
            $body = $random->getBytesFromString(self::LETTERS, 1)
                .$random->getBytesFromString(self::DIGITS.self::LETTERS, 11);
        }

        return new self($type, self::addCheckDigits($body, $type));
    }

    public function mask(): string
    {
        return self::format($this->raw, $this->type);
    }

    public function unmask(): string
    {
        return $this->raw;
    }

    private static function format(string $raw, DocumentType $type): string
    {
        return match ($type) {
            DocumentType::Cpf => sprintf(
                '%s.%s.%s-%s',
                substr($raw, 0, 3),
                substr($raw, 3, 3),
                substr($raw, 6, 3),
                substr($raw, 9, 2),
            ),
            DocumentType::Cnpj => sprintf(
                '%s.%s.%s/%s-%s',
                substr($raw, 0, 2),
                substr($raw, 2, 3),
                substr($raw, 5, 3),
                substr($raw, 8, 4),
                substr($raw, 12, 2),
            ),
        };
    }

    private static function addCheckDigits(string $body, DocumentType $type): string
    {
        $body .= self::checkDigit($body, $type);

        return $body.self::checkDigit($body, $type);
    }

    private static function checkDigit(string $body, DocumentType $type): int
    {
        $sum = 0;
        $weight = 2;

        for ($offset = strlen($body) - 1; $offset >= 0; $offset--) {
            $sum += (ord($body[$offset]) - ord('0')) * $weight;
            $weight = $type === DocumentType::Cnpj && $weight === 9 ? 2 : $weight + 1;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
