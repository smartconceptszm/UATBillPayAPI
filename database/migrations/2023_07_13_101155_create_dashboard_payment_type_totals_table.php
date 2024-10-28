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
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->string('paymentType',150)->notNullable();
         $table->date('dateOfTransaction');
         $table->unsignedInteger('year')->notNullable();
         $table->unsignedInteger('month')->notNullable();
         $table->unsignedInteger('day',2)->notNullable();
         $table->unsignedInteger('numberOfTransactions')->default(0);
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','paymentType','dateOfTransaction',],'client_menu_day');
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
