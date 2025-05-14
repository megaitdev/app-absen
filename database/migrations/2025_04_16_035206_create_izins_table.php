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
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->foreignId('employee_id')->index();
            $table->foreignId('jenis_izin')->constrained('jenis_izins');
            $table->tinyInteger('is_full_day')->default(0)->nullable();
            $table->string('mulai_izin', 20)->nullable();
            $table->string('selesai_izin', 20)->nullable();
            $table->integer('jam_izin')->default(0)->nullable();
            $table->string('pic', 5)->nullable();
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
        Schema::dropIfExists('izins');
    }
};
