<?php

namespace Database\Seeders;

use App\Models\JenisPerizinan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPerizinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisPerizinans = [
            [
                'nama' => 'Cuti Tahunan',
                'deskripsi' => 'Cuti tahunan yang dipotong dari jatah cuti karyawan',
                'memotong_kuota' => true,
                'level_persetujuan_dibutuhkan' => 2
            ],
            [
                'nama' => 'Cuti Sakit',
                'deskripsi' => 'Cuti karena sakit dengan surat keterangan dokter',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 1
            ],
            [
                'nama' => 'Cuti Melahirkan',
                'deskripsi' => 'Cuti melahirkan untuk karyawan wanita',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 2
            ],
            [
                'nama' => 'Cuti Menikah',
                'deskripsi' => 'Cuti untuk menikah',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 2
            ],
            [
                'nama' => 'Cuti Khitan Anak',
                'deskripsi' => 'Cuti untuk khitan anak',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 1
            ],
            [
                'nama' => 'Cuti Baptis Anak',
                'deskripsi' => 'Cuti untuk baptis anak',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 1
            ],
            [
                'nama' => 'Cuti Keluarga Meninggal',
                'deskripsi' => 'Cuti karena ada keluarga yang meninggal dunia',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 1
            ],
            [
                'nama' => 'Cuti Ibadah Haji',
                'deskripsi' => 'Cuti untuk menunaikan ibadah haji',
                'memotong_kuota' => false,
                'level_persetujuan_dibutuhkan' => 2
            ],
            [
                'nama' => 'Cuti Penting',
                'deskripsi' => 'Cuti untuk keperluan penting lainnya',
                'memotong_kuota' => true,
                'level_persetujuan_dibutuhkan' => 2
            ],
            [
                'nama' => 'Cuti Besar',
                'deskripsi' => 'Cuti besar untuk karyawan senior',
                'memotong_kuota' => true,
                'level_persetujuan_dibutuhkan' => 2
            ]
        ];

        foreach ($jenisPerizinans as $jenis) {
            JenisPerizinan::firstOrCreate(
                ['nama' => $jenis['nama']],
                $jenis
            );
        }
    }
}