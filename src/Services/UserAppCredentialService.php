<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppCredentialsServiceInterface;
use NinjaPortal\Portal\Contracts\Services\ServiceInterface;
use NinjaPortal\Portal\Utils;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UserAppCredentialService implements ServiceInterface
{
    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    protected string $email;
    protected string $app_name;

    public function __construct(string $email, string $app_name)
    {
        $this->email = $email;
        $this->app_name = $app_name;
    }

    public function find($key)
    {
        return $this->api()->load($key);
    }

    public function create(array $apiProducts, ?int $expiresIn = null): void
    {
        $this->api()->generate($apiProducts, $expiresIn ?? '-1');
        $this->fireEvent('created', [$this->email, $this->app_name]);
    }

    public function approve(string $key): void
    {
        $this->api()->approve($key);
        $this->fireEvent('approved', [$this->email, $this->app_name]);
    }

    public function revoke(string $key): void
    {
        $this->api()->revoke($key);
        $this->fireEvent('revoked', [$this->email, $this->app_name]);
    }

    public function delete(string $key): void
    {
        $this->api()->delete($key);
        $this->fireEvent('deleted', [$this->email, $this->app_name]);
    }


    /**
     * @throws Exception
     */
    protected function api(): DeveloperAppCredentialsServiceInterface
    {
        return Utils::getApigeeClient()->developerAppCredentials($this->email, $this->app_name);
    }

    /**
     * @throws ExceptionInterface
     */
    public function addProducts($key, array $api_products): void
    {
        $this->api()->addProducts($key, $api_products);
        $this->fireEvent('productAdded', [$this->email, $this->app_name, $api_products]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function removeProducts($key, string $api_product): void
    {
        $this->api()->deleteApiProduct($key, $api_product);
        $this->fireEvent('productRemoved', [$this->email, $this->app_name, $api_product]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function approveApiProduct($key, string $api_product): void
    {
        $this->api()->setApiProductStatus($key, $api_product, 'approve');
        $this->fireEvent('productApproved', [$this->email, $this->app_name, $api_product]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function revokeApiProduct($key, string $api_product): void
    {
        $this->api()->setApiProductStatus($key, $api_product, 'revoke');
        $this->fireEvent('productRevoked', [$this->email, $this->app_name, $api_product]);
    }


    public static function getModel(): string
    {
        return 'UserAppCredential';
    }

}
