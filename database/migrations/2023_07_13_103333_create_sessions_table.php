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
         $table->uuid('id')->primary();
         $table->uuid('client_id')->notNullable();
         $table->uuid('mno_id')->nullable();
         $table->uuid('menu_id')->nullable();
         $table->string('sessionId',50)->notNullable();
         $table->text('customerJourney')->nullable();
         $table->string('mobileNumber',12)->notNullable();
         $table->string('accountNumber',50)->nullable();
         $table->string('district',50)->nullable();
         $table->float('paymentAmount',10,2)->default(0);
         $table->string('response',160)->nullable();
         $table->enum('status',['INITIATED','COMPLETED','FAILED','SUCCESSFUL','REVIEWED',
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
