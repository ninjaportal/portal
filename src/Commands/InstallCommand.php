<?php

namespace NinjaPortal\Portal\Commands;

use Illuminate\Console\Command;
use NinjaPortal\Portal\Seeders\settingsSeeder;
use NinjaPortal\Portal\PortalServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'poral:install';

    protected $description = 'Install the portal';

    public function handle(): void
    {
        $this->info('Installing the portal...');
        // publish and register the portal service provider
        $this->call('vendor:publish', [
            '--provider' => PortalServiceProvider::class,
            '--tag' => 'ninjaportal-provider',
        ]);

        $this->info('Portal service provider registered.');
        // publish and run the migrations
        $this->call('vendor:publish', [
            '--provider' => PortalServiceProvider::class,
            '--tag' => 'ninjaportal-migrations',
        ]);

        $this->info('Portal migrations published.');
        $this->call('migrate');

        $this->info('Portal migrations run.');
        // SEED SETTINGS AND SEETING GROUPS
        $this->call('db:seed', [
            '--class' => settingsSeeder::class,
        ]);



    }


}
