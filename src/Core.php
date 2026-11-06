<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core;

use ArtisanToolbox\Maintainer\Versionable\Contracts\Versionable;
use ArtisanToolbox\Maintainer\Versionable\Contracts\WithReadmeHead;
use ArtisanToolbox\Maintainer\Versionable\Readme\Badge;
use ArtisanToolbox\Maintainer\Versionable\Readme\Head;

class Core implements Versionable, WithReadmeHead
{
    public const string VERSION = '1.2.1';

    public function readmeHead(): Head
    {
        return (new Head)
            ->withLogo(src: 'logo/core-icon.svg', alt: '', width: 88)
            ->withTitle('Artisan Toolbox Core')
            ->withDescription('Shared foundations, contracts, utilities, and common components for Artisan Toolbox packages.')
            ->addBadge(Badge::make(
                link: 'https://github.com/artisan-toolbox/core/releases',
                imgSrc: 'https://img.shields.io/github/v/release/artisan-toolbox/core?label=Release&style=flat-square',
                imgAlt: 'Release',
            ))
            ->addBadge(Badge::make(
                link: 'https://atb.dev.br/packages/core/#requirements',
                imgSrc: 'https://img.shields.io/badge/PHP-%5E8.5-777BB4?style=flat-square',
                imgAlt: 'PHP',
            ))
            ->addBadge(Badge::make(
                link: 'https://atb.dev.br/packages/core/#requirements',
                imgSrc: 'https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square',
                imgAlt: 'Laravel',
            ))
            ->addBadge(Badge::make(
                link: 'https://github.com/artisan-toolbox/core/actions/workflows/tests.yml?query=branch%3A1.x',
                imgSrc: 'https://img.shields.io/github/actions/workflow/status/artisan-toolbox/core/tests.yml?branch=1.x&label=Tests&style=flat-square',
                imgAlt: 'Tests',
            ))
            ->addBadge(Badge::make(
                link: 'https://atb.dev.br/packages/core/',
                imgSrc: 'https://img.shields.io/badge/Docs-Read-blue?style=flat-square',
                imgAlt: 'Docs',
            ));
    }
}
