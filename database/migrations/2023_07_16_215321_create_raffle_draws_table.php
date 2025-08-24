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
      Schema::create('promotions', function (Blueprint $table) {
         $table->id();
         $table->integer('promotion_id');
         $table->date('dateOfDraw');
         $table->unsignedInteger('year')->nullable();
         $table->unsignedInteger('month')->nullable();
         $table->unsignedInteger('day',2)->nullable();
         $table->date('drawStart');
         $table->date('drawEnd');
         $table->unsignedInteger('numberOfDraws')->nullable();
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('promotions');
   }
   
};
