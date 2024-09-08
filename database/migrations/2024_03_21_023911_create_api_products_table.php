<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('apigee_product_id')->unique();
            $table->string('swagger_url')->nullable();
            $table->string('visibility')->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_products');
    }
};
