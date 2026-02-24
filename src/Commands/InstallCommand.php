<?php

namespace NinjaPortal\Portal\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use NinjaPortal\Portal\PortalServiceProvider;
use NinjaPortal\Portal\Seeders\RbacSeeder;
use NinjaPortal\Portal\Seeders\SettingsSeeder;

class InstallCommand extends Command
{
    protected $signature = 'portal:install
        {--force-provider-overwrite : Overwrite App\\Providers\\NinjaPortalServiceProvider if it already exists}
        {--delete-default-users-migration : Delete Laravel\'s default users migration if present}';

    protected $description = 'Install the portal';

    public function handle(): void
    {
        $this->info('Installing the portal...');

        $forceProviderOverwrite = (bool) $this->option('force-provider-overwrite');
        $deleteDefaultUsersMigration = (bool) $this->option('delete-default-users-migration');

        // publish and register the portal service provider
        $this->publishStub(
            'NinjaPortalServiceProvider.stub',
            app_path('Providers/NinjaPortalServiceProvider.php'),
            $forceProviderOverwrite
        );
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
        $migrationPath = database_path('migrations/0001_01_01_000000_create_users_table.php');
        if ($deleteDefaultUsersMigration) {
            $confirmed = $this->confirm(
                'You requested deletion of Laravel\'s default users migration. Continue?',
                false
            );

            if ($confirmed) {
                if (file_exists($migrationPath)) {
                    unlink($migrationPath);
                    $this->info('Default user migration deleted.');
                } else {
                    $this->warn('Default user migration not found, skipping deletion.');
                }
            } else {
                $this->warn('Skipped deleting the default user migration.');
            }
        } elseif (file_exists($migrationPath)) {
            $this->warn(
                'Default Laravel users migration still exists. If your app uses the portal users table structure, '.
                're-run with --delete-default-users-migration to remove it intentionally.'
            );
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

        $this->call('db:seed', [
            '--class' => RbacSeeder::class,
        ]);
    }

    public function publishStub($stub, $path, $force = false, $var = [])
    {
        $stubFile = __DIR__.'/../../stubs/'.$stub;
        if (! file_exists($stubFile)) {
            $this->error('Stub file not found: '.$stubFile);

            return false;
        }
        $content = file_get_contents($stubFile);

        foreach ($var as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        if (! file_exists($path) || $force) {
            file_put_contents($path, $content);
            $this->info('File created: '.$path);
        } else {
            $this->warn('File already exists: '.$path);
        }
    }
}
