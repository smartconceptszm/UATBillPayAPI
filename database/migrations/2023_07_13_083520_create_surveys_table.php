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
      
      Schema::create('surveys', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->string('description',150)->nullable();
         $table->enum('isActive',['YES','NO'])->default('NO')->notNullable();
         $table->timestamps();
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('surveys');
   }

};
