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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->string('mulai_jam_masuk')->nullable();
            $table->string('jam_masuk');
            $table->string('selesai_jam_masuk')->nullable();

            $table->string('mulai_jam_keluar')->nullable();
            $table->string('jam_keluar');
            $table->string('selesai_jam_keluar')->nullable();

            $table->float('total_jam_kerja')->nullable();
            $table->float('total_menit_kerja')->nullable();

            $table->string('mulai_jam_mulai_istirahat')->nullable();
            $table->string('jam_mulai_istirahat')->nullable();
            $table->string('selesai_jam_mulai_istirahat')->nullable();

            $table->string('mulai_jam_selesai_istirahat')->nullable();
            $table->string('jam_selesai_istirahat')->nullable();
            $table->string('selesai_jam_selesai_istirahat')->nullable();

            $table->float('total_jam_istirahat')->nullable();
            $table->float('total_menit_istirahat')->nullable();

            $table->tinyInteger('is_sameday')->default(0);
            $table->tinyInteger('is_break')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
