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
      Schema::create('mno_charges', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("client_id")->notNullable();
         $table->unsignedBigInteger("mno_id")->notNullable();
         $table->float('momoCommission',10,2)->default(0);
         $table->float('smsCharge',10,2)->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('mno_charges');
   }
};
