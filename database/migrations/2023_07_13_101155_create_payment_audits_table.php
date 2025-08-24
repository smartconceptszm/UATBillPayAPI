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
      Schema::create('payment_audits', function (Blueprint $table) {
         $table->id();
         $table->string('payment_id',36);
         $table->json('oldValues')->nullable();
         $table->json('newValues')->nullable();
         $table->string('user_id')->nullable();
         $table->string('updateChannel')->nullable();
         $table->timestamps();
     });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('payment_audits');
   }
};
