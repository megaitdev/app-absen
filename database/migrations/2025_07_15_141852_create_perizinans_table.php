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
        Schema::create('perizinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrd_employees');
            $table->foreignId('submitted_by_user_id')->constrained('users'); // Foreign Key ke tabel users (pengaju)
            $table->foreignId('jenis')->constrained('jenis_perizinans'); // Foreign Key ke tabel jenis_izin

            $table->date('tanggal_mulai'); // Tanggal mulai izin
            $table->date('tanggal_selesai'); // Tanggal selesai izin
            $table->decimal('jumlah_hari', 5, 2); // Jumlah hari izin (misal: 0.5, 1, 5.5)
            $table->integer('durasi'); // Durasi dalam menit

            $table->text('alasan'); // Alasan pengajuan izin
            $table->string('lampiran')->nullable(); // Path/URL file lampiran (misal: surat dokter)

            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dibatalkan'])->default('pending'); // Status permohonan
            $table->integer('level_persetujuan_saat_ini')->default(0); // Level atasan yang sudah menyetujui (0 = belum ada yang menyetujui)

            // Kolom JSON untuk menyimpan riwayat persetujuan (fleksibel untuk multi-level approval)
            $table->longText('riwayat_persetujuan')->nullable(); // Menyimpan array JSON detail persetujuan dari setiap level

            $table->foreignId('ditolak_oleh_user_id')->nullable()->constrained('users'); // Siapa yang menolak permohonan
            $table->text('komentar_penolakan')->nullable(); // Komentar saat penolakan
            $table->timestamp('tanggal_ditolak')->nullable(); // Waktu permohonan ditolak

            $table->timestamp('tanggal_disetujui_hr')->nullable(); // Waktu persetujuan final oleh HR

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
