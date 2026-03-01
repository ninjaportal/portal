<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NinjaPortal\Portal\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name'))
                $table->string('first_name');

            if (! Schema::hasColumn('users', 'last_name'))
                $table->string('last_name');

            if (! Schema::hasColumn('users', 'email'))
                $table->string('email')->unique();

            if (! Schema::hasColumn('users', 'password'))
                $table->string('password');

            if (! Schema::hasColumn('users', 'status'))
                $table->string('status')->default(config('ninjaportal.user.default_status', User::defaultStatus()));

            if (! Schema::hasColumn('users', 'sync_with_apigee'))
                $table->boolean('sync_with_apigee')->default(false);

            if (! Schema::hasColumn('users', 'custom_attributes'))
                $table->json('custom_attributes')->nullable();
            if (! Schema::hasColumn('users', 'email_verified_at'))
                $table->timestamp('email_verified_at')->nullable();
            if (! Schema::hasColumn('users', 'remember_token'))
                $table->rememberToken();
            if (! Schema::hasColumn('users', 'metadata'))
                $table->json('metadata')->nullable();
        });

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

    }

    public function down(): void
    {
        // disable foreign key checks
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
