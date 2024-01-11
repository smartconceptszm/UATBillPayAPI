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
      Schema::create('service_application_details', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->uuid('service_application_id')->notNullable();
         $table->uuid('service_type_detail_id')->notNullable();
         $table->string('value')->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('service_application_details');
   }
};
