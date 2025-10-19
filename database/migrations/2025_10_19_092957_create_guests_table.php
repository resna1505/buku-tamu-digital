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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('guest_groups')->onDelete('set null');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('table_number')->nullable();
            $table->integer('guests_count')->default(1);
            $table->boolean('is_vip')->default(false);
            $table->string('qr_code')->unique();
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_invited')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
