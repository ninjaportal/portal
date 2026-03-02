<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Models\Audience;
use NinjaPortal\Portal\Models\User as PortalUser;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Jade',
                'last_name' => 'Summers',
                'email' => 'jade.summers@ninjaportal.test',
                'password' => 'password',
                'status' => PortalUser::activeStatus(),
                'email_verified_at' => now(),
                'sync_with_apigee' => false,
                'custom_attributes' => [
                    'company' => 'Skyline Retail',
                    'job_title' => 'Lead Developer',
                ],
                'audiences' => ['Retail Developers'],
            ],
            [
                'first_name' => 'Marco',
                'last_name' => 'Diaz',
                'email' => 'marco.diaz@ninjaportal.test',
                'password' => 'password',
                'status' => PortalUser::activeStatus(),
                'email_verified_at' => now()->subDay(),
                'sync_with_apigee' => true,
                'custom_attributes' => [
                    'company' => 'Northwind Partners',
                    'job_title' => 'Integration Architect',
                ],
                'audiences' => ['Partner Integrators'],
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Nair',
                'email' => 'priya.nair@ninjaportal.test',
                'password' => 'password',
                'status' => PortalUser::defaultStatus(),
                'sync_with_apigee' => false,
                'custom_attributes' => [
                    'company' => 'Ninja Portal',
                    'job_title' => 'Product Manager',
                ],
                'audiences' => ['Internal Teams'],
            ],
        ];

        foreach ($users as $data) {
            $audienceNames = $data['audiences'] ?? [];
            unset($data['audiences']);

            $user = PortalUser::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            if (! empty($audienceNames)) {
                $audienceIds = Audience::whereIn('name', $audienceNames)->pluck('id')->all();
                $user->audiences()->sync($audienceIds);
            }
        }
    }
}
