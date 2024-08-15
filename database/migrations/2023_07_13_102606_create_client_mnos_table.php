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

      Schema::create('client_mnos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("client_id",36)->notNullable();
            $table->string("mno_id",36)->notNullable();
            $table->enum('smsMode',['UP','DOWN'])->default('DOWN')->notNullable();
            $table->enum('smsActive',['YES','NO'])->default('NO')->notNullable();
            $table->string('handler',50)->notNullable();
            $table->float('smsCharge',10,2)->default(0);
            $table->timestamps();
            $table->unique(['client_id', 'mno_id'],'ClientMNO');
         });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_mnos');
   }
};
