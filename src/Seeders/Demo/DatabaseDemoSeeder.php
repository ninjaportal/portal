<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;

class DatabaseDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            AdminSeeder::class,
            AudienceSeeder::class,
            CategorySeeder::class,
            ApiProductSeeder::class,
            UserSeeder::class,
        ]);
    }
}
