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
        Schema::create('stock_suppliers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('firma_id')->nullable();
            $table->integer('kid')->nullable();
            $table->string('tedarikci')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_suppliers');
    }
};
