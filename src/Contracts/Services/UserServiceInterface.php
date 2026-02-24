<?php

namespace NinjaPortal\Portal\Contracts\Services;

use NinjaPortal\Portal\Models\User;

/**
 * User domain service contract.
 */
interface UserServiceInterface extends ServiceInterface
{
    /**
     * Locate a user by email address.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Trigger a password reset notification for the user with the given email.
     *
     * @throws \Exception when the user is not found.
     */
    public function requestPasswordReset(string $email): void;

    /**
     * Reset the user's password using the supplied token.
     */
    public function resetPassword(string $email, string $password, string $token): bool;

    /**
     * Update the user's password after validating the current one.
     *
     * @throws \Exception when the user is not found or the current password is invalid.
     */
    public function updatePassword(User|string|int $user, string $currentPassword, string $password): void;
}
