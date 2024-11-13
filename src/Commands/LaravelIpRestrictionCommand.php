<?php

namespace DigitalCoreHub\LaravelIpRestriction\Commands;

use Illuminate\Console\Command;

class LaravelIpRestrictionCommand extends Command
{
    public $signature = 'laravel-ip-restriction';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
