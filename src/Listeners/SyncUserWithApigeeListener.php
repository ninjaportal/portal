<?php

namespace NinjaPortal\Portal\Listeners;

use Illuminate\Support\Facades\Log;
use Lordjoo\LaraApigee\Entities\Structure\AttributesProperty;
use Lordjoo\LaraApigee\Api\Edge\Entities\Developer as EdgeDeveloper;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\Developer as ApigeeXDeveloper;
use NinjaPortal\Portal\Events\UserCreatedEvent;
use NinjaPortal\Portal\Events\UserUpdatedEvent;
use NinjaPortal\Portal\Utils;

/**
 * Class SyncUserWithApigeeListener
 *
 * This class syncs users with Apigee based on the platform (Edge or ApigeeX).
 */
class SyncUserWithApigeeListener
{
    /**
     * Handle the event.
     *
     * @param UserCreatedEvent|UserUpdatedEvent $event
     * @return void
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
     * @param $user
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
            'attributes' => new AttributesProperty(),
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
     * @param $user
     * @param EdgeDeveloper|ApigeeXDeveloper $apigeeUser
     * @return string Action performed: 'create', 'update', or 'error'
     */
    private function syncUser($user, EdgeDeveloper|ApigeeXDeveloper $apigeeUser): string
    {
        try {
            $client = $this->getClient();
            $email = $user->email;

            try {
                $existingUser = $client->developers()->find($email);
                if ($existingUser) {
                    $client->developers()->update($email, $apigeeUser);
                    $client->developers()->setStatus($email, $apigeeUser->getStatus());
                    if (!$user->sync_with_apigee) {
                        $user->update(['sync_with_apigee' => true]);
                    }
                    return 'update';
                }
            } catch (\Exception $e) {
                // User not found, create a new one
                $newApigeeUser = $client->developers()->create($apigeeUser);
                $user->update(['sync_with_apigee' => true]);
                return 'create';
            }
        } catch (\Exception $e) {
            Log::error('Error syncing user with Apigee', ['email' => $user->email, 'error' => $e->getMessage()]);
            return 'error';
        }
    }

    /**
     * Get the client based on the platform (Edge or ApigeeX).
     *
     * @return mixed ApigeeX or Edge client
     * @throws \Exception
     */
    protected function getClient()
    {
        return Utils::getApigeeClient();
    }
}
