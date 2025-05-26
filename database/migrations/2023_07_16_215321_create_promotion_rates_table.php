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
      Schema::create('promotion_rates', function (Blueprint $table) {
         $table->id();
         $table->unsignedInteger('promotion_id');
         $table->unsignedInteger('band');                      
         $table->float('minAmount',10,2)->nullable();
         $table->float('maxAmount',10,2)->nullable();
         $table->float('rate',10,2)->nullable();
         $table->string('name',150)->nullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('promotion_rates');
   }
   
};
