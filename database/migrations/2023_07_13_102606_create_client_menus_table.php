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
      Schema::create('client_menus', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->string('code',25)->notNullable();
         $table->integer("order")->notNullable();
         $table->string('prompt',25)->notNullable();
         $table->string('description',150)->nullable();
         $table->enum('isPayment',['YES','NO'])->default('NO')->notNullable();
         $table->enum('isActive',['YES','NO'])->default('NO')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_mnos');
   }
};
