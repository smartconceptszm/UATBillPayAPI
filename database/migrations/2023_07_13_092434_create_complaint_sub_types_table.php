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
      Schema::create('complaint_sub_types', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('complaint_type_id')->notNullable();
        $table->string('code',3)->notNullable();
        $table->string('name',50)->notNullable();
        $table->unsignedTinyInteger('order')->notNullable();
        $table->enum('requiresDetails',['YES','NO'])->default('NO');
        $table->enum('detailType',['MOBILE','READING','METER','PAYMENTMODE','APPLICATION'])->nullable();
        $table->string('prompt',150)->nullable();
        $table->timestamps();
        $table->unique(['complaint_type_id','code'],'subtype_code');
        $table->unique(['complaint_type_id', 'order'],'subtype_order');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_sub_types');
    }
};
