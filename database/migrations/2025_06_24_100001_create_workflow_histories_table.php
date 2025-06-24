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
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();
            $table->string('workflowable_type'); // Model type (Cuti, Izin, Lembur, VerifikasiAbsen)
            $table->unsignedBigInteger('workflowable_id'); // Model ID
            $table->enum('action', ['submitted', 'approved_supervisor', 'approved_hrd', 'rejected', 'cancelled']);
            $table->foreignId('user_id')->constrained('users'); // User yang melakukan action
            $table->text('notes')->nullable(); // Catatan/alasan
            $table->json('metadata')->nullable(); // Data tambahan (old_status, new_status, etc)
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['workflowable_type', 'workflowable_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');
    }
};
