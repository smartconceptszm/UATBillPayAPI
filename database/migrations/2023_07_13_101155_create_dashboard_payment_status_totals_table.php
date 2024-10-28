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
      Schema::create('dashboard_payment_status_totals', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->enum('paymentStatus',['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED',
                        'RECEIPT DELIVERED'])
                        ->default('RECEIPT DELIVERED')->notNullable();
         $table->date('dateOfTransaction');
         $table->unsignedInteger('year')->notNullable();
         $table->unsignedInteger('month')->notNullable();
         $table->unsignedInteger('day',2)->notNullable();
         $table->unsignedInteger('numberOfTransactions')->nullable();
         $table->float('totalAmount',10,2)->default(0);
         $table->timestamps();
         $table->unique(['client_id','paymentStatus','dateOfTransaction'],'client_paymentstatus_day');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_payments_status_totals');
   }
};
