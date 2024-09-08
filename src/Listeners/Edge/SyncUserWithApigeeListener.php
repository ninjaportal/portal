<?php

namespace NinjaPortal\Portal\Listeners\Edge;

use Illuminate\Support\Facades\Log;
use Lordjoo\LaraApigee\Api\Edge\Entities\Developer;
use Lordjoo\LaraApigee\Entities\Structure\AttributesProperty;
use Lordjoo\LaraApigee\Facades\LaraApigee;
use NinjaPortal\Portal\Events\UserCreatedEvent;
use NinjaPortal\Portal\Events\UserUpdatedEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class SyncUserWithApigeeListener
 *
 * @package NinjaPortal\Portal\Listeners\Edge
 */
class SyncUserWithApigeeListener
{
    /**
     * Handle the event.
     *
     * @param UserCreatedEvent|UserUpdatedEvent $event
     * @return void
     * @throws ExceptionInterface
     */
    public function handle(UserCreatedEvent|UserUpdatedEvent $event): void
    {
        $user = $event->user->refresh();
        $apigeeUser = new Developer([
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

        $action = $this->syncUser($user, $apigeeUser);

        if ($action === 'create') {
            Log::info('User created in Apigee', ['email' => $user->email]);
        } elseif ($action === 'update') {
            Log::info('User updated in Apigee', ['email' => $user->email]);
        }
    }

    /**
     * Syncs a user with Apigee.
     *
     * @param string $email
     * @param Developer $apigeeUser
     * @return string Action performed: 'create', 'update', or 'error'
     * @throws ExceptionInterface
     */
    private function syncUser($user, Developer $apigeeUser): string
    {
        try {
            $email = $user->email;
            $existingUser = LaraApigee::edge()->developers()->find($email);

            if ($existingUser) {
                LaraApigee::edge()->developers()->update($email, $apigeeUser);
                LaraApigee::edge()->developers()->setStatus($email, $apigeeUser->getStatus());
                if (!$user->apigee_id) {
                    $user->update(['apigee_id' => $existingUser->getDeveloperId()]);
                }
                return 'update';
            }

            /** @var Developer $newApigeeUser */
            $newApigeeUser = LaraApigee::edge()->developers()->create($apigeeUser);
            $user->update(['apigee_id' => $newApigeeUser->getDeveloperId()]);
            return 'create';
        } catch (\Exception $e) {
            Log::error('Error syncing user with Apigee', ['email' => $email, 'error' => $e->getMessage()]);
            return 'error';
        }
    }
}
