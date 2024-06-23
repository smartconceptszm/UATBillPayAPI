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
        Schema::create('service_type_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_type_id',36)->notNullable();
            $table->string('name',50)->unique()->notNullable();
            $table->enum('type',['MOBILE','NATIONALID','GENERAL'])->default('GENERAL')->notNullable();
            $table->string('prompt',150)->nullable();
            $table->unsignedTinyInteger('order')->notNullable();
            $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_type_details');
    }
};
