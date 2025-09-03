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
        Schema::table('shifts', function (Blueprint $table) {
            // Jeda lembur terusan (overtime continuation break)
            $table->integer('jeda_lembur_terusan')->default(0)->after('total_menit_istirahat');
            $table->text('istirahat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn([
                'jeda_lembur_terusan',
                'istirahat',
            ]);
        });
    }
};
