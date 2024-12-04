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

      Schema::create('client_revenue_points', function (Blueprint $table) {
            $table->id();
            $table->string("client_id",36)->notNullable();
            $table->string("code",50)->notNullable();
            $table->string('name',150)->notNullable();
            $table->string('description',150)->notNullable();
            $table->timestamps();
            $table->unique(['client_id', 'code'],'client_revenuepointCode');
         });

   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('client_revenue_points');
   }
};
