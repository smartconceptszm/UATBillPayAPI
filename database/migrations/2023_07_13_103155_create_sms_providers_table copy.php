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
      Schema::create('sms_providers', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('name')->unique()->notNullable(); 
         $table->string('handler')->nullable();
         $table->string('colour')->nullable();
         $table->string('contactName')->nullable();
         $table->string('contactEmail')->nullable();
         $table->string('contactNo')->nullable();
         $table->string('logo')->nullable();
         $table->timestamps();
         $table->unique('name','sms_providers_name_unique');
         $table->unique('handler','sms_providers_handler_unique');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('sms_providers');
   }
};
