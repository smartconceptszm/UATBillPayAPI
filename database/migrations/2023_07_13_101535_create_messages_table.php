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

      Schema::create('messages', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('customerAccount',20)->nullable();
         $table->string('message',160)->notNullable();
         $table->string("mno_id",36)->notNullable();
         $table->string("client_id",36)->notNullable();
         $table->string('bulk_id',36)->nullable();
         $table->string('transaction_id',50)->nullable();
         $table->float('amount',10,2)->default(0);
         $table->enum('type',['RECEIPT','SINGLE','BULK','BULKCUSTOM','NOTIFICATION'])->default('RECEIPT')->notNullable();
         $table->enum('status',['INITIATED','DELIVERED','FAILED'])->default('INITIATED')->notNullable();
         $table->string('user_id',36)->nullable();
         $table->text('error')->nullable();
         $table->timestamps();
         $table->index('mno_id');
         $table->index('client_id');
         $table->index('status');
         $table->index('created_at');

      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('messages');
   }
};
