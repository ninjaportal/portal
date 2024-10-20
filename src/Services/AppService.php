<?php

namespace NinjaPortal\Portal\Services;

use Lordjoo\LaraApigee\Api\ApigeeX\Entities\DeveloperApp as ApigeeXDeveloperApp;
use Lordjoo\LaraApigee\Api\Edge\Entities\App;
use Lordjoo\LaraApigee\Api\Edge\Entities\DeveloperApp as EdgeDeveloperApp;
use Lordjoo\LaraApigee\Entities\EntityInterface;
use NinjaPortal\Portal\Contracts\Services\ServiceInterface;
use NinjaPortal\Portal\Services\Traits\InteractsWithApigeeClient;
use Lordjoo\LaraApigee\Contracts\Services\AppServiceInterface;
use NinjaPortal\Portal\Utils;


class AppService implements ServiceInterface
{

    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    protected App $app;

    public function all(): array
    {
        return $this->api()->get();
    }

    public function find(string $name): ?EntityInterface
    {
        return $this->api()->find($name);
    }

    public function create(array $data): ?EntityInterface
    {
        $this->callHook('beforeCreate', [$data]);

        $developerAppEntity = $this->getEntity($data);

        $developerAppEntity->setDeveloperId($this->email);
        $developerAppEntity->setInitialApiProducts($data['apiProducts'] ?? []);

        $app = $this->api()->create($developerAppEntity);

        $this->callHook('afterCreate', [$app]);

        $this->fireEvent('created', [$app, $this->email]);

        $this->app = $app;
        return $app;
    }

    public function update(string $name, array $data): ?EntityInterface
    {
        $this->callHook('beforeUpdate', [$data]);


        $developerAppEntity =
            $this->getPlatform() === 'edge' ? new EdgeDeveloperApp($data) :
                ($this->getPlatform() === 'apigeex' ? new ApigeeXDeveloperApp($data) : null);

        $developerAppEntity->setDeveloperId($this->email);

        $app = $this->api()->update($name, $developerAppEntity);

        $this->callHook('afterUpdate', [$app]);

        $this->fireEvent('updated', [$this->email, $app]);

        $this->app = $app;

        return $app;
    }

    public function delete(string $name): void
    {
        $this->callHook('beforeDelete', [$name]);

        $app = $this->find($name);
        $this->api()->delete($name);

        $this->callHook('afterDelete', [$app]);

        $this->fireEvent('deleted', [$app, $this->email]);
    }

    protected function getEntity(array $data): ?EntityInterface
    {
        return Utils::getPlatform() === 'edge' ? new EdgeDeveloperApp($data) :
            (Utils::getPlatform() === 'apigee' ? new ApigeeXDeveloperApp($data) : null);
    }

    public static function getModel(): string
    {
        return "UserApp";
    }

    protected function api(): AppServiceInterface
    {
        return Utils::getApigeeClient()->apps();
    }


}
