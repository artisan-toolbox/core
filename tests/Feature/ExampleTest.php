<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Core;

it('resolves the singleton', function () {
    expect(resolve(Core::class))->toBeInstanceOf(Core::class);
});

it('returns the same instance from the container', function () {
    expect(resolve(Core::class))->toBe(resolve(Core::class));
});

it('merges the package config', function () {
    expect(config('core.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('core::messages.placeholder'))->toBe('Core placeholder translation.');
});

it('loads the package views', function () {
    expect(view()->exists('core::placeholder'))->toBeTrue();
});

it('registers the artisan command', function () {
    $this->artisan('core:placeholder')
        ->expectsOutputToContain('Core placeholder command executed.')
        ->assertSuccessful();
});
