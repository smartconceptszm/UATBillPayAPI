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
      Schema::create('dashboard_district_totals', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->unsignedInteger('year',4)->notNullable();
         $table->unsignedInteger('month',2)->notNullable();
         $table->string('district',150)->notNullable();
         $table->unsignedInteger('numberOfTransactions')->default(0);
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','year','month','district'],'client_district');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_district_totals');
   }
};
