<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ArtisanToolbox\Core\Core
 */
class Core extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ArtisanToolbox\Core\Core::class;
    }
}
