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

      Schema::create('api_users', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string('api_client_id',36)->notNullable();
         $table->string('payments_provider_id',36)->notNullable();
         $table->string('service_provider_id',36)->notNullable();
         $table->string('username',25)->unique()->notNullable();
         $table->string('password',250)->notNullable();
         $table->enum('status',['REGISTERED','ACTIVE','BLOCKED'])->default('ACTIVE')->notNullable();
         $table->timestamps();
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('users');
   }

};
