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
            // Tambah field untuk nomor kursi tamu 1 dan 2
            $table->string('seat_number_guest_1')->nullable()->after('guest_1_name');
            $table->string('seat_number_guest_2')->nullable()->after('guest_2_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['seat_number_guest_1', 'seat_number_guest_2']);
        });
    }
};
