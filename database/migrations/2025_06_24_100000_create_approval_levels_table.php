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
        Schema::create('approval_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->index(); // Unit yang memerlukan approval
            $table->foreignId('divisi_id')->nullable()->index(); // Divisi yang memerlukan approval
            $table->foreignId('supervisor_user_id')->constrained('users'); // User yang menjadi atasan/supervisor
            $table->enum('approval_type', ['unit', 'divisi']); // Tipe approval berdasarkan unit atau divisi
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['unit_id', 'is_active']);
            $table->index(['divisi_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_levels');
    }
};
