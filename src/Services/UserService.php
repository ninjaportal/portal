<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use NinjaPortal\Portal\Contracts\Services\UserServiceInterface;
use NinjaPortal\Portal\Events\UserResetPasswordEvent;
use NinjaPortal\Portal\Models\User;
use NinjaPortal\Portal\Utils;

class UserService extends BaseService implements UserServiceInterface
{

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where("email", $email)->first();
    }

    public function requestPasswordReset(string $email): void
    {
        $user = $this->findByEmail($email);
        if ($user) {
            // TODO: Search if we should continue with this approach, or fire events instead
            $user->sendPasswordResetNotification($this->generatePasswordResetToken($user));
        } else {
            throw new \Exception("User not found");
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
            return Password::reset(
                [
                    "email" => $email,
                    "password" => $password,
                    "password_confirmation" => $password,
                    "token" => $token,
                ],
                function ($user, $password) {
                    $user->password = $password;
                    $user->save();

                    UserResetPasswordEvent::dispatch($user);
                }
            );
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

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception("Current password is incorrect");
        }

        $user->password = $password;
        $user->save();
    }


    public static function getModel(): string
    {
        return Utils::getUserModel();
    }
}
