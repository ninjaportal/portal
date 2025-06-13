<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppCredentialsServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppCredentialServiceInterface;
use NinjaPortal\Portal\Utils;

class UserAppCredentialService implements UserAppCredentialServiceInterface
{
    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    /**
     * Find the credential by key.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     * @return mixed
     */
    public function find(string $email, string $app_name, string $key)
    {
        return $this->api($email, $app_name)->load($key);
    }

    /**
     * Create a new credential.
     *
     * @param string $email
     * @param string $app_name
     * @param array $apiProducts
     * @param int|null $expiresIn
     */
    public function create(string $email, string $app_name, array $apiProducts, ?int $expiresIn = null): void
    {
        $this->api($email, $app_name)->generate($apiProducts, $expiresIn ?? '-1');
        $this->fireEvent('created', [$email, $app_name]);
    }

    /**
     * Approve a credential by key.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     */
    public function approve(string $email, string $app_name, string $key): void
    {
        $this->api($email, $app_name)->approve($key);
        $this->fireEvent('approved', [$email, $app_name]);
    }

    /**
     * Revoke a credential by key.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     */
    public function revoke(string $email, string $app_name, string $key): void
    {
        $this->api($email, $app_name)->revoke($key);
        $this->fireEvent('revoked', [$email, $app_name]);
    }

    /**
     * Delete a credential by key.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     */
    public function delete(string $email, string $app_name, string $key): void
    {
        $this->api($email, $app_name)->delete($key);
        $this->fireEvent('deleted', [$email, $app_name]);
    }

    /**
     * Add products to a credential.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     * @param array $api_products
     * @throws Exception
     */
    public function addProducts(string $email, string $app_name, string $key, array $api_products): void
    {
        $this->api($email, $app_name)->addProducts($key, $api_products);
        $this->fireEvent('productAdded', [$email, $app_name, $api_products]);
    }

    /**
     * Remove a product from a credential.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     * @param string $api_product
     * @throws Exception
     */
    public function removeProducts(string $email, string $app_name, string $key, string $api_product): void
    {
        $this->api($email, $app_name)->deleteApiProduct($key, $api_product);
        $this->fireEvent('productRemoved', [$email, $app_name, $api_product]);
    }

    /**
     * Approve an API product for a credential.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     * @param string $api_product
     * @throws Exception
     */
    public function approveApiProduct(string $email, string $app_name, string $key, string $api_product): void
    {
        $this->api($email, $app_name)->setApiProductStatus($key, $api_product, 'approve');
        $this->fireEvent('productApproved', [$email, $app_name, $api_product]);
    }

    /**
     * Revoke an API product for a credential.
     *
     * @param string $email
     * @param string $app_name
     * @param string $key
     * @param string $api_product
     * @throws Exception
     */
    public function revokeApiProduct(string $email, string $app_name, string $key, string $api_product): void
    {
        $this->api($email, $app_name)->setApiProductStatus($key, $api_product, 'revoke');
        $this->fireEvent('productRevoked', [$email, $app_name, $api_product]);
    }

    /**
     * Get the DeveloperAppCredentialsServiceInterface.
     *
     * @param string $email
     * @param string $app_name
     * @return DeveloperAppCredentialsServiceInterface
     * @throws Exception
     */
    protected function api(string $email, string $app_name): DeveloperAppCredentialsServiceInterface
    {
        return Utils::getApigeeClient()->developerAppCredentials($email, $app_name);
    }

    /**
     * Get the model associated with the service.
     *
     * @return string
     */
    public static function getModel(): string
    {
        return 'UserAppCredential';
    }
}
