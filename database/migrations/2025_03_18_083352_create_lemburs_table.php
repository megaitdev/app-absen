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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->foreignId('employee_id')->index();
            $table->enum('lembur', ['terusan', 'libur']);
            $table->string('mulai_lembur', 20)->nullable();
            $table->string('selesai_lembur', 20)->nullable();
            $table->string('pic', 5)->nullable();
            $table->string('lampiran', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->text('data_scan')->nullable();

            // Workflow approval fields
            $table->enum('status', ['pending', 'approved_supervisor', 'approved_hrd', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('submitted_by')->nullable()->constrained('users'); // PIC yang mengajukan
            $table->foreignId('approved_by_supervisor')->nullable()->constrained('users'); // Atasan yang approve
            $table->foreignId('approved_by_hrd')->nullable()->constrained('users'); // HRD yang approve
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_supervisor_at')->nullable();
            $table->timestamp('approved_hrd_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('supervisor_notes')->nullable();
            $table->text('hrd_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
