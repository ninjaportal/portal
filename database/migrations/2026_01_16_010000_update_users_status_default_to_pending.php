<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use NinjaPortal\Portal\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'status')) {
            return;
        }

        // Avoid Schema::change() (requires doctrine/dbal). Use raw SQL instead.
        DB::statement("ALTER TABLE `users` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT '".config('ninjaportal.user.default_status', User::defaultStatus())."'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT '".User::activeStatus()."'");
    }
};

