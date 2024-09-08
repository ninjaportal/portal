<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Support\Facades\Password;
use NinjaPortal\Portal\Events\UserResetPasswordEvent;
use NinjaPortal\Portal\Models\User;

class UserService extends BaseService
{
    static protected string $model = User::class;

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

    public function updatePassword(User $user, array $data): void
    {
        if (!auth()->validate([
            "email" => $user->email,
            "password" => $data["current_password"],
        ])) {
            throw new \Exception("Invalid password");
        }
        $user->password = $data["password"];
        $user->save();
    }
}
