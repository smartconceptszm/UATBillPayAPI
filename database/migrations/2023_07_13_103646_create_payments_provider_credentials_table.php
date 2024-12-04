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
      Schema::create('payments_provider_credentials', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("payments_provider_id",36)->notNullable();
         $table->string("key",50)->notNullable();
         $table->string("keyValue",150)->notNullable();
         $table->string("description",150)->nullable();
         $table->timestamps();
         $table->unique(['payments_provider_id','key'],'paymentsProviderKey');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('payments_provider_credentials');
   }
};
