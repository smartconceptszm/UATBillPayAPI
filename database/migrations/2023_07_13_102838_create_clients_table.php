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
      Schema::create('clients', function (Blueprint $table) {
         $table->id();
         $table->string('code',10)->unique()->notNullable();
         $table->string('shortName',25)->unique()->notNullable();
         $table->string('urlPrefix',25)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->float('balance',10,2)->default(0);
         $table->enum('smsPayMode',['POST-PAID','PRE-PAID'])->default('POST-PAID')->notNullable();
         $table->enum('surcharge',['NO','YES'])->default('NO')->notNullable();
         $table->enum('mode',['UP','DOWN'])->default('UP')->notNullable();
         $table->enum('status',['ACTIVE','BLOCKED'])->default('ACTIVE')->notNullable();
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('clients');
   }
};
