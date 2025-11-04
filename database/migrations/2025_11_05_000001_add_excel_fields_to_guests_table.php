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
            // Tambah field BARU untuk data dari Excel (semuanya nullable)
            $table->string('student_name')->nullable()->after('name');
            $table->string('npm')->nullable()->after('student_name');
            $table->string('faculty')->nullable()->after('npm');
            $table->string('study_program')->nullable()->after('faculty');
            $table->string('email')->nullable()->after('whatsapp');
            $table->text('payment_proof')->nullable()->after('email');
            $table->timestamp('registration_date')->nullable()->after('payment_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn([
                'student_name',
                'npm',
                'faculty',
                'study_program',
                'email',
                'payment_proof',
                'registration_date'
            ]);
        });
    }
};
