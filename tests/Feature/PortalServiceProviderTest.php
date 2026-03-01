<?php

namespace NinjaPortal\Portal\Tests\Feature;

use BadMethodCallException;
use Illuminate\Support\Facades\Gate;
use NinjaPortal\Portal\Contracts\Services\AppServiceInterface;
use NinjaPortal\Portal\Contracts\Services\MenuItemServiceInterface;
use NinjaPortal\Portal\Contracts\Services\SettingServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppServiceInterface;
use NinjaPortal\Portal\Models\Category;
use NinjaPortal\Portal\Models\MenuItem;
use NinjaPortal\Portal\Policies\MenuItemPolicy;
use NinjaPortal\Portal\Repositories\CategoryRepository;
use NinjaPortal\Portal\Services\UserAppService;
use NinjaPortal\Portal\Tests\TestCase;

class PortalServiceProviderTest extends TestCase
{
    public function test_it_registers_core_service_bindings(): void
    {
        $this->assertInstanceOf(SettingServiceInterface::class, $this->app->make(SettingServiceInterface::class));
        $this->assertInstanceOf(MenuItemServiceInterface::class, $this->app->make(MenuItemServiceInterface::class));
    }

    public function test_it_registers_app_service_alias_binding(): void
    {
        $resolved = $this->app->make(AppServiceInterface::class);

        $this->assertInstanceOf(UserAppServiceInterface::class, $resolved);
        $this->assertInstanceOf(UserAppService::class, $resolved);
    }

    public function test_it_registers_menu_item_policy(): void
    {
        $policy = Gate::getPolicyFor(MenuItem::class);

        $this->assertInstanceOf(MenuItemPolicy::class, $policy);
    }

    public function test_restore_throws_clear_exception_for_models_without_soft_deletes(): void
    {
        $repository = new CategoryRepository(new Category);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('does not support restore()');

        $repository->restore(1);
    }

    public function test_core_models_do_not_expose_jwt_contract_methods(): void
    {
        $this->assertFalse(method_exists(new \NinjaPortal\Portal\Models\User, 'getJWTIdentifier'));
        $this->assertFalse(method_exists(new \NinjaPortal\Portal\Models\User, 'getJWTCustomClaims'));
        $this->assertFalse(method_exists(new \NinjaPortal\Portal\Models\Admin, 'getJWTIdentifier'));
        $this->assertFalse(method_exists(new \NinjaPortal\Portal\Models\Admin, 'getJWTCustomClaims'));
    }
}
