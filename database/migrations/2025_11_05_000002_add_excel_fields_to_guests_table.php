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
        Schema::table('guests', function (Blueprint $table) {
            // Tambah field untuk Tamu 1 dan Tamu 2
            $table->string('guest_1_name')->nullable()->after('registration_date');
            $table->string('guest_2_name')->nullable()->after('guest_1_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['guest_1_name', 'guest_2_name']);
        });
    }
};
