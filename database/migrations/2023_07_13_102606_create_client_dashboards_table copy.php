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

      Schema::create('client_dashboards', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->string("client_id",36)->notNullable();
         $table->string('name',150)->notNullable();
         $table->enum('isActive',['YES','NO'])->default('YES')->notNullable();
         $table->timestamps();
         $table->unique(['client_id','name'],'client_dashboard');
      });



   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('dashboard_snippets');
   }
};
