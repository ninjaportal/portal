<?php

namespace NinjaPortal\Portal\Commands;

use Illuminate\Console\Command;
use NinjaPortal\Portal\Seeders\RbacSeeder;
use NinjaPortal\Portal\Seeders\SettingsSeeder;
use NinjaPortal\Portal\Seeders\Demo\DatabaseDemoSeeder;

class SeedCommand extends Command
{
    protected $signature = 'portal:seed
        {--settings : Seed portal settings and setting groups}
        {--rbac : Seed portal RBAC roles and permissions}
        {--demo : Seed demo data (users, categories, audiences, products)}
        {--all : Seed everything}';

    protected $description = 'Seed portal data (settings, RBAC, demo).';

    public function handle(): int
    {
        $runAll = (bool) $this->option('all');
        $runSettings = $runAll || (bool) $this->option('settings');
        $runRbac = $runAll || (bool) $this->option('rbac');
        $runDemo = $runAll || (bool) $this->option('demo');

        if (! $runSettings && ! $runRbac && ! $runDemo) {
            $runSettings = true;
            $runRbac = true;
        }

        if ($runSettings) {
            $this->info('Seeding portal settings...');
            $this->call('db:seed', ['--class' => SettingsSeeder::class]);
        }

        if ($runRbac) {
            $this->info('Seeding portal RBAC...');
            $this->call('db:seed', ['--class' => RbacSeeder::class]);
        }

        if ($runDemo) {
            $this->info('Seeding portal demo data...');
            $this->call('db:seed', ['--class' => DatabaseDemoSeeder::class]);
        }

        $this->info('Portal seeding completed.');

        return self::SUCCESS;
    }
}
