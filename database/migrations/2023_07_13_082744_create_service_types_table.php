<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->notNullable();
            $table->uuid('code')->notNullable();
            $table->string('name',50)->notNullable();
            $table->unsignedTinyInteger('order')->notNullable();
            $table->enum('onExistingAccount',['YES','NO'])->default('NO')->notNullable();
            $table->string('description',150)->nullable();
            $table->timestamps();
            $table->unique(['client_id','code'],'client_service_type');
            $table->unique(['client_id','order'],'client_service_order');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
