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
      Schema::create('sessions', function (Blueprint $table) {
         $table->id();
         $table->string('sessionId',50)->notNullable();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->unsignedBigInteger('mno_id')->nullable();
         $table->unsignedBigInteger('menu_id')->nullable();
         $table->text('customerJourney')->notNullable();
         $table->string('accountNumber',20)->nullable();
         $table->float('paymentAmount',10,2)->default(0);
         $table->string('district',50)->nullable();
         $table->string('response',156)->nullable();
         $table->enum('status',['INITIATED','COMPLETED','FAILED','REVIEWED',
                                       'MANUALLY REVIEWED'])->default('INITIATED')->notNullable();
         $table->text('error')->nullable();
         $table->timestamps();
         $table->unique(['sessionId', 'mobileNumber'],'session_mobileNumber');
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('sessions');
   }
};
