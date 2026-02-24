<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppCredentialsServiceInterface;
use NinjaPortal\Portal\Contracts\Services\UserAppCredentialServiceInterface;
use NinjaPortal\Portal\Utils;

class UserAppCredentialService implements UserAppCredentialServiceInterface
{
    use Traits\FireEventsTrait;
    use Traits\ReportsServiceFailuresTrait;
    use Traits\ServiceHooksAwareTrait;

    /**
     * Find the credential by key.
     */
    public function find(string $email, string $appName, string $key): mixed
    {
        return $this->api($email, $appName)->load($key);
    }

    /**
     * Create a new credential.
     */
    public function create(string $email, string $appName, array $apiProducts, ?int $expiresIn = null): void
    {
        $generated = [];

        try {
            $generated = $this->api($email, $appName)->generate($apiProducts, $expiresIn ?? '-1');
        } catch (Exception $e) {
            $this->reportFailure('Failed to create developer app credential.', compact('email', 'appName', 'apiProducts'), $e);
            throw $e;
        }

        $this->fireEvent('created', [$email, $appName, $this->extractCredentialKey($generated)]);
    }

    /**
     * Approve a credential by key.
     */
    public function approve(string $email, string $appName, string $key): void
    {
        try {
            $this->api($email, $appName)->approve($key);
        } catch (Exception $e) {
            $this->reportFailure('Failed to approve developer app credential.', compact('email', 'appName', 'key'), $e);
            throw $e;
        }
        $this->fireEvent('approved', [$email, $appName, $key]);
    }

    /**
     * Revoke a credential by key.
     */
    public function revoke(string $email, string $appName, string $key): void
    {
        try {
            $this->api($email, $appName)->revoke($key);
        } catch (Exception $e) {
            $this->reportFailure('Failed to revoke developer app credential.', compact('email', 'appName', 'key'), $e);
            throw $e;
        }
        $this->fireEvent('revoked', [$email, $appName, $key]);
    }

    /**
     * Delete a credential by key.
     */
    public function delete(string $email, string $appName, string $key): void
    {
        try {
            $this->api($email, $appName)->delete($key);
        } catch (Exception $e) {
            $this->reportFailure('Failed to delete developer app credential.', compact('email', 'appName', 'key'), $e);
            throw $e;
        }
        $this->fireEvent('deleted', [$email, $appName, $key]);
    }

    /**
     * Add products to a credential.
     *
     * @throws Exception
     */
    public function addProducts(string $email, string $appName, string $key, array $api_products): void
    {
        try {
            $this->api($email, $appName)->addProducts($key, $api_products);
        } catch (Exception $e) {
            $this->reportFailure('Failed to add API products to credential.', compact('email', 'appName', 'key', 'api_products'), $e);
            throw $e;
        }
        $this->fireEvent('productAdded', [$email, $appName, $api_products, $key]);
    }

    /**
     * Remove a product from a credential.
     *
     * @throws Exception
     */
    public function removeProducts(string $email, string $appName, string $key, string $api_product): void
    {
        try {
            $this->api($email, $appName)->deleteApiProduct($key, $api_product);
        } catch (Exception $e) {
            $this->reportFailure('Failed to remove API product from credential.', compact('email', 'appName', 'key', 'api_product'), $e);
            throw $e;
        }
        $this->fireEvent('productRemoved', [$email, $appName, $api_product, $key]);
    }

    /**
     * Approve an API product for a credential.
     *
     * @throws Exception
     */
    public function approveApiProduct(string $email, string $appName, string $key, string $api_product): void
    {
        try {
            $this->api($email, $appName)->setApiProductStatus($key, $api_product, 'approve');
        } catch (Exception $e) {
            $this->reportFailure('Failed to approve API product on credential.', compact('email', 'appName', 'key', 'api_product'), $e);
            throw $e;
        }
        $this->fireEvent('productApproved', [$email, $appName, $api_product, $key]);
    }

    /**
     * Revoke an API product for a credential.
     *
     * @throws Exception
     */
    public function revokeApiProduct(string $email, string $appName, string $key, string $api_product): void
    {
        try {
            $this->api($email, $appName)->setApiProductStatus($key, $api_product, 'revoke');
        } catch (Exception $e) {
            $this->reportFailure('Failed to revoke API product on credential.', compact('email', 'appName', 'key', 'api_product'), $e);
            throw $e;
        }
        $this->fireEvent('productRevoked', [$email, $appName, $api_product, $key]);
    }

    /**
     * Get the DeveloperAppCredentialsServiceInterface.
     *
     * @throws Exception
     */
    protected function api(string $email, string $appName): DeveloperAppCredentialsServiceInterface
    {
        return Utils::getApigeeClient()->developerAppCredentials($email, $appName);
    }

    public function getModel(): string
    {
        return 'UserAppCredential';
    }

    /**
     * @param  array<int|string, mixed>  $generated
     */
    protected function extractCredentialKey(array $generated): ?string
    {
        foreach ($generated as $credential) {
            if (is_object($credential)) {
                foreach (['getConsumerKey', 'consumerKey', 'id', 'getId'] as $accessor) {
                    if (method_exists($credential, $accessor)) {
                        $value = $credential->{$accessor}();
                        if (is_string($value) && $value !== '') {
                            return $value;
                        }
                    }
                }

                foreach (['consumerKey', 'consumer_key'] as $property) {
                    if (isset($credential->{$property}) && is_string($credential->{$property}) && $credential->{$property} !== '') {
                        return $credential->{$property};
                    }
                }
            }

            if (is_array($credential)) {
                foreach (['consumerKey', 'consumer_key'] as $key) {
                    $value = $credential[$key] ?? null;
                    if (is_string($value) && $value !== '') {
                        return $value;
                    }
                }
            }
        }

        return null;
    }
}
