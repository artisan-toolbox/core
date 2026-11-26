<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Brasilian;

use InvalidArgumentException;
use Random\Randomizer;

final readonly class Phone
{
    private const array DIGIT_POOLS = [
        '#' => '0123456789',
        'D' => '123456789',
        'L' => '23456',
        'M' => '789',
        'S' => '34',
        'C' => '36',
    ];

    private const array SEPARATORS = ['+', ' ', '(', ')', '-'];

    private const string LANDLINE = 'L###-####';

    private const string MOBILE = 'M####-####';

    private const string REGIONAL_LANDLINE = '(DD) '.self::LANDLINE;

    private const string REGIONAL_MOBILE = '(DD) '.self::MOBILE;

    private const int INTERNATIONAL_MOBILE_LENGTH = 13;

    private function __construct(
        public PhoneType $type,
        private string $raw,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public static function from(string $value, PhoneType ...$types): self
    {
        return self::tryFrom($value, ...$types)
            ?? throw new InvalidArgumentException('Invalid Brazilian phone number.');
    }

    public static function tryFrom(string $value, PhoneType ...$types): ?self
    {
        $value = trim($value, " \t\n\r\v\f");
        $raw = str_replace(self::SEPARATORS, '', $value);

        foreach ($types ?: PhoneType::cases() as $type) {
            $template = self::template($type, strlen($raw) === self::INTERNATIONAL_MOBILE_LENGTH);

            if (! self::matchesRaw($raw, $template)) {
                continue;
            }

            if (
                $value === $raw
                || $value === self::format($raw, $template)
                || ($type === PhoneType::InternationalPhone && $value === '+'.$raw)
            ) {
                return new self($type, $raw);
            }
        }

        return null;
    }

    public static function isValid(string $value, PhoneType ...$types): bool
    {
        return self::tryFrom($value, ...$types) !== null;
    }

    /**
     * Generate a structurally valid test value for the given type.
     */
    public static function fake(PhoneType $type): self
    {
        $random = new Randomizer;
        $template = self::template($type, $type === PhoneType::InternationalPhone && $random->getInt(0, 1) === 1);
        $tokens = str_split(str_replace(self::SEPARATORS, '', $template));

        do {
            $raw = '';

            foreach ($tokens as $token) {
                $raw .= isset(self::DIGIT_POOLS[$token])
                    ? $random->getBytesFromString(self::DIGIT_POOLS[$token], 1)
                    : $token;
            }
        } while (
            $type === PhoneType::Landline
            && self::matchesRaw($raw, self::template(PhoneType::ServiceCenter, false))
        );

        return new self($type, $raw);
    }

    public function mask(): string
    {
        $template = self::template($this->type, strlen($this->raw) === self::INTERNATIONAL_MOBILE_LENGTH);

        return self::format($this->raw, $template);
    }

    public function unmask(): string
    {
        return $this->raw;
    }

    private static function template(PhoneType $type, bool $internationalMobile): string
    {
        return match ($type) {
            PhoneType::ServiceCenter => 'S00#-####',
            PhoneType::Landline => self::LANDLINE,
            PhoneType::Mobile => self::MOBILE,
            PhoneType::RegionalLandline => self::REGIONAL_LANDLINE,
            PhoneType::RegionalMobile => self::REGIONAL_MOBILE,
            PhoneType::InternationalPhone => '+55 '.($internationalMobile ? self::REGIONAL_MOBILE : self::REGIONAL_LANDLINE),
            PhoneType::ShortCode3 => '1##',
            PhoneType::ShortCode4 => '1###',
            PhoneType::ShortCode5 => '10C##',
            PhoneType::TollFree => '0800 ### ####',
            PhoneType::SharedCost => '0300 ### ####',
            PhoneType::Donation => '0500 ### ####',
            PhoneType::Premium => '0900 ### ####',
            PhoneType::Telemarketing => '0303 ### ####',
        };
    }

    private static function matchesRaw(string $raw, string $template): bool
    {
        $template = str_replace(self::SEPARATORS, '', $template);

        if (strlen($raw) !== strlen($template)) {
            return false;
        }

        foreach (str_split($template) as $index => $token) {
            if (! str_contains(self::DIGIT_POOLS[$token] ?? $token, $raw[$index])) {
                return false;
            }
        }

        return true;
    }

    private static function format(string $raw, string $template): string
    {
        $masked = '';
        $index = 0;

        foreach (str_split($template) as $token) {
            $masked .= in_array($token, self::SEPARATORS, true) ? $token : $raw[$index++];
        }

        return $masked;
    }
}
