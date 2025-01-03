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
         $table->uuid('id')->primary();
         $table->string('shortCode',10)->nullable();
         $table->string('shortName',25)->unique()->notNullable();
         $table->string('urlPrefix',25)->unique()->notNullable();
         $table->string('name',50)->unique()->notNullable();
         $table->string('ussdMenuText',50)->unique()->notNullable();
         $table->float('balance',10,2)->default(0);
         $table->enum('smsPayMode',['POST-PAID','PRE-PAID'])->default('POST-PAID')->notNullable();
         $table->enum('surcharge',['NO','YES'])->default('NO')->notNullable();
         $table->enum('mode',['UP','DOWN'])->default('UP')->notNullable();
         $table->enum('ussdAggregator',['NO','YES'])->default('NO')->notNullable();
         $table->text('testMSISDN')->nullable();
         $table->enum('status',['ACTIVE','BLOCKED'])->default('ACTIVE')->notNullable();
         $table->timestamps();
         $table->unique('shortName','clients_shortname_unique');
         $table->unique('urlPrefix','clients_urlprefix_unique');
         $table->unique('name','clients_name_unique');

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
