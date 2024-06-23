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
      Schema::create('customer_field_update_details', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('customer_field_update_id',36)->notNullable();
         $table->string('customer_field_id',36)->notNullable();
         $table->string('value')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('customer_field_update_details');
   }
   
};
