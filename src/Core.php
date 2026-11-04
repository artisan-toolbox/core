<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core;

use ArtisanToolbox\Maintainer\Versionable\Contracts\Versionable;
use ArtisanToolbox\Maintainer\Versionable\Contracts\WithReadmeHead;
use ArtisanToolbox\Maintainer\Versionable\Readme\Badge;
use ArtisanToolbox\Maintainer\Versionable\Readme\Head;

class Core implements Versionable, WithReadmeHead
{
    public const string VERSION = '1.1.0';

    public function readmeHead(): Head
    {
        return (new Head)
            ->withLogo(src: 'logo/core-icon.svg', alt: '', width: 88)
            ->withTitle('Artisan Toolbox Core')
            ->withDescription('Shared foundations, contracts, utilities, and common components for Artisan Toolbox packages.')
            ->addBadge(Badge::releaseVersion('https://github.com/artisan-toolbox/core/releases'))
            ->addBadge(Badge::make(
                link: 'https://packagist.org/packages/artisan-toolbox/core',
                imgSrc: 'https://img.shields.io/packagist/v/artisan-toolbox/core.svg?style=flat-square',
                imgAlt: 'Packagist',
            ))
            ->addBadge(Badge::make(
                link: 'https://github.com/artisan-toolbox/core/actions',
                imgSrc: 'https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square',
                imgAlt: 'Tests',
            ));
    }
}
