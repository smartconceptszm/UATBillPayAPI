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
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->unsignedBigInteger("user_id")->notNullable();
         $table->string('sourceFile')->nullable();
         $table->string('description')->nullable();
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
