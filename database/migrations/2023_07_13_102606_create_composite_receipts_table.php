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

      Schema::create('composite_receipts', function (Blueprint $table) {
         $table->id();
         $table->string('client_id',36)->notNullable();
         $table->string('payment_id',36)->notNullable();
         $table->string('customerAccount',50)->nullable();
         $table->float('receiptAmount',10,2)->default(0);
         $table->string('receiptNumber',50)->nullable();
         $table->string('tokenNumber',150)->nullable();
         $table->enum('status',['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED',
                              'RECEIPT DELIVERED'])
                              ->default('PAID | NOT RECEIPTED')->notNullable();
         $table->text('error')->nullable();
         $table->timestamps();
         $table->unique(['client_id','customerAccount', 'payment_id'],'customer_payment');
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('composite_receipts');
   }
};
