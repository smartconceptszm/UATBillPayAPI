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
      Schema::create('bulk_messages', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("client_id",36)->notNullable();
         $table->string("user_id",36)->notNullable();
         $table->json('mobileNumbers')->nullable();
         $table->string('message')->nullable();
         $table->enum('type',['BULK','BULKCUSTOM'])->default('BULK')->notNullable();
         $table->timestamps();
     });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('bulk_messages');
   }
};
