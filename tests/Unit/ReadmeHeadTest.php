<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Core;
use ArtisanToolbox\Maintainer\Versionable\Readme\Badge;

it('configures the package logo and badges for the managed README header', function () {
    $head = (new Core)->readmeHead();

    expect($head->title())->toBe('Artisan Toolbox Core')
        ->and($head->logo())->toBe(['src' => 'logo/core-icon.svg', 'alt' => '', 'width' => 88])
        ->and(dirname(__DIR__, 2).'/logo/core-icon.svg')->toBeFile()
        ->and(array_map(static fn (Badge $badge): array => [
            $badge->imgAlt(), $badge->link(), $badge->imgSrc('1.2.3-beta.4'),
        ], $head->badges()))->toBe([
            ['version', 'https://github.com/artisan-toolbox/core/releases', 'https://img.shields.io/badge/version-1.2.3--beta.4-blue?style=flat-square'],
            ['Packagist', 'https://packagist.org/packages/artisan-toolbox/core', 'https://img.shields.io/badge/Packagist-F28D1A?style=flat-square&logo=packagist&logoColor=white'],
            ['PHP', 'https://atb.dev.br/packages/core/#requirements', 'https://img.shields.io/badge/PHP-%5E8.5-777BB4?style=flat-square'],
            ['Laravel', 'https://atb.dev.br/packages/core/#requirements', 'https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square'],
            ['Tests', 'https://github.com/artisan-toolbox/core/actions/workflows/tests.yml?query=branch%3A1.x', 'https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square'],
            ['Docs', 'https://atb.dev.br/packages/core/', 'https://img.shields.io/badge/Docs-Read-blue?style=flat-square'],
        ]);
});
