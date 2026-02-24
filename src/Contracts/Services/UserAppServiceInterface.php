<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Illuminate\Support\Collection;
use Lordjoo\LaraApigee\Entities\EntityInterface;

/**
 * Developer app management contract for Apigee-backed apps.
 */
interface UserAppServiceInterface
{
    /**
     * Retrieve all developer apps for a given user email.
     */
    public function all(string $email): Collection;

    /**
     * Locate a developer app by name.
     */
    public function find(string $email, string $name): ?EntityInterface;

    /**
     * Provision a new developer app.
     */
    public function create(string $email, array $data): ?EntityInterface;

    /**
     * Update an existing developer app's configuration.
     */
    public function update(string $email, string $name, array $data): ?EntityInterface;

    /**
     * Remove a developer app.
     */
    public function delete(string $email, string $name): void;

    /**
     * Approve a developer app for use.
     */
    public function approve(string $email, string $name): ?EntityInterface;

    /**
     * Revoke a developer app.
     */
    public function revoke(string $email, string $name): ?EntityInterface;
}
