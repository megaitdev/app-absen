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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->bigInteger('employee_id')->index();
            $table->string('pin')->index();
            $table->date('date')->index();
            $table->string('day')->nullable();
            $table->foreignId('shift_id')->constrained('shifts');
            $table->string('status', 20)->index();

            $table->dateTime('scan_masuk_murni')->nullable();
            $table->dateTime('scan_masuk_efektif')->nullable();
            $table->string('status_masuk', 20)->index()->nullable();

            $table->dateTime('scan_keluar_murni')->nullable();
            $table->dateTime('scan_keluar_efektif')->nullable();
            $table->string('status_keluar', 20)->index()->nullable();

            $table->integer('jam_kerja_murni')->nullable()->default(0);
            $table->integer('jam_kerja_efektif')->nullable()->default(0);

            $table->integer('istirahat_murni')->nullable()->default(0);
            $table->integer('istirahat_efektif')->nullable()->default(0);

            $table->integer('lembur_murni')->nullable()->default(0);
            $table->integer('lembur_efektif')->nullable()->default(0);
            $table->integer('lembur_akumulasi')->nullable()->default(0);

            $table->integer('jam_hilang_murni')->nullable()->default(0);
            $table->integer('jam_hilang_efektif')->nullable()->default(0);

            $table->boolean('is_cuti')->default(false)->index();
            $table->boolean('is_izin')->default(false)->index();
            $table->boolean('is_sakit')->default(false)->index();
            $table->boolean('is_lembur')->default(false)->index();

            $table->boolean('uk')->default(true)->index();
            $table->boolean('um')->default(true)->index();
            $table->boolean('ut')->default(true)->index();

            $table->boolean('uml')->default(false)->index();
            $table->boolean('utl')->default(false)->index();
            $table->boolean('umll')->default(false)->index();



            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
