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
      Schema::create('customer_fields', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('client_id',36)->notNullable();
         $table->string('name',50)->notNullable();
         $table->enum('type',['MOBILE','GENERAL'])->default('GENERAL')->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->string('prompt',150)->nullable();
         $table->timestamps();
         $table->unique(['client_id', 'name'],'client_customer_detail');
         $table->unique(['client_id', 'order'],'client_order');
   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_fields');
    }
};
