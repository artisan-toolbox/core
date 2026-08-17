<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Casts\Translated;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Translation\Translator;

beforeEach(function () {
    /** @var Translator $translator */
    $translator = resolve('translator');

    $translator->addLines([
        'plans.starter' => 'Starter',
    ], 'en');

    $translator->addLines([
        'plans.starter' => 'Inicial',
    ], 'pt_BR');
});

it('translates a stored key using the current locale', function () {
    $model = new TranslatedAttributeModel;
    $model->setRawAttributes(['label' => 'plans.starter']);

    app()->setLocale('en');

    expect($model->label)->toBe('Starter');

    app()->setLocale('pt_BR');

    expect($model->label)->toBe('Inicial')
        ->and($model->toArray()['label'])->toBe('Inicial');
});

it('returns a stored string when no translation exists', function () {
    $model = new TranslatedAttributeModel;
    $model->setRawAttributes(['label' => 'Custom plan']);

    expect($model->label)->toBe('Custom plan');
});

it('preserves null values', function () {
    $model = new TranslatedAttributeModel;
    $model->setRawAttributes(['label' => null]);

    expect($model->label)->toBeNull();
});

it('stores the untranslated value when assigning the attribute', function () {
    app()->setLocale('en');

    $model = new TranslatedAttributeModel;

    $model->label = 'plans.starter';

    expect($model->getAttributes()['label'])->toBe('plans.starter')
        ->and($model->label)->toBe('Starter');
});

it('rejects translations that resolve to an array', function () {
    $model = new TranslatedAttributeModel;
    $model->setRawAttributes(['label' => 'plans']);

    expect(fn () => $model->label)->toThrow(InvalidArgumentException::class);
});

#[WithoutTimestamps]
class TranslatedAttributeModel extends Model
{
    use HasFactory;
    use HasFactory;

    /**
     * @return array<string, class-string>
     */
    protected function casts(): array
    {
        return [
            'label' => Translated::class,
        ];
    }
}
