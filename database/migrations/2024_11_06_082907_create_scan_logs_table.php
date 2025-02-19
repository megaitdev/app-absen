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
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->datetime('scan_date');
            $table->date('date')->index();
            $table->time('time');
            $table->string('nama_karyawan');
            $table->string('nik')->index();
            $table->string('pin')->index();
            $table->string('divisi')->index();
            $table->string('divisi_id');
            $table->string('unit');
            $table->string('unit_id')->index();
            $table->string('sn')->nullable()->index();
            $table->string('device_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
