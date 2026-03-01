<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use NinjaPortal\Portal\Contracts\Repositories\UserRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\UserServiceInterface;
use NinjaPortal\Portal\Events\User\UserAudiencesSyncedEvent;
use NinjaPortal\Portal\Events\User\UserPasswordResetRequestedEvent;
use NinjaPortal\Portal\Events\User\UserResetPasswordEvent;
use NinjaPortal\Portal\Models\User;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;

/**
 * @mixin Traits\HasRepositoryAwareTrait<User, UserRepositoryInterface>
 */
class UserService extends BaseService implements UserServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    protected function mutateDataBeforeCreate(array $data): array
    {
        if (! array_key_exists('status', $data) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = User::defaultStatus();
        }

        return $data;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository()->findByEmail($email);
    }

    public function requestPasswordReset(string $email): void
    {
        $user = $this->findByEmail($email);
        if ($user) {
            UserPasswordResetRequestedEvent::dispatch(
                $user,
                $this->generatePasswordResetToken($user)
            );
        } else {
            throw new \Exception('User not found');
        }
    }

    public function generatePasswordResetToken(User $user): string
    {
        return Password::createToken($user);
    }

    public function resetPassword(
        string $email,
        string $password,
        string $token
    ): bool {
        $user = $this->findByEmail($email);
        if ($user) {
            $status = Password::reset(
                [
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $password,
                    'token' => $token,
                ],
                function ($user, $password) {
                    $user->password = $password;
                    $user->save();

                    UserResetPasswordEvent::dispatch($user);
                }
            );

            return $status === Password::PASSWORD_RESET;
        }

        return false;
    }

    public function updatePassword(User|string|int $user, string $currentPassword, string $password): void
    {
        if (is_string($user)) {
            $user = $this->findByEmail($user);
        } elseif (is_int($user)) {
            $user = $this->find($user);
        }

        if (! $user) {
            throw new \Exception('User not found');
        }

        if (! Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->password = $password;
        $user->save();
    }

    public function syncAudiences(User|string|int $user, array $audienceIds): User
    {
        $user = $this->repository()->resolve($user);

        $this->callHook('beforeSyncAudiences', [$user, $audienceIds]);

        $user->audiences()->sync($audienceIds);
        $user->load('audiences');

        $this->callHook('afterSyncAudiences', [$user, $audienceIds]);

        UserAudiencesSyncedEvent::dispatch($user, $audienceIds);

        return $user;
    }
}
