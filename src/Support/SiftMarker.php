<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Support;

/**
 * @internal
 */
final class SiftMarker
{
    private static ?self $instance = null;

    private function __construct() {}

    public static function instance(): self
    {
        return self::$instance ??= new self;
    }
}
