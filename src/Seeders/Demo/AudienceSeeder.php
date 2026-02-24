<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Models\Audience;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        $audiences = [
            'Retail Developers',
            'Partner Integrators',
            'Internal Teams',
        ];

        foreach ($audiences as $name) {
            Audience::updateOrCreate(['name' => $name]);
        }
    }
}
