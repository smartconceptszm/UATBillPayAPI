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
      Schema::create('client_wallets', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("client_id",36)->notNullable();
         $table->string("payments_provider_id",36)->notNullable();
         $table->enum('paymentMethod',['MOMO','CARD'])->default('MOMO')->notNullable();
         $table->enum('paymentsActive',['YES','NO'])->default('NO')->notNullable();
         $table->float('paymentsCommission',10,2)->default(0);
         $table->enum('paymentsMode',['UP','DOWN'])->default('UP')->notNullable();
         $table->string('modeMessage',155)->nullable();
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
