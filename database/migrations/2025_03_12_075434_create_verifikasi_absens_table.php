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
        Schema::create('verifikasi_absens', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('employee_id');
            $table->string('jenis', 20)->nullable();
            $table->string('jam_masuk', 20)->nullable();
            $table->string('jam_keluar', 20)->nullable();
            $table->string('pic', 50)->nullable();
            $table->string('lampiran', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->text('data_scan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_absens');
    }
};
