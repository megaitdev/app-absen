<?php

namespace App\Http\Controllers\Perizinan;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\mak_hrd\Employee;
use App\Models\Perizinan;
use App\Models\JenisPerizinan;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PerizinanController extends Controller
{
    private $script;
    private $css;

    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    public function index()
    {
        $data = [
            'title' => 'Perizinan',
            'slug' => 'perizinan',
            'scripts' => [],
            'csses' => $this->css->getListCss('perizinan'),
        ];

        return view('perizinan.index', $data);
    }

    public function cuti()
    {
        // Get managed employees for current user
        $managedEmployees = $this->getManagedEmployees();

        $data = [
            'title' => 'Cuti',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'managedEmployees' => $managedEmployees
        ];

        return view('perizinan.cuti', $data);
    }

    public function storeCuti(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrd_employees,id',
            'jenis_cuti' => 'required|string|in:tahunan,sakit,melahirkan,menikah,khitan,baptis,keluarga_meninggal,ibadah_haji,penting,besar',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10|max:1000',
            'alamat_cuti' => 'nullable|string|max:500',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120' // 5MB max
        ], [
            'employee_id.required' => 'Karyawan harus dipilih',
            'employee_id.exists' => 'Karyawan tidak ditemukan',
            'jenis_cuti.required' => 'Jenis cuti harus dipilih',
            'jenis_cuti.in' => 'Jenis cuti tidak valid',
            'tanggal_mulai.required' => 'Tanggal mulai cuti harus diisi',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai cuti tidak boleh kurang dari hari ini',
            'tanggal_selesai.required' => 'Tanggal selesai cuti harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai',
            'keterangan.required' => 'Keterangan/alasan cuti harus diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
            'alamat_cuti.max' => 'Alamat cuti maksimal 500 karakter',
            'lampiran.*.mimes' => 'File lampiran harus berformat PDF, JPG, PNG, DOC, atau DOCX',
            'lampiran.*.max' => 'Ukuran file lampiran maksimal 5MB'
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Validasi akses karyawan
            $managedEmployees = $this->getManagedEmployees();
            $employee = $managedEmployees->where('id', $request->employee_id)->first();

            if (!$employee) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengajukan cuti atas nama karyawan tersebut')
                    ->withInput();
            }

            // Cari atau buat jenis perizinan berdasarkan jenis cuti
            $jenisPerizinan = $this->getOrCreateJenisPerizinan($request->jenis_cuti);

            // Hitung jumlah hari dan durasi
            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
            $jumlahHari = $this->calculateWorkingDays($tanggalMulai, $tanggalSelesai);
            $durasi = $jumlahHari * 8 * 60; // Asumsi 8 jam per hari dalam menit

            // Handle file upload
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    // Mendapatkan tanggal saat ini
                    $date = now()->format('Y-m-d'); // Format: YYYY-MM-DD

                    // Format id_employee menjadi 4 digit (dengan leading zeros jika perlu)
                    $formattedIdEmployee = str_pad($employee->id, 4, '0', STR_PAD_LEFT);

                    // Membuat kode unik 3 digit huruf besar
                    $uniqueCode = strtoupper(Str::random(3));

                    // Menggabungkan semua bagian untuk nama file baru
                    $filename = "CUTI-{$date}-{$formattedIdEmployee}-{$uniqueCode}." . $file->getClientOriginalExtension();

                    // Simpan file ke storage
                    $path = $file->storeAs('perizinan/cuti', $filename, 'storage');
                    $lampiranPaths[] = $path;
                }
            }

            // Simpan data perizinan
            $perizinan = Perizinan::create([
                'employee_id' => $request->employee_id,
                'submitted_by_user_id' => Auth::id(),
                'jenis' => $jenisPerizinan->id,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'jumlah_hari' => $jumlahHari,
                'durasi' => $durasi,
                'alasan' => $request->keterangan . ($request->alamat_cuti ? "\n\nAlamat selama cuti: " . $request->alamat_cuti : ''),
                'lampiran' => !empty($lampiranPaths) ? json_encode($lampiranPaths) : null,
                'status' => 'pending',
                'level_persetujuan_saat_ini' => 0,
                'riwayat_persetujuan' => [
                    [
                        'level' => 0,
                        'action' => 'submitted',
                        'submitted_by_user_id' => Auth::id(),
                        'submitted_by_user_name' => Auth::user()->nama,
                        'employee_id' => $request->employee_id,
                        'employee_name' => $employee->nama,
                        'timestamp' => now(),
                        'keterangan' => 'Pengajuan cuti disubmit'
                    ]
                ]
            ]);

            DB::commit();

            return redirect()->route('perizinan.index')
                ->with('success', 'Pengajuan cuti berhasil disubmit. Nomor pengajuan: #' . str_pad($perizinan->id, 6, '0', STR_PAD_LEFT));
        } catch (\Exception $e) {
            DB::rollback();

            // Hapus file yang sudah diupload jika ada error
            if (!empty($lampiranPaths)) {
                foreach ($lampiranPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan cuti: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function izin()
    {
        // Get managed employees for current user
        $managedEmployees = $this->getManagedEmployees();

        $data = [
            'title' => 'Izin',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'managedEmployees' => $managedEmployees
        ];

        return view('perizinan.izin', $data);
    }

    public function storeIzin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrd_employees,id',
            'jenis_izin' => 'required|string|in:sakit,keperluan_keluarga,keperluan_pribadi,keperluan_mendesak,keperluan_medis,acara_keluarga,keperluan_resmi,lainnya',
            'tanggal_izin' => 'required|date|after_or_equal:today',
            'waktu_izin' => 'required|string|in:full_day,half_day_morning,half_day_afternoon,custom_time',
            'jam_mulai' => 'nullable|required_if:waktu_izin,custom_time|date_format:H:i',
            'jam_selesai' => 'nullable|required_if:waktu_izin,custom_time|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string|min:10|max:1000',
            'kontak_darurat' => 'nullable|string|max:20',
            'lokasi_izin' => 'nullable|string|max:255',
            'pernyataan' => 'required|accepted',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120' // 5MB max
        ], [
            'employee_id.required' => 'Karyawan harus dipilih',
            'employee_id.exists' => 'Karyawan tidak ditemukan',
            'jenis_izin.required' => 'Jenis izin harus dipilih',
            'jenis_izin.in' => 'Jenis izin tidak valid',
            'tanggal_izin.required' => 'Tanggal izin harus diisi',
            'tanggal_izin.after_or_equal' => 'Tanggal izin tidak boleh kurang dari hari ini',
            'waktu_izin.required' => 'Waktu izin harus dipilih',
            'waktu_izin.in' => 'Waktu izin tidak valid',
            'jam_mulai.required_if' => 'Jam mulai harus diisi untuk waktu tertentu',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid',
            'jam_selesai.required_if' => 'Jam selesai harus diisi untuk waktu tertentu',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai',
            'keterangan.required' => 'Keterangan/alasan izin harus diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
            'kontak_darurat.max' => 'Kontak darurat maksimal 20 karakter',
            'lokasi_izin.max' => 'Lokasi izin maksimal 255 karakter',
            'pernyataan.required' => 'Anda harus menyetujui pernyataan',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan',
            'lampiran.*.mimes' => 'File lampiran harus berformat PDF, JPG, PNG, DOC, atau DOCX',
            'lampiran.*.max' => 'Ukuran file lampiran maksimal 5MB'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Validasi akses karyawan
            $managedEmployees = $this->getManagedEmployees();
            $employee = $managedEmployees->where('id', $request->employee_id)->first();

            if (!$employee) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengajukan izin atas nama karyawan tersebut')
                    ->withInput();
            }

            // Cari atau buat jenis perizinan berdasarkan jenis izin
            $jenisPerizinan = $this->getOrCreateJenisPerizinanIzin($request->jenis_izin);

            // Hitung durasi berdasarkan waktu izin
            $durasi = $this->calculateIzinDuration($request->waktu_izin, $request->jam_mulai, $request->jam_selesai);

            // Handle file upload
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $date = now()->format('Y-m-d');
                    $formattedIdEmployee = str_pad($employee->id, 4, '0', STR_PAD_LEFT);
                    $uniqueCode = strtoupper(Str::random(3));
                    $filename = "IZIN-{$date}-{$formattedIdEmployee}-{$uniqueCode}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('perizinan/izin', $filename, 'storage');
                    $lampiranPaths[] = $path;
                }
            }

            // Prepare additional info
            $additionalInfo = [];
            if ($request->kontak_darurat) {
                $additionalInfo['kontak_darurat'] = $request->kontak_darurat;
            }
            if ($request->lokasi_izin) {
                $additionalInfo['lokasi_izin'] = $request->lokasi_izin;
            }
            if ($request->waktu_izin === 'custom_time') {
                $additionalInfo['jam_mulai'] = $request->jam_mulai;
                $additionalInfo['jam_selesai'] = $request->jam_selesai;
            }
            $additionalInfo['waktu_izin'] = $request->waktu_izin;

            // Simpan data perizinan
            $perizinan = Perizinan::create([
                'employee_id' => $request->employee_id,
                'submitted_by_user_id' => Auth::id(),
                'jenis' => $jenisPerizinan->id,
                'tanggal_mulai' => $request->tanggal_izin,
                'tanggal_selesai' => $request->tanggal_izin,
                'jumlah_hari' => 1,
                'durasi' => $durasi,
                'alasan' => $request->keterangan . (!empty($additionalInfo) ? "\n\nInfo Tambahan: " . json_encode($additionalInfo, JSON_PRETTY_PRINT) : ''),
                'lampiran' => !empty($lampiranPaths) ? json_encode($lampiranPaths) : null,
                'status' => 'pending',
                'level_persetujuan_saat_ini' => 0,
                'riwayat_persetujuan' => json_encode([
                    [
                        'level' => 0,
                        'action' => 'submitted',
                        'submitted_by_user_id' => Auth::id(),
                        'submitted_by_user_name' => Auth::user()->nama,
                        'employee_id' => $request->employee_id,
                        'employee_name' => $employee->nama,
                        'timestamp' => now(),
                        'keterangan' => 'Pengajuan izin disubmit'
                    ]
                ])
            ]);

            DB::commit();

            return redirect()->route('perizinan.index')
                ->with('success', 'Pengajuan izin berhasil disubmit. Nomor pengajuan: #' . str_pad($perizinan->id, 6, '0', STR_PAD_LEFT));
        } catch (\Exception $e) {
            DB::rollback();

            // Hapus file yang sudah diupload jika ada error
            if (!empty($lampiranPaths)) {
                foreach ($lampiranPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan izin: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function verifikasiAbsen()
    {
        // Get managed employees for current user
        $managedEmployees = $this->getManagedEmployees();

        $data = [
            'title' => 'Verifikasi Absen',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'managedEmployees' => $managedEmployees
        ];

        return view('perizinan.verifikasi-absen', $data);
    }

    public function storeVerifikasiAbsen(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrd_employees,id',
            'tanggal_absen' => 'required|date|before_or_equal:today',
            'jenis_verifikasi' => 'required|string|in:absen_masuk,absen_pulang,koreksi_waktu,absen_lengkap',
            'jam_masuk_benar' => 'nullable|required_if:jenis_verifikasi,absen_masuk,koreksi_waktu,absen_lengkap|date_format:H:i',
            'jam_pulang_benar' => 'nullable|required_if:jenis_verifikasi,absen_pulang,koreksi_waktu,absen_lengkap|date_format:H:i|after_or_equal:jam_masuk_benar',
            'alasan_verifikasi' => 'required|string|min:20|max:2000',
            'saksi_nama' => 'nullable|string|max:100',
            'saksi_jabatan' => 'nullable|string|max:100',
            'lokasi_kerja' => 'nullable|string|max:255',
            'pernyataan' => 'required|accepted',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120' // 5MB max
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'employee_id.exists' => 'Karyawan tidak ditemukan.',
            'tanggal_absen.required' => 'Tanggal absen yang ingin diverifikasi harus diisi.',
            'tanggal_absen.before_or_equal' => 'Tanggal absen tidak boleh melebihi hari ini.',
            'jenis_verifikasi.required' => 'Jenis verifikasi harus dipilih.',
            'jam_masuk_benar.required_if' => 'Jam masuk yang benar harus diisi untuk jenis verifikasi ini.',
            'jam_pulang_benar.required_if' => 'Jam pulang yang benar harus diisi untuk jenis verifikasi ini.',
            'jam_pulang_benar.after_or_equal' => 'Jam pulang tidak boleh lebih awal dari jam masuk.',
            'alasan_verifikasi.required' => 'Alasan atau penjelasan verifikasi harus diisi.',
            'alasan_verifikasi.min' => 'Alasan verifikasi minimal 20 karakter.',
            'pernyataan.required' => 'Anda harus menyetujui pernyataan kebenaran data.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
            'lampiran.*.mimes' => 'Format file lampiran tidak valid. Hanya PDF, JPG, PNG, DOC, DOCX yang diizinkan.',
            'lampiran.*.max' => 'Ukuran file lampiran tidak boleh melebihi 5MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Validasi akses karyawan
            $managedEmployees = $this->getManagedEmployees();
            $employee = $managedEmployees->where('id', $request->employee_id)->first();

            if (!$employee) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengajukan verifikasi atas nama karyawan tersebut')
                    ->withInput();
            }

            // Cari atau buat jenis perizinan untuk "Verifikasi Absen"
            $jenisPerizinan = JenisPerizinan::firstOrCreate(
                ['nama' => 'Verifikasi Absen'],
                [
                    'deskripsi' => 'Pengajuan untuk memperbaiki data kehadiran yang tidak sesuai.',
                    'memotong_kuota' => false,
                    'level_persetujuan_dibutuhkan' => 1 // Cukup HRD
                ]
            );

            // Handle file upload
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $date = now()->format('Y-m-d');
                    $formattedIdEmployee = str_pad($employee->id, 4, '0', STR_PAD_LEFT);
                    $uniqueCode = strtoupper(Str::random(3));
                    $filename = "VERABSEN-{$date}-{$formattedIdEmployee}-{$uniqueCode}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('perizinan/verifikasi-absen', $filename, 'public');
                    $lampiranPaths[] = $path;
                }
            }

            // Gabungkan semua informasi tambahan ke dalam satu field
            $detailVerifikasi = [
                'jenis_verifikasi' => $request->jenis_verifikasi,
                'jam_masuk_seharusnya' => $request->jam_masuk_benar,
                'jam_pulang_seharusnya' => $request->jam_pulang_benar,
                'saksi' => [
                    'nama' => $request->saksi_nama,
                    'jabatan' => $request->saksi_jabatan,
                ],
                'lokasi_kerja' => $request->lokasi_kerja,
            ];

            $alasanLengkap = $request->alasan_verifikasi . "\n\n--- DETAIL VERIFIKASI ---\n" . json_encode($detailVerifikasi, JSON_PRETTY_PRINT);

            // Simpan data perizinan
            $perizinan = Perizinan::create([
                'employee_id' => $employee->id,
                'submitted_by_user_id' => Auth::id(),
                'jenis' => $jenisPerizinan->id,
                'tanggal_mulai' => $request->tanggal_absen,
                'tanggal_selesai' => $request->tanggal_absen,
                'jumlah_hari' => 0, // Verifikasi tidak dihitung sebagai hari
                'durasi' => 0, // Verifikasi tidak memiliki durasi
                'alasan' => $alasanLengkap,
                'lampiran' => !empty($lampiranPaths) ? json_encode($lampiranPaths) : null,
                'status' => 'pending',
                'level_persetujuan_saat_ini' => 0,
                'riwayat_persetujuan' => json_encode([
                    [
                        'level' => 0,
                        'action' => 'submitted',
                        'submitted_by_user_id' => Auth::id(),
                        'submitted_by_user_name' => Auth::user()->nama,
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->nama,
                        'timestamp' => now(),
                        'keterangan' => 'Pengajuan verifikasi absen disubmit.'
                    ]
                ])
            ]);

            DB::commit();

            return redirect()->route('perizinan.index')
                ->with('success', 'Pengajuan verifikasi absen berhasil disubmit. Nomor pengajuan: #' . str_pad($perizinan->id, 6, '0', STR_PAD_LEFT));
        } catch (\Exception $e) {
            DB::rollback();

            // Hapus file yang sudah diupload jika ada error
            if (!empty($lampiranPaths)) {
                foreach ($lampiranPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function lembur()
    {
        $managedEmployees = $this->getManagedEmployees();
        $data = [
            'title' => 'Lembur',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'managedEmployees' => $managedEmployees,
        ];

        return view('perizinan.lembur', $data);
    }

    public function storeLembur(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:hrd_employees,id',
            'tanggal_lembur' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string|min:10|max:1000',
            'lampiran.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'jenis_lembur' => 'required|in:sendiri,tim',
            'team_members' => 'nullable|json|required_if:jenis_lembur,tim',
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'tanggal_lembur.required' => 'Tanggal lembur harus diisi.',
            'tanggal_lembur.after_or_equal' => 'Tanggal lembur tidak boleh kurang dari hari ini.',
            'jam_mulai.required' => 'Jam mulai lembur harus diisi.',
            'jam_selesai.required' => 'Jam selesai lembur harus diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'keterangan.required' => 'Keterangan/alasan lembur harus diisi.',
            'keterangan.min' => 'Keterangan minimal 10 karakter.',
            'jenis_lembur.required' => 'Jenis lembur harus dipilih.',
            'team_members.required_if' => 'Anggota tim harus ditambahkan untuk lembur tim.',
            'team_members.json' => 'Format data tim tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $managedEmployees = $this->getManagedEmployees();
            $employee = $managedEmployees->where('id', $request->employee_id)->first();

            if (!$employee) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk mengajukan lembur atas nama karyawan tersebut.')
                    ->withInput();
            }

            // Check if employee already has overtime request for the same date
            $existingLembur = Lembur::where('employee_id', $request->employee_id)
                ->where('date', $request->tanggal_lembur)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingLembur) {
                return redirect()->back()
                    ->with('error', 'Karyawan sudah memiliki pengajuan lembur pada tanggal tersebut.')
                    ->withInput();
            }

            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $date = now()->format('Y-m-d');
                    $formattedIdEmployee = str_pad($employee->id, 4, '0', STR_PAD_LEFT);
                    $uniqueCode = strtoupper(Str::random(3));
                    $filename = "LEMBUR-{$date}-{$formattedIdEmployee}-{$uniqueCode}." . $file->getClientOriginalExtension();
                    $path = $file->storeAs('perizinan/lembur', $filename, 'public');
                    $lampiranPaths[] = $filename;
                }
            }

            $teamMemberIds = [];
            if ($request->jenis_lembur === 'tim' && !empty($request->team_members)) {
                $teamMemberIds = json_decode($request->team_members, true);
                // Ensure the submitter is also in the team if they are not already
                if (!in_array($employee->id, $teamMemberIds)) {
                    $teamMemberIds[] = $employee->id;
                }
            } else {
                $teamMemberIds[] = $employee->id;
            }

            $allEmployeeIds = collect($teamMemberIds)->unique();
            $lemburGroupId = Str::uuid(); // Generate a unique ID for this group of overtime requests
            $totalEmployees = 0;

            foreach ($allEmployeeIds as $memberId) {
                // Check if this employee is managed by current user
                $memberEmployee = $managedEmployees->where('id', $memberId)->first();
                if (!$memberEmployee) {
                    continue; // Skip employees not managed by current user
                }

                // Check for existing overtime on the same date
                $existingMemberLembur = Lembur::where('employee_id', $memberId)
                    ->where('date', $request->tanggal_lembur)
                    ->where('status', '!=', 'rejected')
                    ->first();

                if ($existingMemberLembur) {
                    continue; // Skip if already has overtime request
                }

                Lembur::create([
                    'employee_id' => $memberId,
                    'date' => $request->tanggal_lembur,
                    'mulai_lembur' => $request->jam_mulai,
                    'selesai_lembur' => $request->jam_selesai,
                    'keterangan' => $request->keterangan,
                    'lampiran' => !empty($lampiranPaths) ? json_encode($lampiranPaths) : null,
                    'lembur' => 'terusan',
                    'group_id' => $lemburGroupId,
                    'is_team_lead' => ($memberId == $employee->id),
                ]);

                $totalEmployees++;
            }

            if ($totalEmployees === 0) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'Tidak ada karyawan yang berhasil diproses untuk pengajuan lembur.')
                    ->withInput();
            }

            DB::commit();

            $message = $totalEmployees === 1
                ? 'Pengajuan lembur berhasil disubmit dan sedang menunggu persetujuan.'
                : "Pengajuan lembur berhasil disubmit untuk {$totalEmployees} karyawan dan sedang menunggu persetujuan.";

            return redirect()->route('perizinan.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            // Clean up uploaded files
            if (!empty($lampiranPaths)) {
                foreach ($lampiranPaths as $path) {
                    Storage::disk('public')->delete('perizinan/lembur/' . $path);
                }
            }
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan lembur: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showLembur(Lembur $lembur)
    {
        // Check if user has access to view this overtime request
        $managedEmployees = $this->getManagedEmployees();
        $employee = $managedEmployees->where('id', $lembur->employee_id)->first();

        if (!$employee) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan lembur ini.');
        }

        $lembur->load(['employee', 'submittedBy', 'approvedBySupervisor', 'approvedByHrd', 'workflowHistories.user']);

        $data = [
            'title' => 'Detail Lembur',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'lembur' => $lembur,
            'employee' => $employee,
        ];

        return view('perizinan.lembur-detail', $data);
    }

    public function myLemburRequests()
    {
        $managedEmployees = $this->getManagedEmployees();
        $employeeIds = $managedEmployees->pluck('id');

        $lemburRequests = Lembur::whereIn('employee_id', $employeeIds)
            ->with(['employee', 'submittedBy', 'workflowHistories'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $data = [
            'title' => 'Pengajuan Lembur Saya',
            'slug' => 'perizinan',
            'csses' => [],
            'scripts' => [],
            'lemburRequests' => $lemburRequests,
            'managedEmployees' => $managedEmployees,
        ];

        return view('perizinan.lembur-requests', $data);
    }

    /**
     * Get managed employees for current user
     */
    private function getManagedEmployees()
    {
        $user = Auth::user();

        // If user has employees attribute (JSON string with employee IDs)
        if (isset($user->employees) && !empty($user->employees)) {
            $employeeIds = $user->employees;

            if (is_array($employeeIds) && !empty($employeeIds)) {
                return Employee::whereIn('id', $employeeIds)
                    ->where('is_deleted', 0)
                    ->with(['unit', 'divisi', 'posisi', 'jabatan'])
                    ->select('id', 'nama', 'nip', 'jabatan_id')
                    ->get();
            }
        }

        // If no managed employees, return current user as employee (self-service)
        if ($user->employee_id) {
            return Employee::where('id', $user->employee_id)
                ->where('is_deleted', 0)
                ->with(['unit', 'divisi', 'posisi', 'jabatan'])
                ->select('id', 'nama', 'nip', 'jabatan_id')
                ->get();
        }

        return collect(); // Return empty collection
    }

    /**
     * Get employee information via AJAX
     */
    public function getEmployeeInfo(Request $request)
    {
        $employeeId = $request->input('employee_id');

        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required'], 400);
        }

        // Check if current user can access this employee
        $managedEmployees = $this->getManagedEmployees();
        $employee = $managedEmployees->where('id', $employeeId)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found or access denied'], 404);
        }

        // Prepare employee data
        $employeeData = [
            'id' => $employee->id,
            'nama' => $employee->nama,
            'nip' => $employee->nip ?? '-',
            'jabatan' => $employee->jabatan->jabatan ?? '-',
            'unit_kerja' => $employee->unit->unit ?? '-',
            'divisi' => $employee->divisi->divisi ?? '-',
            'sisa_cuti' => $this->getSisaCuti($employee->id) // You can implement this method
        ];

        return response()->json($employeeData);
    }

    /**
     * Search for employees for team selection
     */
    public function searchEmployee(Request $request)
    {
        $searchTerm = $request->input('q');

        if (empty($searchTerm)) {
            return response()->json(['items' => []]);
        }

        $employees = Employee::where('is_deleted', 0)
            ->where(function ($query) use ($searchTerm) {
                $query->where('nama', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nip', 'LIKE', "%{$searchTerm}%");
            })
            ->limit(10)
            ->get(['id', 'nama', 'nip']);

        $results = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'text' => $employee->nama . ' (' . ($employee->nip ?? 'No NIP') . ')'
            ];
        });

        return response()->json(['items' => $results]);
    }

    /**
     * Get remaining leave days for employee
     * This is a placeholder - implement according to your business logic
     */
    private function getSisaCuti($employeeId)
    {
        // Implement your logic to calculate remaining leave days
        // This could involve checking leave history, annual allowance, etc.
        return '12 hari'; // Placeholder
    }

    /**
     * Get or create jenis perizinan based on cuti type
     */
    private function getOrCreateJenisPerizinan($jenisCuti)
    {
        $jenisMapping = [
            'tahunan' => ['nama' => 'Cuti Tahunan', 'memotong_kuota' => true, 'level' => 2],
            'sakit' => ['nama' => 'Cuti Sakit', 'memotong_kuota' => false, 'level' => 1],
            'melahirkan' => ['nama' => 'Cuti Melahirkan', 'memotong_kuota' => false, 'level' => 2],
            'menikah' => ['nama' => 'Cuti Menikah', 'memotong_kuota' => false, 'level' => 2],
            'khitan' => ['nama' => 'Cuti Khitan Anak', 'memotong_kuota' => false, 'level' => 1],
            'baptis' => ['nama' => 'Cuti Baptis Anak', 'memotong_kuota' => false, 'level' => 1],
            'keluarga_meninggal' => ['nama' => 'Cuti Keluarga Meninggal', 'memotong_kuota' => false, 'level' => 1],
            'ibadah_haji' => ['nama' => 'Cuti Ibadah Haji', 'memotong_kuota' => false, 'level' => 2],
            'penting' => ['nama' => 'Cuti Penting', 'memotong_kuota' => true, 'level' => 2],
            'besar' => ['nama' => 'Cuti Besar', 'memotong_kuota' => true, 'level' => 2]
        ];

        $jenisData = $jenisMapping[$jenisCuti] ?? $jenisMapping['tahunan'];

        return JenisPerizinan::firstOrCreate(
            ['nama' => $jenisData['nama']],
            [
                'deskripsi' => 'Jenis perizinan untuk ' . $jenisData['nama'],
                'memotong_kuota' => $jenisData['memotong_kuota'],
                'level_persetujuan_dibutuhkan' => $jenisData['level']
            ]
        );
    }

    /**
     * Calculate working days between two dates (excluding weekends)
     */
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($current->dayOfWeek !== Carbon::SATURDAY && $current->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Get or create jenis perizinan based on izin type
     */
    private function getOrCreateJenisPerizinanIzin($jenisIzin)
    {
        $jenisMapping = [
            'sakit' => ['nama' => 'Izin Sakit', 'memotong_kuota' => false, 'level' => 1],
            'keperluan_keluarga' => ['nama' => 'Izin Keperluan Keluarga', 'memotong_kuota' => false, 'level' => 1],
            'keperluan_pribadi' => ['nama' => 'Izin Keperluan Pribadi', 'memotong_kuota' => false, 'level' => 1],
            'keperluan_mendesak' => ['nama' => 'Izin Keperluan Mendesak', 'memotong_kuota' => false, 'level' => 1],
            'keperluan_medis' => ['nama' => 'Izin Keperluan Medis', 'memotong_kuota' => false, 'level' => 1],
            'acara_keluarga' => ['nama' => 'Izin Acara Keluarga', 'memotong_kuota' => false, 'level' => 1],
            'keperluan_resmi' => ['nama' => 'Izin Keperluan Resmi', 'memotong_kuota' => false, 'level' => 2],
            'lainnya' => ['nama' => 'Izin Lainnya', 'memotong_kuota' => false, 'level' => 1]
        ];

        $jenisData = $jenisMapping[$jenisIzin] ?? $jenisMapping['lainnya'];

        return JenisPerizinan::firstOrCreate(
            ['nama' => $jenisData['nama']],
            [
                'deskripsi' => 'Jenis perizinan untuk ' . $jenisData['nama'],
                'memotong_kuota' => $jenisData['memotong_kuota'],
                'level_persetujuan_dibutuhkan' => $jenisData['level']
            ]
        );
    }

    /**
     * Calculate izin duration in minutes based on time type
     */
    private function calculateIzinDuration($waktuIzin, $jamMulai = null, $jamSelesai = null)
    {
        switch ($waktuIzin) {
            case 'full_day':
                return 8 * 60; // 8 hours in minutes
            case 'half_day_morning':
            case 'half_day_afternoon':
                return 4 * 60; // 4 hours in minutes
            case 'custom_time':
                if ($jamMulai && $jamSelesai) {
                    $start = Carbon::createFromFormat('H:i', $jamMulai);
                    $end = Carbon::createFromFormat('H:i', $jamSelesai);
                    return $end->diffInMinutes($start);
                }
                return 0;
            default:
                return 0;
        }
    }
}
