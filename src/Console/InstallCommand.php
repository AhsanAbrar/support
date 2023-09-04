<?php

namespace AhsanDev\Support\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Support resources';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Publishing Support Migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'support-migrations']);

        $this->info('Support scaffolding installed successfully.');
    }
}
