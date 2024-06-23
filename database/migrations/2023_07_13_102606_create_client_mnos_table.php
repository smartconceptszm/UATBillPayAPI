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
         $table->string("client_id",36)->notNullable();
         $table->string("mno_id",36)->notNullable();
         $table->float('smsCharge',10,2)->default(0);
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
