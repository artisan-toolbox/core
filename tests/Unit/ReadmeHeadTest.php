<?php

declare(strict_types=1);

use ArtisanToolbox\Core\Core;
use ArtisanToolbox\Maintainer\Versionable\Readme\Badge;

it('configures the package logo and badges for the managed README header', function () {
    $head = (new Core)->readmeHead();

    expect($head->title())->toBe('Artisan Toolbox Core')
        ->and($head->logo())->toBe(['src' => 'logo/core-icon.svg', 'alt' => '', 'width' => 88])
        ->and(dirname(__DIR__, 2).'/logo/core-icon.svg')->toBeFile()
        ->and(array_map(static fn (Badge $badge): string => $badge->link(), $head->badges()))->toBe([
            'https://github.com/artisan-toolbox/core/releases',
            'https://packagist.org/packages/artisan-toolbox/core',
            'https://github.com/artisan-toolbox/core/actions',
        ]);
});
