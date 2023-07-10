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

      Schema::create('rights', function (Blueprint $table) {
         $table->id();
         $table->string('name',50)->unique()->notNullable();
         $table->string('description',50)->nullable();
         $table->timestamps();
      });

      Schema::create('group_rights', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("group_id")->notNullable();
         $table->unsignedBigInteger("right_id")->notNullable();
         $table->timestamps();
      });

      Schema::create('groups', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger('client_id')->notNullable();
         $table->string('name',50)->notNullable();
         $table->string('description',50)->nullable();
         $table->unique(['client_id','name'],'client_id_name');
         $table->timestamps();
      });

      Schema::create('user_groups', function (Blueprint $table) {
         $table->id();
         $table->unsignedBigInteger("user_id")->notNullable();
         $table->unsignedBigInteger("group_id")->notNullable();
         $table->timestamps();
      });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('users');
      Schema::dropIfExists('rights');
      Schema::dropIfExists('group_rights');
      Schema::dropIfExists('groups');
      Schema::dropIfExists('user_groups');
   }

};
