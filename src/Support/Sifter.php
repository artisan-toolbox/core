<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Support;

use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Traversable;

final class Sifter
{
    /**
     * Recursively sift an iterable into an array.
     *
     * @param  iterable<array-key, mixed>  $items
     * @return array<array-key, mixed>
     */
    public static function toArray(iterable $items): array
    {
        $sifted = [];

        foreach ($items as $key => $value) {
            if ($value instanceof SiftMarker) {
                continue;
            }

            $sifted[$key] = self::value($value);
        }

        return $sifted;
    }

    /**
     * Return a sifted copy of a collection.
     *
     * @param  Collection<array-key, mixed>  $items
     * @return Collection<array-key, mixed>
     */
    public static function collection(Collection $items): Collection
    {
        return $items
            ->filter(fn (mixed $value): bool => ! $value instanceof SiftMarker)
            ->map(fn (mixed $value): mixed => self::value($value));
    }

    /**
     * Return a lazy sifted view of a lazy collection.
     *
     * @param  LazyCollection<array-key, mixed>  $items
     * @return LazyCollection<array-key, mixed>
     */
    public static function lazyCollection(LazyCollection $items): LazyCollection
    {
        return $items
            ->filter(fn (mixed $value): bool => ! $value instanceof SiftMarker)
            ->map(fn (mixed $value): mixed => self::value($value));
    }

    private static function value(mixed $value): mixed
    {
        return match (true) {
            is_array($value) => self::toArray($value),
            $value instanceof LazyCollection => self::lazyCollection($value),
            $value instanceof Collection => self::collection($value),
            $value instanceof Traversable => self::traversable($value),
            default => $value,
        };
    }

    /**
     * Lazily sift a traversable whose concrete type cannot be safely reconstructed.
     *
     * @param  Traversable<array-key, mixed>  $items
     * @return Generator<array-key, mixed, mixed, void>
     */
    private static function traversable(Traversable $items): Generator
    {
        foreach ($items as $key => $value) {
            if ($value instanceof SiftMarker) {
                continue;
            }

            yield $key => self::value($value);
        }
    }
}
