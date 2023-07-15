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

      Schema::create('users', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('username',25)->unique()->notNullable();
         $table->string('password',250)->notNullable();
         $table->string('fullnames',100)->notNullable();
         $table->string('mobileNumber',12)->unique()->notNullable();
         $table->string('email',100)->unique()->notNullable();
         $table->enum('status',['REGISTERED','ACTIVE','BLOCKED'])->default('REGISTERED')->notNullable();
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
