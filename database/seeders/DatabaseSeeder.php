<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'nama' => 'Abima Nugraha',
            'email' => 'abimanugraha@gmail.com',
            'email_verified_at' => Carbon::now(),
            'nomor_wa' => '628989227992',
            'nomor_wa_verified_at' => Carbon::now(),
            'username' => 'tuanputri',
            'password' => Hash::make('root'),
        ]);
        User::create([
            'nama' => 'PIC EOP',
            'email' => 'piceop@gmail.com',
            'email_verified_at' => Carbon::now(),
            'nomor_wa' => '628989227992',
            'nomor_wa_verified_at' => Carbon::now(),
            'username' => 'piceop',
            'password' => Hash::make('root'),
            'role' => 'pic',
            'pic' => 'EOP',
        ]);


        DB::unprepared(file_get_contents(public_path('sql/shifts.sql')));
        DB::unprepared(file_get_contents(public_path('sql/schedules.sql')));
        // DB::unprepared(file_get_contents(public_path('sql/scan_logs.sql')));
        DB::unprepared(file_get_contents(public_path('sql/holidays.sql')));
        DB::unprepared(file_get_contents(public_path('sql/jenis_izins.sql')));
        DB::unprepared(file_get_contents(public_path('sql/jenis_cutis.sql')));
        DB::unprepared(file_get_contents(public_path('sql/dasar_jadwals.sql')));
    }
}
