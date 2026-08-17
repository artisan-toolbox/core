<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Console\Commands;

use Illuminate\Console\Command;

#[\Illuminate\Console\Attributes\Description('Placeholder Artisan command shipped by the package core.')]
#[\Illuminate\Console\Attributes\Signature('core:placeholder')]
class CoreCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('Core placeholder command executed.');

        return self::SUCCESS;
    }
}
