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
         $table->uuid('id')->primary();
         $table->string('session_id',36)->notNullable();
         $table->string('wallet_id',36)->notNullable();
         $table->string('menu_id',36)->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('walletNumber',50)->nullable();
         $table->string('customerAccount',50)->nullable();
         $table->string('district',50)->nullable();
         $table->string('reference',160)->nullable();
         $table->string('ppTransactionId',30)->nullable();            
         $table->float('surchargeAmount',10,2)->default(0);
         $table->float('paymentAmount',10,2)->default(0);
         $table->float('receiptAmount',10,2)->default(0);
         $table->string('transactionId',50)->nullable();
         $table->string('receiptNumber',30)->nullable();
         $table->string('tokenNumber',30)->nullable();
         $table->string('receipt',255)->nullable();
         $table->enum('channel',['USSD','MOBILEAPP','BANKAPI', 'WEBSITE'])
                              ->default('USSD')->notNullable();
         $table->enum('paymentStatus',['INITIATED','SUBMISSION FAILED','SUBMITTED','PAYMENT FAILED',
                              'PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED',
                              'RECEIPT DELIVERED'])
                              ->default('INITIATED')->notNullable();
         $table->enum('status',['INITIATED','COMPLETED','FAILED','SUCCESSFUL','REVIEWED',
                              'MANUALLY REVIEWED'])->default('INITIATED')->notNullable();
         $table->text('error')->nullable();
         $table->string('user_id',36)->nullable();
         $table->timestamps();
         $table->index(['client_id','paymentStatus','receiptAmount','wallet_id','created_at'],'paymentsSearch');
         $table->index('customerAccount');
         $table->index('mobileNumber');
         $table->index('session_id');
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
