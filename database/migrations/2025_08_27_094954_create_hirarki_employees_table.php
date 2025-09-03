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
        Schema::create('hirarki_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->references('id')->on('hrd_employees');
            $table->foreignId('atasan_id')->references('id')->on('hrd_employees');
            $table->tinyInteger('status')->default(1);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hirarki_employees');
    }
};
