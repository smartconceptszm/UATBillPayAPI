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
      Schema::create('dashboard_daily_totals', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('client_id',36)->notNullable();
         $table->string('day',2)->notNullable();
         $table->unsignedBigInteger('numberOfTransactions')->nullable();
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','day'],'client_day');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_daily_totals');
   }
};
