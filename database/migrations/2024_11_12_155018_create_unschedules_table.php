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
        Schema::create('unschedules', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan')->nullable();
            $table->bigInteger('employee_id')->index();
            $table->string('pin')->index();
            $table->date('date')->index();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete(); // delete unschedules when the related shift is deleted
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unschedules');
    }
};
