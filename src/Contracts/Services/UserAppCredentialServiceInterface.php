<?php

namespace NinjaPortal\Portal\Contracts\Services;

/**
 * Developer app credential management contract.
 */
interface UserAppCredentialServiceInterface
{
    public function find(string $email, string $appName, string $key): mixed;

    public function create(string $email, string $appName, array $apiProducts, ?int $expiresIn = null): void;

    public function approve(string $email, string $appName, string $key): void;

    public function revoke(string $email, string $appName, string $key): void;

    public function delete(string $email, string $appName, string $key): void;

    public function addProducts(string $email, string $appName, string $key, array $apiProducts): void;

    public function removeProducts(string $email, string $appName, string $key, string $apiProduct): void;

    public function approveApiProduct(string $email, string $appName, string $key, string $apiProduct): void;

    public function revokeApiProduct(string $email, string $appName, string $key, string $apiProduct): void;
}
