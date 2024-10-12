<?php

namespace NinjaPortal\Portal;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use NinjaPortal\Portal\Commands\InstallCommand;
use NinjaPortal\Portal\Policies\RolePolicy;
use NinjaPortal\Portal\Services\SettingService;
use NinjaPortal\Portal\Translatable\Locales;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Role;

class PortalServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('ninjaportal')
            ->hasCommands([
                InstallCommand::class
            ])
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->publishes([
            __DIR__ . '/Providers/BasePortalServiceProvider.php' => app_path('Providers/NinjaPortalServiceProvider.php'),
        ], 'ninjaportal-provider');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'ninjaportal-migrations');

        $this->registerTranslatableHelper();
    }

    protected function registerTranslatableHelper()
    {
        $this->app->singleton('translatable.locales', Locales::class);
        $this->app->singleton(Locales::class);
    }

    public function packageBooted()
    {

        // Load settings
        try {
            SettingService::loadAllSettings();
        } catch (\Exception $e) {
            Log::error("Failed to load settings: {$e->getMessage()}");
        }

        // Register Role Policy
        Gate::policy(Role::class, RolePolicy::class);


    }

}
