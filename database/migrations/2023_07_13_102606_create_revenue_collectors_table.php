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

      Schema::create('revenue_collectors', function (Blueprint $table) {
            $table->id();
            $table->string("client_id",36)->notNullable();
            $table->string("code",50)->notNullable();
            $table->string('nrcNumber',15)->notNullable();
            $table->string('fullname',150)->notNullable();
            $table->string("mobileNumber",12)->notNullable()->unique();
            $table->timestamps();
            $table->unique(['client_id', 'code'],'client_revenuecollectorCode');
         });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('revenue_collectors');
   }
};
