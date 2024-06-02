<?php

namespace Homeful\Property\Commands;

use Illuminate\Console\Command;

class PropertyCommand extends Command
{
    public $signature = 'property';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
