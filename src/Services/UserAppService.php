<?php

namespace NinjaPortal\Portal\Services;

use Lordjoo\LaraApigee\Entities\EntityInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppServiceInterface;
use NinjaPortal\Portal\Services\Traits\InteractsWithApigeeClient;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppServiceInterface;
use NinjaPortal\Portal\Utils;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\DeveloperApp as ApigeeXDeveloperApp;
use Lordjoo\LaraApigee\Api\Edge\Entities\DeveloperApp as EdgeDeveloperApp;


class UserAppService implements UserAppServiceInterface
{

    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    public function all(string $email): array
    {
        return $this->api($email)->get();
    }

    /**
     * Create a developer app for the user.
     *
     * @param string $email
     * @param array $data
     * @return \Lordjoo\LaraApigee\Entities\EntityInterface|null
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
     * Update a developer app for the user.
     *
     * @param string $email
     * @param string $name
     * @param array $data
     * @return \Lordjoo\LaraApigee\Entities\EntityInterface|null
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
     * @return \Lordjoo\LaraApigee\Entities\EntityInterface|null
     */
    public function find(string $email, string $name): ?EntityInterface
    {
        return $this->api($email)->find($name);
    }

    public static function getModel(): string
    {
        return "UserApp";
    }

    /**
     * Get the API client for managing developer apps for a specific user.
     *
     * @param string $email
     * @return DeveloperAppServiceInterface
     * @throws \Exception
     */
    protected function api(string $email): DeveloperAppServiceInterface
    {
        return Utils::getApigeeClient()->developerApps($email);
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
}
