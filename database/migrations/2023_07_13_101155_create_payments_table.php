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
      Schema::create('payments', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->unsignedBigInteger('session_id')->nullable();
         $table->unsignedBigInteger('mno_id')->notNullable();
         $table->unsignedBigInteger('menu_id')->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',20)->notNullable();
         $table->string('district',50)->nullable();
         $table->string('reference',160)->nullable();
         $table->string('mnoTransactionId',30)->unique()->nullable();            
         $table->float('surchargeAmount',10,2)->default(0);
         $table->float('paymentAmount',10,2)->default(0);
         $table->float('receiptAmount',10,2)->default(0);
         $table->string('transactionId',50)->unique()->nullable();
         $table->string('receiptNumber',30)->unique()->nullable();
         $table->string('receipt',156)->nullable();
         $table->enum('channel',['USSD','MOBILEAPP','BANKAPI', 'WEBSITE'])
                              ->default('USSD')->notNullable();
         $table->enum('paymentStatus',['INITIATED','SUBMISSION FAILED','SUBMITTED',
                              'PAYMENT FAILED','PAID | NOT RECEIPTED','RECEIPTED',
                              'RECEIPT DELIVERED'])
                              ->default('INITIATED')->notNullable();
         $table->enum('status',['INITIATED','COMPLETED','FAILED','REVIEWED',
                              'MANUALLY REVIEWED'])->default('INITIATED')->notNullable();
         $table->text('error')->nullable();
         $table->unsignedBigInteger('user_id')->nullable();
         $table->timestamps();
         $table->unique(['session_id', 'mobileNumber'],'session_mobileNumber');
         $table->index(['client_id', 'paymentStatus', 'created_at']);
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('payments');
   }
};
