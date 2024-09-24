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
      
      Schema::create('dashboard_monthly_trends', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('client_id',36)->notNullable();
         $table->string('payments_provider_id',36)->notNullable();
         $table->string('year',4)->notNullable();
         $table->string('month',2)->notNullable();
         $table->unsignedInteger('numberOfTransactions')->nullable();
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','year','month'],'client_provider_year_month');
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_monthly_trends');
   }
};
