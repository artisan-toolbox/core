<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

use function ArtisanToolbox\Core\sift_when;

it('registers the sift macro on Laravel iterable utilities', function () {
    expect(Arr::hasMacro('sift'))->toBeTrue()
        ->and(Collection::hasMacro('sift'))->toBeTrue()
        ->and(LazyCollection::hasMacro('sift'))->toBeTrue();
});

it('keeps values whose conditions pass', function () {
    $result = Arr::sift([
        'name' => sift_when(true, 'Ada'),
        'active' => sift_when(true, false),
        'notes' => sift_when(true, null),
    ]);

    expect($result)->toBe([
        'name' => 'Ada',
        'active' => false,
        'notes' => null,
    ]);
});

it('resolves closures only when their conditions pass', function () {
    $resolved = 0;

    $result = Arr::sift([
        'kept' => sift_when(true, function () use (&$resolved): string {
            $resolved++;

            return 'ready';
        }),
        'discarded' => sift_when(false, function () use (&$resolved): string {
            $resolved++;

            return 'never';
        }),
    ]);

    expect($result)
        ->toBe(['kept' => 'ready'])
        ->and($resolved)->toBe(1);
});

it('recursively discards marked values while preserving supported nested iterable types', function () {
    $children = new Collection([
        'visible' => sift_when(true, 'yes'),
        'hidden' => sift_when(false, 'no'),
        'deep' => (function (): Generator {
            yield 'kept' => 42;
            yield 'discarded' => sift_when(false, 99);
        })(),
    ]);

    $result = Arr::sift([
        'root' => sift_when(false, 'discarded'),
        'children' => $children,
        'empty' => [sift_when(false, 'discarded')],
    ]);

    expect($result['children'])
        ->toBeInstanceOf(Collection::class)
        ->not->toBe($children)
        ->and($result['children']->all())->toBe([
            'visible' => 'yes',
            'deep' => $result['children']['deep'],
        ])
        ->and($result['children']['deep'])->toBeInstanceOf(Generator::class)
        ->and(iterator_to_array($result['children']['deep']))->toBe(['kept' => 42])
        ->and($result['empty'])->toBe([])
        ->and($children->has('hidden'))->toBeTrue();
});

it('preserves the keys of sifted iterables', function () {
    $result = Arr::sift([
        2 => 'first',
        5 => sift_when(false, 'discarded'),
        8 => 'last',
    ]);

    expect($result)->toBe([
        2 => 'first',
        8 => 'last',
    ]);
});

it('returns a new collection without mutating the original collection', function () {
    $original = new Collection([
        'kept' => sift_when(true, 'yes'),
        'discarded' => sift_when(false, 'no'),
    ]);

    $result = $original->sift();

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->not->toBe($original)
        ->and($result->all())->toBe(['kept' => 'yes'])
        ->and($original->has('discarded'))->toBeTrue();
});

it('returns a lazy collection without consuming the original', function () {
    $iterations = 0;
    $original = new LazyCollection(function () use (&$iterations): Generator {
        $iterations++;

        yield 'kept' => sift_when(true, 'yes');
        yield 'discarded' => sift_when(false, 'no');
    });

    $result = $original->sift();

    expect($result)
        ->toBeInstanceOf(LazyCollection::class)
        ->not->toBe($original)
        ->and($iterations)->toBe(0)
        ->and($result->all())->toBe(['kept' => 'yes'])
        ->and($iterations)->toBe(1);
});

it('preserves nested lazy collections without consuming them', function () {
    $iterations = 0;
    $nested = new LazyCollection(function () use (&$iterations): Generator {
        $iterations++;

        yield 'kept' => sift_when(true, 'yes');
        yield 'discarded' => sift_when(false, 'no');
    });

    $result = Arr::sift(['nested' => $nested]);

    expect($result['nested'])
        ->toBeInstanceOf(LazyCollection::class)
        ->not->toBe($nested)
        ->and($iterations)->toBe(0)
        ->and($result['nested']->all())->toBe(['kept' => 'yes'])
        ->and($iterations)->toBe(1);
});
