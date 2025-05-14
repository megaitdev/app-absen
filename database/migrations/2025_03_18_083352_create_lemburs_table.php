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
