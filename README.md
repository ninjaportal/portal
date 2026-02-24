# NinjaPortal Portal Package

Core domain package for NinjaPortal.

It provides:

- Portal data models (users, categories, API products, audiences, menus, settings)
- Repository + service abstractions
- Translatable model support
- Apigee-backed developer app + credential services
- Core events/policies/RBAC seeding

## Install

From the Laravel app (backend):

```bash
php artisan portal:install
```

Safer install options:

```bash
php artisan portal:install --force-provider-overwrite
php artisan portal:install --delete-default-users-migration
```

Notes:

- The installer does not overwrite `App\Providers\NinjaPortalServiceProvider` unless you pass `--force-provider-overwrite`.
- The installer does not delete Laravel's default users migration unless you pass `--delete-default-users-migration`.

## Seeding

```bash
php artisan portal:seed --all
php artisan portal:seed --settings --rbac
php artisan portal:seed --demo
```

## RBAC And Policies

The package now standardizes model-policy permissions using:

```text
portal.{model}.{ability}
```

Examples:

- `portal.user.view_any`
- `portal.user.update`
- `portal.api_product.create`
- `portal.setting_group.delete`

Route/API level permissions remain:

- `portal.admin.access`
- `portal.rbac.manage`
- `portal.admins.manage`
- `portal.activities.view`

The `RbacSeeder` seeds both route-level permissions and model-policy permissions.

## Service Implementation Standards

### 1. Eloquent-backed domain services

Use this pattern:

- Extend `BaseService`
- Implement a `...ServiceInterface`
- Use `CrudOperationsTrait`
- Inject a repository interface in the constructor
- Keep business-specific methods small and focused

Examples:

- `src/Services/UserService.php`
- `src/Services/ApiProductService.php`
- `src/Services/SettingService.php`

### 2. External/Apigee-backed services

Use this pattern:

- Implement a dedicated interface (not `ServiceInterface`)
- Wrap LaraApigee service calls
- Use consistent error logging via `ReportsServiceFailuresTrait`
- Emit portal domain events after successful mutations

Examples:

- `src/Services/UserAppService.php`
- `src/Services/UserAppCredentialService.php`

## Event Naming Standard

Preferred event naming is:

```text
{DomainThing}{Action}Event
```

Examples:

- `UserCreatedEvent`
- `ApiProductUpdatedEvent`
- `UserAppCredentialCreatedEvent`

Publishing policy:

- `...Event` is the canonical and only supported class name
- do not add no-suffix event classes

`FireEventsTrait` dispatches the `...Event` class only.

Credential-related events may include `credentialKey` when the operation targets a
specific credential (approve/revoke/delete/product mutations). Generated-key events
include it when the upstream Apigee client returns the created key.

## App-Level Wiring (Listeners / Observers)

The package publishes an app provider stub:

- `App\Providers\NinjaPortalServiceProvider`

Use it to wire project-specific listeners/observers (for example syncing users to Apigee on create/update).

## Testing And Static Checks (Package)

After installing package dev dependencies:

```bash
composer install
composer test
composer analyse
composer format
```

## Known Tradeoffs (Current)

- Migration rollback safety is not guaranteed for every migration path.
- Demo seeders are intended for one-time/demo environments and are not guaranteed to be re-runnable.
- `restore()` is only supported for models using Eloquent `SoftDeletes`; other models
  will throw a clear exception if `restore()` is called.
