<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core;

use ArtisanToolbox\Core\Support\SiftMarker;
use Closure;

if (! function_exists(__NAMESPACE__.'\\sift_when')) {
    /**
     * Keep a value for Arr::sift when the given condition passes.
     *
     * @template TValue
     *
     * @param  TValue|Closure(): TValue  $value
     * @return TValue|SiftMarker
     */
    function sift_when(bool $condition, mixed $value): mixed
    {
        if (! $condition) {
            return SiftMarker::instance();
        }

        return $value instanceof Closure ? $value() : $value;
    }
}
