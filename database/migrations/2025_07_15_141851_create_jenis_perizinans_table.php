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
        Schema::create('jenis_perizinans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama jenis izin (e.g., 'Cuti Tahunan', 'Sakit', 'Izin Pribadi')
            $table->text('deskripsi')->nullable(); // Deskripsi opsional untuk jenis izin
            $table->boolean('memotong_kuota')->default(false); // Apakah izin ini memotong jatah cuti? (true/false)
            $table->integer('level_persetujuan_dibutuhkan')->default(1); // Berapa level atasan yang harus menyetujui (default 1)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_perizinans');
    }
};
