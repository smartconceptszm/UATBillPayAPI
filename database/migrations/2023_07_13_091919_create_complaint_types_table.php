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
      Schema::create('complaint_types', function (Blueprint $table) {
         $table->uuid('id')->primary();
         $table->uuid('client_id')->notNullable();
         $table->string('code',2)->notNullable();
         $table->string('name',50)->notNullable();
         $table->unsignedTinyInteger('order')->notNullable();
         $table->timestamps();
         $table->unique(['client_id', 'code'],'client_complaint_type');
         $table->unique(['client_id', 'order'],'client_type_order');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_types');
    }
};
