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
      
      Schema::create('other_payment_types', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('code',3)->notNullable();
         $table->string('name',50)->notNullable();
         $table->unsignedTinyInteger('order')->unique()->notNullable();
         $table->enum('receiptAccount',['CUSTOMER','GENERAL LEDGER'])->default('CUSTOMER')->notNullable();
         $table->string('ledgerAccountNumber',50)->nullable();
         $table->enum('hasReference',['NO','YES'])->default('NO')->notNullable();
         $table->enum('type',['MOBILE','GENERAL'])->default('GENERAL')->notNullable();
         $table->string('prompt',150)->nullable();
         $table->timestamps();
         $table->unique(['client_id', 'name'],'client_payment_type');
         $table->unique(['client_id', 'order'],'client_payment_order');
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('other_payment_types');
   }
};
