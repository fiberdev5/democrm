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
        Schema::table('integration_purchases', function (Blueprint $table) {
            $table->string('payment_token')->nullable()->after('integration_id');
            $table->index('payment_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_purchases', function (Blueprint $table) {
            $table->dropColumn('payment_token');
        });
    }
};
