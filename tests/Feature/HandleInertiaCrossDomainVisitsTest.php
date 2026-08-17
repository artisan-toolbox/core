<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Http\Middleware\HandleInertiaCrossDomainVisits;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::any('/destination', fn () => response('handled'))
        ->middleware(HandleInertiaCrossDomainVisits::class);
});

it('continues non-Inertia requests', function () {
    $this->withHeader('Referer', 'https://app.example.com/source')
        ->get('https://admin.example.com/destination')
        ->assertOk()
        ->assertSeeText('handled');
});

it('continues non-GET Inertia requests', function () {
    $this->withHeaders([
        'Referer' => 'https://app.example.com/source',
        'X-Inertia' => 'true',
    ])->post('https://admin.example.com/destination')
        ->assertOk()
        ->assertSeeText('handled');
});

it('continues same-host Inertia visits', function () {
    $this->withHeaders([
        'Referer' => 'https://admin.example.com/source',
        'X-Inertia' => 'true',
    ])->get('https://admin.example.com/destination')
        ->assertOk()
        ->assertSeeText('handled');
});

it('forces a full-page visit when an Inertia GET crosses hosts', function () {
    $this->withHeaders([
        'Referer' => 'https://app.example.com/source',
        'X-Inertia' => 'true',
    ])->get('https://admin.example.com/destination?section=users')
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', 'https://admin.example.com/destination?section=users')
        ->assertContent('');
});
