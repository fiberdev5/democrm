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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable(); // Logo dosya yolu veya URL
            
            // Kategori ve Tür
            $table->string('category'); // payment, email, sms, crm, accounting, storage vb.
            $table->boolean('is_active')->default(true); // Entegrasyon aktif mi?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
