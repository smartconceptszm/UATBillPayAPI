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
      Schema::create('dashboard_hourly_totals', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->date('dateOfTransaction');
         $table->unsignedInteger('hour')->notNullable();
         $table->unsignedInteger('year',4)->notNullable();
         $table->unsignedInteger('month',2)->notNullable();
         $table->unsignedInteger('day')->notNullable();
         $table->unsignedInteger('numberOfTransactions')->default(0);
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','dateOfTransaction','hour'],'client_day_hour');
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
