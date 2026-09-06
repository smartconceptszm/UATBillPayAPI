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
      
      Schema::create('api_clients', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->enum('category',['PAYMENTS PROVIDER', 'SERVICE PROVIDER', 'OTHER'])->default('OTHER')->notNullable();
         $table->string('service_provider_id',36)->nullable();
         $table->string('payments_provider_id',36)->nullable();
         $table->string('username',25)->unique()->notNullable();
         $table->string('password',250)->notNullable();
         $table->string('shortName',25)->unique()->nullable();
         $table->string('name',50)->unique()->notNullable();
         $table->string('mobileNumber',12)->unique()->notNullable();
         $table->string('email',100)->unique()->nullable();
         $table->enum('channel',['MOBILEAPP', 'WHATSAPP','BANK-FULL', 'BANK-RECEIPTING'])->default('MOBILEAPP')->notNullable();
         $table->enum('status',['ACTIVE','REGISTERED','BLOCKED','SUSPENDED'])->default('REGISTERED')->notNullable();
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
