<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Illuminate\Support\Collection;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\DeveloperApp as ApigeeXDeveloperApp;
use Lordjoo\LaraApigee\Api\Edge\Entities\DeveloperApp as EdgeDeveloperApp;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppServiceInterface;
use Lordjoo\LaraApigee\Entities\EntityInterface;
use NinjaPortal\Portal\Contracts\Services\AppServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppServiceInterface;
use NinjaPortal\Portal\Events\UserApp\UserAppApprovedEvent;
use NinjaPortal\Portal\Events\UserApp\UserAppCreatedEvent;
use NinjaPortal\Portal\Events\UserApp\UserAppDeletedEvent;
use NinjaPortal\Portal\Events\UserApp\UserAppRevokedEvent;
use NinjaPortal\Portal\Events\UserApp\UserAppUpdatedEvent;
use NinjaPortal\Portal\Utils;

class UserAppService implements UserAppServiceInterface, AppServiceInterface
{
    use Traits\ServiceHooksAwareTrait;
    use Traits\ReportsServiceFailuresTrait;

    public function getModel(): string
    {
        return 'UserApp';
    }

    /**
     * @return Collection<EntityInterface>
     *
     * @throws Exception
     */
    public function all(string $email): Collection
    {
        try {
            return $this->api($email)->get();
        } catch (Exception $e) {
            $this->reportFailure('Failed to list developer apps.', compact('email'), $e);
            throw $e;
        }
    }

    /**
     * Get the API client for managing developer apps for a specific user.
     *
     * @throws Exception
     */
    protected function api(string $email): DeveloperAppServiceInterface
    {
        return Utils::getApigeeClient()->developerApps($email);
    }

    /**
     * Create a developer app for the user.
     */
    public function create(string $email, array $data): ?EntityInterface
    {
        $this->callHook('beforeCreate', [$data]);

        $developerAppEntity = $this->getEntity($data);

        if (! $developerAppEntity) {
            throw new Exception('Unsupported Apigee platform.');
        }

        $developerAppEntity->setDeveloperId($email);
        $developerAppEntity->setInitialApiProducts($data['apiProducts'] ?? []);

        try {
            $app = $this->api($email)->create($developerAppEntity);
        } catch (Exception $e) {
            $this->reportFailure('Failed to create developer app.', compact('email', 'data'), $e);
            throw $e;
        }

        $this->callHook('afterCreate', [$app]);
        UserAppCreatedEvent::dispatch($app, $email);

        return $app;
    }

    /**
     * Get the appropriate DeveloperApp entity based on the platform.
     */
    protected function getEntity(array $data): ?EntityInterface
    {
        return Utils::getPlatform() === 'edge' ? new EdgeDeveloperApp($data) :
            (Utils::getPlatform() === 'apigeex' ? new ApigeeXDeveloperApp($data) : null);
    }

    /**
     * Update a developer app for the user.
     */
    public function update(string $email, string $name, array $data): ?EntityInterface
    {
        $this->callHook('beforeUpdate', [$data]);

        $existing = $this->find($email, $name);
        $developerAppEntity = $this->getEntity($this->buildUpdatePayload($name, $data, $existing));

        if (! $developerAppEntity) {
            throw new Exception('Unsupported Apigee platform.');
        }

        $developerAppEntity->setDeveloperId($email);

        try {
            $app = $this->api($email)->update($name, $developerAppEntity);
        } catch (Exception $e) {
            $this->reportFailure('Failed to update developer app.', compact('email', 'name', 'data'), $e);
            throw $e;
        }

        $this->callHook('afterUpdate', [$app]);
        UserAppUpdatedEvent::dispatch($email, $app);

        return $app;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function buildUpdatePayload(string $name, array $data, ?EntityInterface $existing): array
    {
        $payload = [
            'name' => $name,
            'displayName' => $this->payloadString($data, 'displayName')
                ?? $this->entityString($existing, 'getDisplayName')
                ?? $name,
            'status' => strtolower(
                $this->payloadString($data, 'status')
                ?? $this->entityString($existing, 'getStatus')
                ?? 'approved'
            ),
        ];

        if (($apiProducts = $this->payloadArray($data, 'apiProducts')) !== null) {
            $payload['apiProducts'] = $apiProducts;
        }

        if (array_key_exists('callbackUrl', $data)) {
            $payload['callbackUrl'] = $data['callbackUrl'];
        } elseif ($existing && method_exists($existing, 'getCallbackUrl')) {
            $payload['callbackUrl'] = $existing->getCallbackUrl();
        }

        if (array_key_exists('description', $data)) {
            $payload['description'] = (string) ($data['description'] ?? '');
        } elseif (($description = $this->entityString($existing, 'getDescription')) !== null) {
            $payload['description'] = $description;
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function payloadString(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];

        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : (string) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, string>|null
     */
    protected function payloadArray(array $data, string $key): ?array
    {
        if (! array_key_exists($key, $data) || ! is_array($data[$key])) {
            return null;
        }

        return array_values(array_filter(array_map(static fn ($value): string => (string) $value, $data[$key])));
    }

    protected function entityString(?EntityInterface $entity, string $getter): ?string
    {
        if (! $entity || ! method_exists($entity, $getter)) {
            return null;
        }

        $value = $entity->{$getter}();

        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : (string) $value;
    }

    /**
     * Delete a developer app for the user.
     */
    public function delete(string $email, string $name): void
    {
        $this->callHook('beforeDelete', [$name]);

        $app = $this->find($email, $name);

        try {
            $this->api($email)->delete($name);
        } catch (Exception $e) {
            $this->reportFailure('Failed to delete developer app.', compact('email', 'name'), $e);
            throw $e;
        }

        $this->callHook('afterDelete', [$app]);
        if ($app) {
            UserAppDeletedEvent::dispatch($app, $email);
        }
    }

    /**
     * Find a developer app for the user by its name.
     */
    public function find(string $email, string $name): ?EntityInterface
    {
        try {
            return $this->api($email)->find($name);
        } catch (Exception $e) {
            $this->reportFailure('Failed to fetch developer app.', compact('email', 'name'), $e);
            throw $e;
        }
    }

    public function approve(string $email, string $name): ?EntityInterface
    {
        try {
            $this->api($email)->approve($name);
        } catch (Exception $e) {
            $this->reportFailure('Failed to approve developer app.', compact('email', 'name'), $e);
            throw $e;
        }

        $app = $this->find($email, $name);
        if ($app) {
            UserAppApprovedEvent::dispatch($email, $app);
        }

        return $app;
    }

    public function revoke(string $email, string $name): ?EntityInterface
    {
        try {
            $this->api($email)->revoke($name);
        } catch (Exception $e) {
            $this->reportFailure('Failed to revoke developer app.', compact('email', 'name'), $e);
            throw $e;
        }

        $app = $this->find($email, $name);
        if ($app) {
            UserAppRevokedEvent::dispatch($email, $app);
        }

        return $app;
    }
}
