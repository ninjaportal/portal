<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Illuminate\Support\Collection;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\DeveloperApp as ApigeeXDeveloperApp;
use Lordjoo\LaraApigee\Api\Edge\Entities\DeveloperApp as EdgeDeveloperApp;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppServiceInterface;
use Lordjoo\LaraApigee\Entities\EntityInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppServiceInterface;
use NinjaPortal\Portal\Services\Traits\InteractsWithApigeeClient;
use NinjaPortal\Portal\Utils;


class UserAppService implements UserAppServiceInterface
{

    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    public static function getModel(): string
    {
        return "UserApp";
    }

    /**
     * @param string $email
     * @return Collection<EntityInterface>
     * @throws Exception
     */
    public function all(string $email): Collection
    {
        return $this->api($email)->get();
    }

    /**
     * Get the API client for managing developer apps for a specific user.
     *
     * @param string $email
     * @return DeveloperAppServiceInterface
     * @throws Exception
     */
    protected function api(string $email): DeveloperAppServiceInterface
    {
        return Utils::getApigeeClient()->developerApps($email);
    }

    /**
     * Create a developer app for the user.
     *
     * @param string $email
     * @param array $data
     * @return EntityInterface|null
     */
    public function create(string $email, array $data): ?EntityInterface
    {
        $this->callHook('beforeCreate', [$data]);

        $developerAppEntity = $this->getEntity($data);

        $developerAppEntity->setDeveloperId($email);
        $developerAppEntity->setInitialApiProducts($data['apiProducts'] ?? []);

        $app = $this->api($email)->create($developerAppEntity);

        $this->callHook('afterCreate', [$app]);
        $this->fireEvent('created', [$app, $email]);

        return $app;
    }

    /**
     * Get the appropriate DeveloperApp entity based on the platform.
     *
     * @param array $data
     * @return EntityInterface|null
     */
    protected function getEntity(array $data): ?EntityInterface
    {
        return Utils::getPlatform() === 'edge' ? new EdgeDeveloperApp($data) :
            (Utils::getPlatform() === 'apigeex' ? new ApigeeXDeveloperApp($data) : null);
    }

    /**
     * Update a developer app for the user.
     *
     * @param string $email
     * @param string $name
     * @param array $data
     * @return EntityInterface|null
     */
    public function update(string $email, string $name, array $data): ?EntityInterface
    {
        $this->callHook('beforeUpdate', [$data]);

        $developerAppEntity = $this->getEntity($data);
        $developerAppEntity->setDeveloperId($email);

        $app = $this->api($email)->update($name, $developerAppEntity);

        $this->callHook('afterUpdate', [$app]);
        $this->fireEvent('updated', [$email, $app]);

        return $app;
    }

    /**
     * Delete a developer app for the user.
     *
     * @param string $email
     * @param string $name
     * @return void
     */
    public function delete(string $email, string $name): void
    {
        $this->callHook('beforeDelete', [$name]);

        $app = $this->find($email, $name);
        $this->api($email)->delete($name);

        $this->callHook('afterDelete', [$app]);
        $this->fireEvent('deleted', [$app, $email]);
    }

    /**
     * Find a developer app for the user by its name.
     *
     * @param string $email
     * @param string $name
     * @return EntityInterface|null
     */
    public function find(string $email, string $name): ?EntityInterface
    {
        return $this->api($email)->find($name);
    }
}
