<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_product_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('thumbnail')->nullable();

            $table->foreignId('api_product_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->unique(['api_product_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_product_translations');
    }
};
