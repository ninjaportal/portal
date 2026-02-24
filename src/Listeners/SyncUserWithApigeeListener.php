<?php

namespace NinjaPortal\Portal\Listeners;

use Illuminate\Support\Facades\Log;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\Developer as ApigeeXDeveloper;
use Lordjoo\LaraApigee\Api\Edge\Entities\Developer as EdgeDeveloper;
use Lordjoo\LaraApigee\Entities\Structure\AttributesProperty;
use NinjaPortal\Portal\Events\User\UserCreatedEvent;
use NinjaPortal\Portal\Events\User\UserUpdatedEvent;
use NinjaPortal\Portal\Utils;
use Throwable;

/**
 * Class SyncUserWithApigeeListener
 *
 * This class syncs users with Apigee based on the platform (Edge or ApigeeX).
 */
class SyncUserWithApigeeListener
{
    /**
     * Handle the event.
     */
    public function handle(UserCreatedEvent|UserUpdatedEvent $event): void
    {
        $user = $event->user->refresh();
        $apigeeUser = $this->prepareApigeeUser($user);

        $action = $this->syncUser($user, $apigeeUser);

        if ($action === 'create') {
            Log::info('User created in Apigee', ['email' => $user->email]);
        } elseif ($action === 'update') {
            Log::info('User updated in Apigee', ['email' => $user->email]);
        }
    }

    /**
     * Prepares the Apigee user entity.
     *
     * @return EdgeDeveloper|ApigeeXDeveloper
     */
    protected function prepareApigeeUser($user)
    {
        if (Utils::getPlatform() == 'edge') {
            $model = EdgeDeveloper::class;
        } else {
            $model = ApigeeXDeveloper::class;
        }

        $apigeeUser = new $model([
            'email' => $user->email,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'userName' => $user->email,
            'attributes' => new AttributesProperty,
            'status' => $user->status,
        ]);

        foreach ($user->custom_attributes ?? [] as $key => $value) {
            $apigeeUser->setAttribute($key, $value);
        }

        return $apigeeUser;
    }

    /**
     * Syncs a user with Apigee.
     *
     * @return string Action performed: 'create', 'update', or 'error'
     */
    private function syncUser($user, EdgeDeveloper|ApigeeXDeveloper $apigeeUser): string
    {
        try {
            $client = $this->getClient();
            $email = $user->email;

            $existingUser = null;

            try {
                $existingUser = $client->developers()->find($email);
            } catch (Throwable $e) {
                if (! $this->isNotFoundException($e)) {
                    throw $e;
                }
            }

            if ($existingUser) {
                $client->developers()->update($email, $apigeeUser);
                $client->developers()->setStatus($email, $apigeeUser->getStatus());
                if (! $user->sync_with_apigee) {
                    $user->update(['sync_with_apigee' => true]);
                }

                return 'update';
            }

            $client->developers()->create($apigeeUser);
            $user->update(['sync_with_apigee' => true]);

            return 'create';
        } catch (Throwable $e) {
            Log::error('Error syncing user with Apigee', ['email' => $user->email, 'error' => $e->getMessage()]);

            return 'error';
        }
    }

    /**
     * Get the client based on the platform (Edge or ApigeeX).
     *
     * @return mixed ApigeeX or Edge client
     *
     * @throws \Exception
     */
    protected function getClient()
    {
        return Utils::getApigeeClient();
    }

    protected function isNotFoundException(Throwable $exception): bool
    {
        if ((int) $exception->getCode() === 404) {
            return true;
        }

        if (method_exists($exception, 'getResponse')) {
            $response = $exception->getResponse();
            if (is_object($response) && method_exists($response, 'getStatusCode')) {
                return (int) $response->getStatusCode() === 404;
            }
        }

        $previous = $exception->getPrevious();
        if ($previous instanceof Throwable && $previous !== $exception) {
            if ((int) $previous->getCode() === 404) {
                return true;
            }
        }

        $message = strtolower($exception->getMessage());

        return str_contains($message, '404')
            || str_contains($message, 'not found');
    }
}
