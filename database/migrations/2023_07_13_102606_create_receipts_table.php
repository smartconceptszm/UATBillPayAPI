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

      Schema::create('receipts', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->string('payment_id',36)->notNullable();
         $table->string('description',150)->nullable();
         $table->timestamps();
         $table->index(['client_id', 'payment_id'],'client_payment');
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('receipts');
   }
};
