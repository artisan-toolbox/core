<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core;

use ArtisanToolbox\Core\Console\Commands\CoreCommand;
use ArtisanToolbox\Core\Support\Sifter;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'core');

        $this->app->singleton(Core::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Arr::macro('sift', fn (iterable $items): array => Sifter::toArray($items));

        Collection::macro('sift', function (): Collection {
            return Sifter::collection($this);
        });

        LazyCollection::macro('sift', function (): LazyCollection {
            return Sifter::lazyCollection($this);
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/core.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'core');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'core');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/core.php' => config_path('core.php'),
        ], ['core', 'core-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/core'),
        ], ['core', 'core-views']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/core'),
        ], ['core', 'core-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/core'),
        ], ['core', 'core-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['core', 'core-migrations']);

        $this->commands([
            CoreCommand::class,
        ]);
    }
}
