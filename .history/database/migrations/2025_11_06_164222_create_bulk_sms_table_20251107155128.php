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
        Schema::create('bulk_sms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firma_id')->index()->nullable();
            $table->unsignedBigInteger('servis')->nullable();
            $table->unsignedBigInteger('musteri')->nullable();
            $table->string('gsmno', 20)->nullable();
            $table->string('provider')->nullable();
            $table->string('response_code')->nullable();
            $table->string('hata_mesaji')->nullable();
            $table->text('mesaj')->nullable();
            $table->integer('asama')->nullable();
            $table->enum('durum', ['0', '1', '2'])->default('1')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms');
    }
};
