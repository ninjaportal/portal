<?php

namespace NinjaPortal\Portal\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use NinjaPortal\Portal\Seeders\SettingsSeeder;
use NinjaPortal\Portal\PortalServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'poral:install';

    protected $description = 'Install the portal';

    public function handle(): void
    {
        $this->info('Installing the portal...');
        // publish and register the portal service provider
        $this->publishStub('NinjaPortalServiceProvider.stub', app_path('Providers/NinjaPortalServiceProvider.php'), true);
        ServiceProvider::addProviderToBootstrapFile(
            'App\Providers\NinjaPortalServiceProvider',
            $this->laravel->getBootstrapProvidersPath(),
        );

        // publish spatie/laravel-permission migrations
        $this->info('Publishing permission migrations...');
        $this->call('vendor:publish', [
            '--tag' => 'permission-migrations',
        ]);


        // publish and run the migrations
        $overwrite = $this->confirm("Default user migration will be deleted. Do you want to continue?");
        if ($overwrite) {
            // unlink default user migration
            $migrationPath = database_path('migrations/0001_01_01_000000_create_users_table.php');
            if (file_exists($migrationPath)) {
                unlink($migrationPath);
            }
            $this->info('Default user migration deleted.');
        } else {
            $this->warn('Be advised that the default user migration could cause conflicts.');
        }
        $this->info('Publish and run the portal migrations.');
        $this->call('vendor:publish', [
            '--provider' => PortalServiceProvider::class,
            '--tag' => 'ninjaportal-migrations',
        ]);
        $this->call('migrate');

        // publish config
        $this->call('vendor:publish', [
            '--provider' => PortalServiceProvider::class,
            '--tag' => 'ninjaportal-config',
        ]);


        // SEED SETTINGS AND SEETING GROUPS
        $this->call('db:seed', [
            '--class' => SettingsSeeder::class,
        ]);


    }


    public function publishStub($stub, $path, $force = false,$var = [])
    {
        $stubFile = __DIR__ . '/../../stubs/'. $stub;
        if (!file_exists($stubFile)) {
            $this->error('Stub file not found: ' . $stubFile);
            return false;
        }
        $content = file_get_contents($stubFile);

        foreach ($var as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        if (!file_exists($path) || $force) {
            file_put_contents($path, $content);
            $this->info('File created: ' . $path);
        } else {
            $this->warn('File already exists: ' . $path);
        }
    }


}
