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
      Schema::create('client_mnos', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->uuid("client_id")->notNullable();
         $table->uuid("mno_id")->notNullable();
         $table->float('smsCharge',10,2)->default(0);
         $table->enum('momoActive',['YES','NO'])->default('NO')->notNullable();
         $table->float('momoCommission',10,2)->default(0);
         $table->enum('momoMode',['UP','DOWN'])->default('UP')->notNullable();
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
