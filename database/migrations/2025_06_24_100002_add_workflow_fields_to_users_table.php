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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan field untuk mendukung workflow approval
            $table->boolean('is_supervisor')->default(false)->after('role'); // Apakah user adalah supervisor
            $table->boolean('is_hrd')->default(false)->after('is_supervisor'); // Apakah user adalah HRD
            $table->json('supervised_units')->nullable()->after('is_hrd'); // Unit yang disupervisi (array of unit_ids)
            $table->json('supervised_divisis')->nullable()->after('supervised_units'); // Divisi yang disupervisi (array of divisi_ids)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_supervisor', 'is_hrd', 'supervised_units', 'supervised_divisis']);
        });
    }
};
