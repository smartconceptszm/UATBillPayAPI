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
      Schema::create('client_wallet_credentials', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("wallet_id",36)->notNullable();
         $table->string("key",50)->notNullable();
         $table->string("keyValue",150)->notNullable();
         $table->string("description",150)->nullable();
         $table->unique(['wallet_id','key'],'client_wallet_key');
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('group_rights');
   }
};
