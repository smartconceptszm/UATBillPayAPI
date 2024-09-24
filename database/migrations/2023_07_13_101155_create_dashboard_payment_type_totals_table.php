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
      Schema::create('dashboard_payment_type_totals', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('client_id',36)->notNullable();
         $table->string('menu_id',36)->notNullable();
         $table->string('day',2)->notNullable();
         $table->unsignedInteger('numberOfTransactions')->nullable();
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_payment_type_totals');
   }
};
