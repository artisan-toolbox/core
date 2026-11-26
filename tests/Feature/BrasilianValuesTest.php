<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Brasilian\Document;
use ArtisanToolbox\Core\Brasilian\DocumentType;
use ArtisanToolbox\Core\Brasilian\Phone;
use ArtisanToolbox\Core\Brasilian\PhoneType;
use ArtisanToolbox\Core\Brasilian\Rules\DocumentRule;
use ArtisanToolbox\Core\Brasilian\Rules\PhoneRule;
use Illuminate\Support\Facades\Validator;

it('parses and formats Brazilian documents without losing leading zeros or letters', function (DocumentType $type, string $raw, string $masked) {
    $document = Document::from($masked);

    expect($document->type)->toBe($type)
        ->and($document->unmask())->toBe($raw)
        ->and(Document::from($raw, $type)->mask())->toBe($masked);
})->with(fn () => [
    'CPF' => [DocumentType::Cpf, '01234567890', '012.345.678-90'],
    'numeric CNPJ' => [DocumentType::Cnpj, '00000000000191', '00.000.000/0001-91'],
    'Receita Federal check-digit example' => [DocumentType::Cnpj, '12ABC34501DE35', '12.ABC.345/01DE-35'],
]);

it('normalizes lowercase CNPJ as uppercase ASCII', function () {
    expect(Document::from("\t12.abc.345/01de-35\n")->unmask())->toBe('12ABC34501DE35')
        ->and(Document::from('12abc34501de35')->mask())->toBe('12.ABC.345/01DE-35');
});

it('rejects malformed documents and incorrect check digits', function (string $value) {
    expect(Document::tryFrom($value))->toBeNull()
        ->and(Document::isValid($value))->toBeFalse()
        ->and(fn () => Document::from($value))->toThrow(InvalidArgumentException::class);
})->with([
    '',
    '012.345.678-91',
    '00000000000',
    '111.111.111-11',
    '00.000.000/0000-00',
    '00.000.000/0001-92',
    '12.ABC.345/01DE-45',
    '12.ABC.345/01DE-36',
    '12.ABC.345/01DE-A5',
    '12.ÀBC.345/01DE-35',
    '12.ABC.345/01DE 35',
    '12ABC345!01DE35',
    '12AB.C34501DE35',
    'A1234567890',
]);

it('restricts document parsing to the requested types', function () {
    expect(Document::tryFrom('012.345.678-90', DocumentType::Cnpj))->toBeNull()
        ->and(Document::isValid('12ABC34501DE35', DocumentType::Cpf, DocumentType::Cnpj))->toBeTrue();
});

it('recognizes the concrete Front Awesome phone formats', function (PhoneType $type, string $raw, string $masked) {
    $phone = Phone::from($masked);

    expect($phone->type)->toBe($type)
        ->and($phone->unmask())->toBe($raw)
        ->and(Phone::from($raw, $type)->mask())->toBe($masked);
})->with(fn () => [
    'landline' => [PhoneType::Landline, '23456789', '2345-6789'],
    'mobile' => [PhoneType::Mobile, '787654321', '78765-4321'],
    'regional landline' => [PhoneType::RegionalLandline, '1163456789', '(11) 6345-6789'],
    'regional mobile' => [PhoneType::RegionalMobile, '11887654321', '(11) 88765-4321'],
    'international landline' => [PhoneType::InternationalPhone, '551134567890', '+55 (11) 3456-7890'],
    'international mobile' => [PhoneType::InternationalPhone, '5511987654321', '+55 (11) 98765-4321'],
    'three-digit short code' => [PhoneType::ShortCode3, '190', '190'],
    'four-digit short code' => [PhoneType::ShortCode4, '1052', '1052'],
    'five-digit short code starting with 103' => [PhoneType::ShortCode5, '10315', '10315'],
    'five-digit short code starting with 106' => [PhoneType::ShortCode5, '10621', '10621'],
    'service center starting with 300' => [PhoneType::ServiceCenter, '30030100', '3003-0100'],
    'service center starting with 400' => [PhoneType::ServiceCenter, '40040200', '4004-0200'],
    'toll free' => [PhoneType::TollFree, '08001234567', '0800 123 4567'],
    'shared cost' => [PhoneType::SharedCost, '03001234567', '0300 123 4567'],
    'donation' => [PhoneType::Donation, '05001234567', '0500 123 4567'],
    'premium' => [PhoneType::Premium, '09001234567', '0900 123 4567'],
    'telemarketing' => [PhoneType::Telemarketing, '03031234567', '0303 123 4567'],
]);

it('keeps the country code when parsing compact international numbers', function () {
    $phone = Phone::from(' +5511987654321 ');

    expect($phone->type)->toBe(PhoneType::InternationalPhone)
        ->and($phone->mask())->toBe('+55 (11) 98765-4321')
        ->and($phone->unmask())->toBe('5511987654321');
});

it('uses the first allowed type when multiple phone formats match', function () {
    expect(Phone::from('3003-0100', PhoneType::Landline, PhoneType::ServiceCenter)->type)->toBe(PhoneType::Landline)
        ->and(Phone::from('3003-0100', PhoneType::ServiceCenter, PhoneType::Landline)->type)->toBe(PhoneType::ServiceCenter)
        ->and(Phone::tryFrom('190', PhoneType::Mobile, PhoneType::RegionalMobile))->toBeNull();
});

it('rejects invalid or incomplete phones', function (string $value) {
    expect(Phone::tryFrom($value))->toBeNull()
        ->and(Phone::isValid($value))->toBeFalse()
        ->and(fn () => Phone::from($value))->toThrow(InvalidArgumentException::class);
})->with([
    '',
    '(10) 98765-4321',
    '(01) 2345-6789',
    '68765-4321',
    '7456-7890',
    '10499',
    '0800 123 456',
    '0400 123 4567',
    '+54 (11) 98765-4321',
    '+11987654321',
    '++5511987654321',
    '1198765-4321',
    '11x987654321',
]);

it('generates usable examples for every Brazilian document type', function (DocumentType $type) {
    $document = Document::fake($type);

    expect($document->type)->toBe($type)
        ->and(Document::isValid($document->mask(), $type))->toBeTrue();

    if ($type === DocumentType::Cnpj) {
        expect($document->unmask())->toMatch('/[A-Z]/');
    }
})->with(fn () => DocumentType::cases());

it('generates usable examples for every Brazilian phone type', function (PhoneType $type) {
    $phone = Phone::fake($type);

    expect($phone->type)->toBe($type)
        ->and(Phone::isValid($phone->mask(), $type))->toBeTrue();
})->with(fn () => PhoneType::cases());

it('accepts all, one, or several types through the Laravel validator', function (
    string $value,
    DocumentRule|PhoneRule $rule,
    bool $passes,
) {
    expect(Validator::make(['value' => $value], ['value' => ['required', $rule]])->passes())->toBe($passes);
})->with(fn () => [
    'any document' => ['12.ABC.345/01DE-35', new DocumentRule, true],
    'only CNPJ' => ['12.abc.345/01de-35', new DocumentRule(DocumentType::Cnpj), true],
    'only CPF' => ['12.ABC.345/01DE-35', new DocumentRule(DocumentType::Cpf), false],
    'CPF or CNPJ' => ['01234567890', new DocumentRule(DocumentType::Cnpj, DocumentType::Cpf), true],
    'invalid CNPJ checksum' => ['12ABC34501DE36', new DocumentRule, false],
    'any phone' => ['+55 (11) 98765-4321', new PhoneRule, true],
    'only mobile' => ['987654321', new PhoneRule(PhoneType::Mobile), true],
    'DDD required' => ['987654321', new PhoneRule(PhoneType::RegionalMobile), false],
    'regional mobile or landline' => ['(11) 3456-7890', new PhoneRule(PhoneType::RegionalMobile, PhoneType::RegionalLandline), true],
    'short code outside the selected types' => ['190', new PhoneRule(PhoneType::Mobile, PhoneType::RegionalMobile), false],
]);

it('works with nullable fields and reports non-string values through Laravel errors', function () {
    $absent = Validator::make(['tax_id' => null, 'phone' => null], [
        'tax_id' => ['nullable', new DocumentRule],
        'phone' => ['nullable', new PhoneRule],
    ]);

    $invalid = Validator::make(['tax_id' => 52998224725, 'phone' => ['11987654321']], [
        'tax_id' => [new DocumentRule],
        'phone' => [new PhoneRule],
    ]);

    expect($absent->passes())->toBeTrue()
        ->and($invalid->errors()->first('tax_id'))->toBe('The tax id must be a valid Brazilian document.')
        ->and($invalid->errors()->first('phone'))->toBe('The phone must be a valid Brazilian phone number.');
});
