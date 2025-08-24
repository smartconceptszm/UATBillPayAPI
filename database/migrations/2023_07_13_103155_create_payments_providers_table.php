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
      Schema::create('payments_providers', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('name')->unique()->notNullable();
         $table->string('shortName')->unique()->notNullable();
         $table->string('colour')->nullable();
         $table->string('contactName')->nullable();
         $table->string('contactEmail')->nullable();
         $table->string('contactNo')->nullable();
         $table->string('logo')->nullable();
         $table->string('client_id')->nullable();
         $table->timestamps();
         $table->unique('shortName','indx_shortName');
         $table->unique('name','name_unique');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('payments_providers');
   }
};
