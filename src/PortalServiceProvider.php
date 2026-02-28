<?php

namespace NinjaPortal\Portal;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use NinjaPortal\Portal\Commands\InstallCommand;
use NinjaPortal\Portal\Commands\SeedCommand;
use NinjaPortal\Portal\Contracts\Repositories\AdminRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\ApiProductRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\AudienceRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\CategoryRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\MenuItemRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\MenuRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\PermissionRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\RoleRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\SettingGroupRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\SettingRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\UserRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AdminServiceInterface;
use NinjaPortal\Portal\Contracts\Services\ApiProductServiceInterface;
use NinjaPortal\Portal\Contracts\Services\AppServiceInterface;
use NinjaPortal\Portal\Contracts\Services\AudienceServiceInterface;
use NinjaPortal\Portal\Contracts\Services\CategoryServiceInterface;
use NinjaPortal\Portal\Contracts\Services\MenuItemServiceInterface;
use NinjaPortal\Portal\Contracts\Services\MenuServiceInterface;
use NinjaPortal\Portal\Contracts\Services\PermissionServiceInterface;
use NinjaPortal\Portal\Contracts\Services\RoleServiceInterface;
use NinjaPortal\Portal\Contracts\Services\SettingGroupServiceInterface;
use NinjaPortal\Portal\Contracts\Services\SettingServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppCredentialServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserServiceInterface;
use NinjaPortal\Portal\Events\User\UserPasswordResetRequestedEvent;
use NinjaPortal\Portal\Listeners\SendUserPasswordResetNotificationListener;
use NinjaPortal\Portal\Policies\AdminPolicy;
use NinjaPortal\Portal\Policies\ApiProductPolicy;
use NinjaPortal\Portal\Policies\AudiencePolicy;
use NinjaPortal\Portal\Policies\CategoryPolicy;
use NinjaPortal\Portal\Policies\MenuItemPolicy;
use NinjaPortal\Portal\Policies\MenuPolicy;
use NinjaPortal\Portal\Policies\PermissionPolicy;
use NinjaPortal\Portal\Policies\RolePolicy;
use NinjaPortal\Portal\Policies\SettingGroupPolicy;
use NinjaPortal\Portal\Policies\SettingPolicy;
use NinjaPortal\Portal\Policies\UserPolicy;
use NinjaPortal\Portal\Providers\Concerns\RegistersBindings;
use NinjaPortal\Portal\Repositories\AdminRepository;
use NinjaPortal\Portal\Repositories\ApiProductRepository;
use NinjaPortal\Portal\Repositories\AudienceRepository;
use NinjaPortal\Portal\Repositories\CategoryRepository;
use NinjaPortal\Portal\Repositories\MenuItemRepository;
use NinjaPortal\Portal\Repositories\MenuRepository;
use NinjaPortal\Portal\Repositories\PermissionRepository;
use NinjaPortal\Portal\Repositories\RoleRepository;
use NinjaPortal\Portal\Repositories\SettingGroupRepository;
use NinjaPortal\Portal\Repositories\SettingRepository;
use NinjaPortal\Portal\Repositories\UserRepository;
use NinjaPortal\Portal\Services\AdminService;
use NinjaPortal\Portal\Services\ApiProductService;
use NinjaPortal\Portal\Services\AudienceService;
use NinjaPortal\Portal\Services\CategoryService;
use NinjaPortal\Portal\Services\MenuItemService;
use NinjaPortal\Portal\Services\MenuService;
use NinjaPortal\Portal\Services\PermissionService;
use NinjaPortal\Portal\Services\RoleService;
use NinjaPortal\Portal\Services\SettingGroupService;
use NinjaPortal\Portal\Services\SettingService;
use NinjaPortal\Portal\Services\UserAppCredentialService;
use NinjaPortal\Portal\Services\UserAppService;
use NinjaPortal\Portal\Services\UserService;
use NinjaPortal\Portal\Translatable\Locales;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PortalServiceProvider extends PackageServiceProvider
{
    use RegistersBindings;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('ninjaportal')
            ->hasCommands([
                InstallCommand::class,
                SeedCommand::class,
            ])
            ->hasConfigFile();
    }

    public function register()
    {
        parent::register();

        $this->registerRepositories();
        $this->registerServices();
    }

    public function packageRegistered()
    {
        $this->publishes([
            __DIR__.'/Providers/BasePortalServiceProvider.php' => app_path('Providers/NinjaPortalServiceProvider.php'),
        ], 'ninjaportal-provider');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'ninjaportal-migrations');

        $this->registerTranslatableHelper();
    }

    protected function registerTranslatableHelper(): void
    {
        $this->app->singleton('translatable.locales', Locales::class);
        $this->app->singleton(Locales::class);
    }

    protected array $serviceBindings = [
        ApiProductServiceInterface::class => ApiProductService::class,
        AudienceServiceInterface::class => AudienceService::class,
        CategoryServiceInterface::class => CategoryService::class,
        AdminServiceInterface::class => AdminService::class,
        AppServiceInterface::class => UserAppService::class,
        MenuServiceInterface::class => MenuService::class,
        MenuItemServiceInterface::class => MenuItemService::class,
        PermissionServiceInterface::class => PermissionService::class,
        RoleServiceInterface::class => RoleService::class,
        SettingServiceInterface::class => SettingService::class,
        SettingGroupServiceInterface::class => SettingGroupService::class,
        UserServiceInterface::class => UserService::class,
        UserAppServiceInterface::class => UserAppService::class,
        UserAppCredentialServiceInterface::class => UserAppCredentialService::class,
    ];

    protected array $repositoryBindings = [
        [
            'interface' => AdminRepositoryInterface::class,
            'implementation' => AdminRepository::class,
            'model' => [
                'config:ninjaportal.models.Admin',
                \NinjaPortal\Portal\Models\Admin::class,
            ],
        ],
        [
            'interface' => ApiProductRepositoryInterface::class,
            'implementation' => ApiProductRepository::class,
            'model' => [
                'config:ninjaportal.models.ApiProduct',
                \NinjaPortal\Portal\Models\ApiProduct::class,
            ],
        ],
        [
            'interface' => AudienceRepositoryInterface::class,
            'implementation' => AudienceRepository::class,
            'model' => [
                'config:ninjaportal.models.Audience',
                \NinjaPortal\Portal\Models\Audience::class,
            ],
        ],
        [
            'interface' => CategoryRepositoryInterface::class,
            'implementation' => CategoryRepository::class,
            'model' => [
                'config:ninjaportal.models.Category',
                \NinjaPortal\Portal\Models\Category::class,
            ],
        ],
        [
            'interface' => MenuRepositoryInterface::class,
            'implementation' => MenuRepository::class,
            'model' => [
                'config:ninjaportal.models.Menu',
                \NinjaPortal\Portal\Models\Menu::class,
            ],
        ],
        [
            'interface' => MenuItemRepositoryInterface::class,
            'implementation' => MenuItemRepository::class,
            'model' => [
                'config:ninjaportal.models.MenuItem',
                \NinjaPortal\Portal\Models\MenuItem::class,
            ],
        ],
        [
            'interface' => PermissionRepositoryInterface::class,
            'implementation' => PermissionRepository::class,
            'model' => [
                'config:ninjaportal.models.Permission',
                Permission::class,
            ],
        ],
        [
            'interface' => RoleRepositoryInterface::class,
            'implementation' => RoleRepository::class,
            'model' => [
                'config:ninjaportal.models.Role',
                Role::class,
            ],
        ],
        [
            'interface' => SettingRepositoryInterface::class,
            'implementation' => SettingRepository::class,
            'model' => [
                'config:ninjaportal.models.Setting',
                \NinjaPortal\Portal\Models\Setting::class,
            ],
        ],
        [
            'interface' => SettingGroupRepositoryInterface::class,
            'implementation' => SettingGroupRepository::class,
            'model' => [
                'config:ninjaportal.models.SettingGroup',
                \NinjaPortal\Portal\Models\SettingGroup::class,
            ],
        ],
        [
            'interface' => UserRepositoryInterface::class,
            'implementation' => UserRepository::class,
            'model' => [
                'config:ninjaportal.models.User',
                \NinjaPortal\Portal\Models\User::class,
            ],
        ],
    ];

    protected array $policyBindings = [
        'config:ninjaportal.models.Admin' => AdminPolicy::class,
        'config:ninjaportal.models.ApiProduct' => ApiProductPolicy::class,
        'config:ninjaportal.models.Audience' => AudiencePolicy::class,
        'config:ninjaportal.models.Category' => CategoryPolicy::class,
        'config:ninjaportal.models.Menu' => MenuPolicy::class,
        'config:ninjaportal.models.MenuItem' => MenuItemPolicy::class,
        'config:ninjaportal.models.Permission' => PermissionPolicy::class,
        'config:ninjaportal.models.Role' => RolePolicy::class,
        'config:ninjaportal.models.Setting' => SettingPolicy::class,
        'config:ninjaportal.models.SettingGroup' => SettingGroupPolicy::class,
        'config:ninjaportal.models.User' => UserPolicy::class,
    ];

    public function packageBooted()
    {
        Event::listen(
            UserPasswordResetRequestedEvent::class,
            SendUserPasswordResetNotificationListener::class
        );

        try {
            $this->app->make(SettingServiceInterface::class)->loadAllSettings();
        } catch (\Exception $e) {
            Log::error("Failed to load settings: {$e->getMessage()}");
        }

        $this->registerPolicies();
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policyBindings as $model => $policy) {
            Gate::policy($this->resolvePolicyModelClass($model), $policy);
        }
    }

    protected function resolvePolicyModelClass(string $model): string
    {
        if (str_starts_with($model, 'config:')) {
            $configured = config(substr($model, 7));
            if (is_string($configured) && $configured !== '') {
                return $configured;
            }
        }

        return $model;
    }
}
