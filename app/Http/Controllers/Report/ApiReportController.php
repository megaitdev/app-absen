<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Jobs\GenerateReportEmployeesPicJob;
use App\Models\Cuti;
use App\Models\ftm\AttLog;
use App\Models\Holiday;
use App\Models\Izin;
use App\Models\JenisIzin;
use App\Models\Lembur;
use App\Models\Report;
use App\Models\VerifikasiAbsen;
use App\Models\mak_hrd\Employee;
use App\Models\Progress;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use SaeedVaziry\LaravelAsync\Facades\AsyncHandler;

class ApiReportController extends Controller
{

    function getAttLog($date, Employee $employee)
    {
        $date = Carbon::parse($date);
        $att_logs = AttLog::where('scan_date', $date)
            ->where('pin', $employee->pin)
            ->orderBy('scan_date')
            ->get();

        return response()->json($att_logs);
    }

    function getReport(Report $report)
    {
        return response()->json($report->load('shift'));
    }
    function getReportByDate($date, Employee $employee)
    {
        $report = Report::where('date', $date)
            ->where('employee_id', $employee->id)
            ->first();
        return response()->json($report);
    }

    function getVerifikasi(VerifikasiAbsen $verifikasi)
    {
        $dataScan = json_decode($verifikasi->data_scan);
        $verifikasi->scan = $dataScan;
        return response()->json($verifikasi->load('pic'));
    }
    function deleteVerifikasi(VerifikasiAbsen $verifikasi)
    {
        $report = Report::where('employee_id', $verifikasi->employee_id)
            ->where('date', $verifikasi->date)->first();

        $employee = Employee::find($verifikasi->employee_id);

        $shift = $employee->shiftByDate($verifikasi->date);

        $dataUpdate = [
            'jam_kerja_murni' => 0,
            'jam_kerja_efektif' => 0,
            'lembur_murni' => 0,
            'lembur_efektif' => 0,
            'lembur_akumulasi' => 0,
            'jam_hilang_murni' => $shift->total_menit_kerja,
            'jam_hilang_efektif' => $shift->total_menit_kerja,
            'is_verifikasi' => 0,
        ];
        switch ($verifikasi->jenis) {
            case 'scan_masuk':
                $dataUpdate['scan_masuk_murni'] = null;
                $dataUpdate['scan_masuk_efektif'] = null;
                $dataUpdate['status_masuk'] = 'Belum Scan Masuk';
                break;
            case 'scan_keluar':
                $dataUpdate['scan_keluar_murni'] = null;
                $dataUpdate['scan_keluar_efektif'] = null;
                $dataUpdate['status_keluar'] = 'Belum Scan Keluar';
                break;
            default:
                $dataUpdate['scan_masuk_murni'] = null;
                $dataUpdate['scan_masuk_efektif'] = null;
                $dataUpdate['status_masuk'] = 'Belum Scan Masuk';
                $dataUpdate['scan_keluar_murni'] = null;
                $dataUpdate['scan_keluar_efektif'] = null;
                $dataUpdate['status_keluar'] = 'Belum Scan Keluar';
                break;
        }

        if ($employee->pangkat_id == 1) {
            $dataUpdate['uk'] = 0;
            $dataUpdate['um'] = 0;
            $dataUpdate['ut'] = 0;
        }

        $report->update($dataUpdate);

        if ($verifikasi->lampiran) {
            Storage::delete('public/verifikasi/' . $verifikasi->lampiran);
        }
        $verifikasi->delete();
        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil dihapus',
            'data' => $report
        ]);
    }

    function storeVerifikasi(Request $request)
    {
        try {
            // Generate formatted filename
            if ($request->hasFile('lampiran_verifikasi')) {
                $file = $request->file('lampiran_verifikasi');
                $date = Carbon::parse($request->date);
                $employee_id = str_pad($request->employee_id, 4, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "VER-{$date}-{$employee_id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/verifikasi', $filename);
            }

            $data_scan[0] = $request->jam_masuk ? [
                'date' => $request->date,
                'scan_date' => $request->date . ' ' .   $request->jam_masuk,
                'jenis' => 'scan_masuk'
            ] : null;
            $data_scan[1] = $request->jam_keluar ? [
                'date' => $request->date,
                'scan_date' => $request->date . ' ' .   $request->jam_keluar,
                'jenis' => 'scan_keluar'
            ] : null;
            $data_scan = array_filter($data_scan);
            $data_scan = array_values($data_scan);
            $data_scan_string = json_encode($data_scan);

            // Create verifikasi record
            $verifikasi = VerifikasiAbsen::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'jenis' => $request->status,
                'pic' => Auth::user()->id ?? 'System',
                'jam_masuk' => $request->jam_masuk,
                'jam_keluar' => $request->jam_keluar,
                'keterangan' => $request->keterangan,
                'lampiran' => $filename ?? null,
                'data_scan' => $data_scan_string
            ]);

            // Update report record based on status
            $report = Report::where('employee_id', $request->employee_id)
                ->where('date', $request->date)->first();
            $report->update([
                'is_verifikasi' => 1,
                'scan_masuk_murni' => $request->jam_masuk ? $request->date . ' ' . $request->jam_masuk : $report->scan_masuk_murni,
                'scan_keluar_murni' => $request->jam_keluar ? $request->date . ' ' . $request->jam_keluar : $report->scan_keluar_murni,
                'scan_masuk_efektif' => $request->jam_masuk ? $request->date . ' ' . $request->jam_masuk : $report->scan_masuk_efektif,
                'scan_keluar_efektif' => $request->jam_keluar ? $request->date . ' ' . $request->jam_keluar : $report->scan_keluar_efektif,
            ]);

            $this->countingReport($report);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi berhasil disimpan',
                'data' => $request->all()
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function countingReport($report, $izin = null)
    {
        $date = $report->date;
        $carbonDate = Carbon::parse($date);
        $shift = $report->shiftDate();
        $check = (object)[
            'in' => Carbon::parse($report->scan_masuk_murni),
            'out' => Carbon::parse($report->scan_keluar_murni),
        ];

        $employee = Employee::findOrFail($report->employee_id);
        $pangkat_id = $employee->pangkat_id;

        $dataUpdate = [];
        $dataUpdate['istirahat_murni'] = $shift->total_menit_istirahat;
        $dataUpdate['istirahat_efektif'] = $shift->total_menit_istirahat;

        $jamHilangMurni = 0;
        $jamHilangEfektif = 0;

        // Simplify the code by removing unnecessary variables and directly using the report object
        $status = (object) [
            'check_in' => Carbon::parse($check->in)->lte($shift->jam_masuk) ? 'Tepat Waktu' : 'Terlambat',
            'check_out' => Carbon::parse($check->out)->gte($shift->jam_keluar) ? 'Tepat Waktu' : 'Pulang Cepat',
        ];

        $dataUpdate['status_masuk'] = $status->check_in;
        $dataUpdate['status_keluar'] = $status->check_out;

        if ($status->check_in != 'Tepat Waktu') {
            $selisih = $this->getSelisihWaktu($check->in, $shift->jam_masuk);
            $jamHilangEfektif += $selisih->efektif;
            $jamHilangMurni += $selisih->murni;
            $dataUpdate['scan_masuk_efektif'] = Carbon::parse($shift->jam_masuk)
                ->addMinutes($selisih->efektif)
                ->format('Y-m-d H:i:s');
        }
        if ($status->check_out != 'Tepat Waktu') {
            $selisih = $this->getSelisihWaktu($check->out, $shift->jam_keluar);
            $jamHilangEfektif += $selisih->efektif;
            $jamHilangMurni += $selisih->murni;
            $dataUpdate['scan_keluar_efektif'] = Carbon::parse($shift->jam_keluar)
                ->subMinutes($selisih->efektif)
                ->format('Y-m-d H:i:s');
        }

        $dataUpdate['jam_kerja_efektif'] = $shift->total_menit_kerja - $jamHilangEfektif;
        $dataUpdate['jam_kerja_murni'] = floor($check->in->diffInMinutes($check->out)) - $shift->total_menit_istirahat;
        // dump($check->out->lt($shift->jam_mulai_istirahat));
        if ($check->out->lt($shift->jam_mulai_istirahat)) {
            $dataUpdate['jam_kerja_efektif'] = $shift->total_menit_kerja - $jamHilangEfektif + $shift->total_menit_istirahat;
            $dataUpdate['jam_kerja_murni'] = floor($check->in->diffInMinutes($check->out));
        }

        if ($check->out->lt($shift->jam_mulai_istirahat)) {
            $jamHilangEfektif -= $shift->total_menit_istirahat;
            $jamHilangMurni -= $shift->total_menit_istirahat;
        }


        $dataUpdate['jam_hilang_murni'] = $jamHilangMurni;
        $dataUpdate['jam_hilang_efektif'] = $jamHilangEfektif;
        if ($employee->pangkat_id == 1) {
            $dataUpdate['potongan'] = $jamHilangEfektif;
            if ($izin && $izin->is_full_day == 0) {
                $dataUpdate['potongan'] = $jamHilangEfektif + $izin->jam_izin;
            }

            if (($dataUpdate['jam_kerja_efektif'] - $dataUpdate['potongan']) <= 240) {
                $dataUpdate['uk'] = 0;
                $dataUpdate['um'] = 0;
            } else {
                $dataUpdate['uk'] = 1;
                $dataUpdate['um'] = 1;
                $dataUpdate['ut'] = 1;
            }
        }
        if ($report->is_cuti == 1) {
            $dataUpdate['potongan'] = 0;
        }

        if ($status->check_out == 'Tepat Waktu') {
            $lemburMurni = $this->getSelisihWaktu($check->out, $shift->jam_keluar)->murni;
            $lemburEfektif = $lemburMurni;
            $lemburEfektif = $this->getEfektifJamLembur($lemburEfektif);
            $jenisLembur = $report->shift_id > 5 ? 'Terusan' : 'Lembur Libur';
            $lemburAkumulasi = $this->getAkumulasiJamLembur($lemburEfektif, $jenisLembur, $pangkat_id);
            $dataUpdate['lembur_murni'] = $lemburMurni;
            $dataUpdate['lembur_efektif'] = $lemburEfektif;
            $dataUpdate['lembur_akumulasi'] = $lemburAkumulasi;
            $day = $carbonDate->locale('id')->translatedFormat('l');
            if ($day != 'Jumat' && $report->is_lembur == 1) {
                $dataUpdate['uml'] = $lemburEfektif >= 180 ? 1 : 0;
            } else if ($report->is_lembur == 1) {
                $dataUpdate['uml'] = $lemburEfektif >= 120 ? 1 : 0;
            } else {
                $dataUpdate['uml'] = 0;
            }
        }

        // dd($dataUpdate);

        $report->update($dataUpdate);
    }

    function storeCuti(Request $request)
    {
        try {
            // Generate formatted filename
            if ($request->hasFile('lampiran_cuti')) {
                $file = $request->file('lampiran_cuti');
                $date = $request->date;
                $employee_id = str_pad($request->employee_id, 4, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "CUTI-{$date}-{$employee_id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/cuti', $filename);
            }

            $employee = Employee::find($request->employee_id);

            $shift = $employee->shiftByDate($request->date);

            // Create cuti record
            $cuti = Cuti::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'jenis_cuti' => $request->jenis_cuti,
                'pic' => Auth::user()->id ?? 'System',
                'keterangan' => $request->keterangan,
                'lampiran' => $filename ?? null,
            ]);

            // Update report record
            $report = Report::where('employee_id', $request->employee_id)
                ->where('date', $request->date)->first();
            $dataUpdate = [
                'status' => 'Tidak Hadir',
                'scan_masuk_murni' => null,
                'scan_masuk_efektif' => null,
                'status_masuk' => null,
                'scan_keluar_murni' => null,
                'scan_keluar_efektif' => null,
                'status_keluar' => null,
                'jam_kerja_murni' => $shift->total_menit_kerja,
                'jam_kerja_efektif' => $shift->total_menit_kerja,
                'istirahat_murni' => 0,
                'istirahat_efektif' => 0,
                'lembur_murni' => 0,
                'lembur_efektif' => 0,
                'lembur_akumulasi' => 0,
                'jam_hilang_murni' => 0,
                'jam_hilang_efektif' => 0,
                'potongan' => 0,
                'is_cuti' => 1,
                'keterangan' => 'cuti',
            ];
            if ($employee->pangkat_id == 1) {
                $dataUpdate['uk'] = 1;
                $dataUpdate['um'] = 1;
                $dataUpdate['ut'] = 1;
            }
            $report->update($dataUpdate);

            return response()->json([
                'success' => true,
                'message' => 'Cuti berhasil disimpan',
                'data' => $cuti
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function getCuti(Cuti $cuti)
    {
        return response()->json($cuti->load('pic', 'jenisCuti'));
    }

    function deleteCuti(Cuti $cuti)
    {
        $report = Report::where('employee_id', $cuti->employee_id)
            ->where('date', $cuti->date)->first();

        $employee = Employee::find($cuti->employee_id);

        $shift = $employee->shiftByDate($cuti->date);

        $dataUpdate = [
            'status' => 'Tidak Hadir',
            'scan_masuk_murni' => null,
            'scan_masuk_efektif' => null,
            'status_masuk' => null,
            'scan_keluar_murni' => null,
            'scan_keluar_efektif' => null,
            'status_keluar' => null,
            'jam_kerja_murni' => 0,
            'jam_kerja_efektif' => 0,
            'istirahat_murni' => 0,
            'istirahat_efektif' => 0,
            'lembur_murni' => 0,
            'lembur_efektif' => 0,
            'lembur_akumulasi' => 0,
            'jam_hilang_murni' => $shift->total_menit_kerja,
            'jam_hilang_efektif' => $shift->total_menit_kerja,
            'potongan' => $shift->total_menit_kerja,
            'is_cuti' => 0,
        ];

        if ($employee->pangkat_id == 1) {
            $dataUpdate['uk'] = 0;
            $dataUpdate['um'] = 0;
            $dataUpdate['ut'] = 0;
        }

        $report->update($dataUpdate);

        if ($cuti->lampiran) {
            Storage::delete('public/cuti/' . $cuti->lampiran);
        }
        $cuti->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cuti berhasil dihapus',
            'data' => $report
        ]);
    }

    function storeLembur(Request $request)
    {
        try {
            // Generate formatted filename
            if ($request->hasFile('lampiran_lembur')) {
                $file = $request->file('lampiran_lembur');
                $date = $request->date;
                $employee_id = str_pad($request->employee_id, 4, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "LEMBUR-{$date}-{$employee_id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/lembur', $filename);
            }

            $employee = Employee::find($request->employee_id);

            $shift = $employee->shiftByDate($request->date);

            $report = Report::where('employee_id', $request->employee_id)
                ->where('date', $request->date)->first();

            $mulaiLembur = $request->date . ' ' . $request->start_lembur;
            $selesaiLembur = $request->date . ' ' . $request->end_lembur;
            if ($request->end_date) {
                $selesaiLembur = $request->end_date . ' ' . $request->end_lembur;
            }

            $data_scan[0] = [
                'date' => $request->end_date ?? $request->date,
                'scan_date' => $selesaiLembur,
                'jenis' => 'scan_keluar'
            ];
            if ($report->is_verifikasi) {
                $verifikasi = $report->hasVerifikasi();
                $dataScanVerifikasi[0] = [
                    'date' => $verifikasi->date,
                    'scan_date' => $verifikasi->date . ' ' . $verifikasi->jam_masuk,
                    'jenis' => 'scan_masuk'
                ];
                $stringDataScanVerifikasi = json_encode($dataScanVerifikasi);
                $udpateVerifikasi[0] = [
                    'jenis' => 'scan_masuk',
                    'jam_keluar' => null,
                    'data_scan' => $stringDataScanVerifikasi
                ];
                $verifikasi->update($udpateVerifikasi);
            }
            // $data_scan = array_filter($data_scan);
            // $data_scan = array_values($data_scan);
            $data_scan_string = json_encode($data_scan);

            // Create lembur record
            Lembur::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'lembur' => $request->lembur,
                'mulai_lembur' => $mulaiLembur,
                'selesai_lembur' => $selesaiLembur,
                'pic' => Auth::user()->id ?? 'System',
                'lampiran' => $filename ?? null,
                'keterangan' => $request->keterangan,
                'data_scan' => $data_scan_string
            ]);

            // Update report record
            $dataUpdate = [
                'status' => 'Hadir',
                'scan_masuk_murni' => $report->scan_masuk_murni,
                'scan_keluar_murni' => $selesaiLembur,
                'is_lembur' => 1,
            ];
            $report->update($dataUpdate);
            $this->countingReport($report);

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil disimpan',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function storeLemburLibur(Request $request)
    {
        try {
            $date = $request->date;
            $mulaiLembur = $date . ' ' . $request->start_lembur;
            $selesaiLembur = $date . ' ' . $request->end_lembur;
            if ($request->end_date) {
                $selesaiLembur = $request->end_date . ' ' . $request->end_lembur;
            }
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $employee = Employee::find($request->employee_id);
            $shift = Shift::find(1); // Shift Lembur Libur
            $dataReport = [
                'nama_karyawan' => $employee->nama,
                'employee_id' => $employee->id,
                'pin' => $employee->pin,
                'date' => $date,
                'day' => $carbonDate->locale('id')->translatedFormat('l'),
                'shift_id' => $shift->id,
                'status' => 'Hadir',
                "scan_masuk_murni" => $mulaiLembur,
                "scan_keluar_murni" => $selesaiLembur,
                "jam_kerja_murni" => 0,
                'lembur_murni' => 0,
                'lembur_akumulasi' => 0,
                'is_lembur_libur' => 1,
                'utl' => 1,
            ];

            if ($request->hasFile('lampiran_lembur')) {
                $file = $request->file('lampiran_lembur');
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "LEMBUR-{$date}-{$employee->id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/lembur', $filename);
            }

            $data_scan[0] = [
                'date' => $date,
                'scan_date' => $mulaiLembur,
                'jenis' => 'scan_masuk'
            ];
            $data_scan[1] = [
                'date' => $date,
                'scan_date' => $selesaiLembur,
                'jenis' => 'scan_keluar'
            ];
            $data_scan_string = json_encode($data_scan);

            Lembur::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'lembur' => 'libur',
                'mulai_lembur' => $mulaiLembur,
                'selesai_lembur' => $selesaiLembur,
                'pic' => Auth::user()->id ?? 'System',
                'lampiran' => $filename ?? null,
                'keterangan' => $request->keterangan,
                'data_scan' => $data_scan_string
            ]);

            $reportController = new ReportController(new ScriptController, new CssController);
            $durasiLembur = $reportController->hitungJamKerjaMurni($request->start_lembur, $request->end_lembur);
            $durasiLembur = (int)floor($durasiLembur / 30) * 30;
            $dataReport['lembur_murni'] = $durasiLembur;
            $dataReport['lembur_efektif'] = $durasiLembur;
            $dataReport['jam_kerja_murni'] = $durasiLembur;
            $dataReport['lembur_akumulasi'] = $reportController->getJamLemburAkumulasi($durasiLembur, 'Lembur Libur', $employee->pangkat_id);
            $dataReport['jam_kerja_efektif'] = $durasiLembur;
            $dataReport['umll'] = $durasiLembur > 240 ? 1 : 0;
            Report::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'date' => $request->date
                ],
                $dataReport
            );

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil disimpan',
                'data' => $request->all()
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    function confirmLembur(Request $request)
    {
        try {
            // Generate formatted filename
            if ($request->hasFile('lampiran_lembur')) {
                $file = $request->file('lampiran_lembur');
                $date = $request->date;
                $employee_id = str_pad($request->employee_id, 4, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "LEMBUR-{$date}-{$employee_id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/lembur', $filename);
            }

            $employee = Employee::find($request->employee_id);

            $shift = $employee->shiftByDate($request->date);

            $report = Report::where('employee_id', $request->employee_id)
                ->where('date', $request->date)->first();


            $mulaiLembur = $request->date . ' ' . $request->start_lembur;
            $selesaiLembur = $request->date . ' ' . $request->end_lembur;
            if ($request->end_date) {
                $selesaiLembur = $request->end_date . ' ' . $request->end_lembur;
            }

            $data_scan[0] = [
                'date' => $request->end_date ?? $request->date,
                'scan_date' => $selesaiLembur,
                'jenis' => 'scan_keluar'
            ];
            $data_scan_string = json_encode($data_scan);

            // Create lembur record
            $lembur = Lembur::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'lembur' => $request->lembur,
                'mulai_lembur' => $mulaiLembur,
                'selesai_lembur' => $selesaiLembur,
                'pic' => Auth::user()->id ?? 'System',
                'lampiran' => $filename ?? null,
                'keterangan' => $request->keterangan,
                'data_scan' => $data_scan_string
            ]);

            // Update report record
            $dataUpdate = [
                'status' => 'Hadir',
                'scan_masuk_murni' => $report->scan_masuk_murni,
                'scan_keluar_murni' => $selesaiLembur,
                'is_lembur' => 1,
            ];
            $report->update($dataUpdate);
            $this->countingReport($report);

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil disimpan',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function getLembur(Lembur $lembur)
    {
        return response()->json($lembur->load('pic'));
    }

    function deleteLembur(Lembur $lembur)
    {
        if ($lembur->lampiran) {
            Storage::delete('public/lembur/' . $lembur->lampiran);
        }
        $employee = Employee::find($lembur->employee_id);
        $employee_id = $lembur->employee_id;
        $report = Report::where('employee_id', $lembur->employee_id)
            ->where('date', $lembur->date)->first();

        if ($lembur->lembur == 'terusan') {
            $report->update([
                'is_lembur' => 0,
                'utl' => 0,
                'uml' => 0
            ]);
        } else {
            $scanMasuk = Attlog::where('scan_date', $lembur->date)->where('pin', $employee->pin)->first();
            if (!$scanMasuk) {
                $report->delete();
            } else {
                $report->update([
                    'is_lembur_libur' => 0,
                    'utl' => 0,
                    'umll' => 0
                ]);
            }
        }
        $lembur->delete();

        $reportController = new ReportController(new ScriptController, new CssController);
        $periode = (object) [
            'start' => $lembur->date,
            'end' => $lembur->end_date ?? $lembur->date
        ];
        $reportController->generateReportEmployee($employee_id, $periode);

        return response()->json([
            'success' => true,
            'message' => 'Lembur berhasil dihapus',
            'data' => $lembur
        ]);
    }

    function storeIzin(Request $request)
    {
        try {
            // Generate formatted filename
            $date = $request->date;
            $isFullDay = (int)$request->is_full_day;
            if ($request->hasFile('lampiran_izin')) {
                $file = $request->file('lampiran_izin');
                $employee_id = str_pad($request->employee_id, 4, '0', STR_PAD_LEFT);
                $extension = $file->getClientOriginalExtension();
                $uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 3);
                $filename = "IZIN-{$date}-{$employee_id}-{$uuid}.{$extension}";
                $path = $file->storeAs('public/izin', $filename);
            }

            $mulaiIzin = $date . ' ' . $request->start_time;
            $selesaiIzin = $date . ' ' . $request->end_time;

            // Update report record
            $report = Report::where('employee_id', $request->employee_id)
                ->where('date', $request->date)->first();
            $jenisIzinList = JenisIzin::pluck('izin', 'id')->toArray();
            $jenisIzin = $request->jenis_izin;

            $updateReport = [
                'is_izin' => 1,
            ];
            if (isset($jenisIzinList[$jenisIzin])) {
                if ($jenisIzinList[$jenisIzin] == 'Izin Sakit') {
                    $updateReport['is_sakit'] = 1;
                }
                if ($jenisIzinList[$jenisIzin] == 'Izin Tidak Masuk') {
                    $updateReport['ut'] = 0;
                    $updateReport['um'] = 0;
                    $updateReport['uk'] = 0;
                }
            }

            $data_scan = [];
            $data_scan_string = null;
            if ($report->scan_masuk_murni == null && $isFullDay == 0) {
                $updateReport = [
                    'is_izin' => 1,
                    'scan_masuk_murni' => $selesaiIzin,
                    'scan_masuk_efektif' => $selesaiIzin,
                ];
                $data_scan[0] = [
                    'date' => $date,
                    'scan_date' => $selesaiIzin,
                    'jenis' => 'scan_masuk'
                ];
            }
            if ($report->scan_keluar_murni == null && $isFullDay == 0) {
                $updateReport = [
                    'is_izin' => 1,
                    'scan_keluar_murni' => $mulaiIzin,
                    'scan_keluar_efektif' => $mulaiIzin,
                ];
                $data_scan[0] = [
                    'date' => $date,
                    'scan_date' => $mulaiIzin,
                    'jenis' => 'scan_keluar'
                ];
            }
            if (!empty($data_scan)) {
                $data_scan = array_values($data_scan);
                $data_scan_string = json_encode($data_scan);
            }

            // Create izin record
            $izin = Izin::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'jenis_izin' => $request->jenis_izin,
                'mulai_izin' => $mulaiIzin,
                'is_full_day' => $isFullDay,
                'selesai_izin' => $selesaiIzin,
                'jam_izin' => $request->durasi,
                'pic' => Auth::user()->id ?? 'System',
                'keterangan' => $request->keterangan,
                'data_scan' => $data_scan_string,
                'lampiran' => $filename ?? null,
            ]);

            $report->update($updateReport);

            if (!$isFullDay == 1) {
                $this->countingReport($report, $izin);
            }

            return response()->json([
                'success' => true,
                'message' => 'Izin berhasil disimpan',
                'data' => [$request->all(), $updateReport]
            ]);
        } catch (\Exception $e) {
            // Delete uploaded file if exists and error occurs
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function getIzin(Izin $izin)
    {
        return response()->json($izin->load('pic', 'jenisIzin'));
    }

    function deleteIzin(Izin $izin)
    {
        $report = Report::where('employee_id', $izin->employee_id)
            ->where('date', $izin->date)->first();

        $employee_id = $izin->employee_id;
        if ($izin->is_full_day == 1) {
            $report->update([
                'is_izin' => 0,
            ]);
        } else {
            $uk = $um = ($report->jam_kerja_efektif - $report->jam_hilang_efektif > 240) ? 1 : 0;
            $report->update([
                'jam_hilang_efektif' => abs($report->jam_hilang_efektif - $izin->jam_izin),
                'potongan' => abs($report->jam_hilang_efektif - $izin->jam_izin),
                'is_izin' => 0,
                'uk' => $uk,
                'um' => $um
            ]);
        }

        if ($izin->lampiran) {
            Storage::delete('public/izin/' . $izin->lampiran);
        }
        $izin->delete();
        $reportController = new ReportController(new ScriptController, new CssController);
        $periode = (object) [
            'start' => $izin->date,
            'end' => $izin->date
        ];
        $reportController->generateReportEmployee($employee_id, $periode);
        return response()->json([
            'success' => true,
            'message' => 'Izin berhasil dihapus',
            'data' => $report
        ]);
    }

    function printEmployeeReport($employee_id)
    {
        $controller = new Controller();
        $periode = $controller->getPeriodeReport();
        $startDate = $periode->start;
        $endDate = $periode->end;
        $employee = Employee::find($employee_id);
        $reports = Report::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee_id)
            ->orderBy('date', 'asc')
            ->get();


        $startDateDmY = Carbon::parse($startDate)->format('d/m/Y');
        $endDateDmY = Carbon::parse($endDate)->format('d/m/Y');

        $year = Carbon::parse($startDate)->format('Y');

        $filename = public_path('storage/templates/laporan-absen-karyawan-v1.xlsx');
        $reader = new ReaderXlsx;
        $spreadsheet = $reader->load($filename);


        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absen Karyawan');
        $sheet->setCellValue('C2', $employee->nama);
        $sheet->setCellValue('C3', $employee->pangkat_id > 1 ? 'Pengatur' : 'Operator');
        $sheet->setCellValue('C4', $startDateDmY . ' - ' . $endDateDmY);
        $sheet->setCellValue('C5', $periode->name . ' ' . $year);
        $sheet->setCellValue('F2', $employee->nip);
        $sheet->setCellValue('F3', $employee->unit->unit);
        $row = 2;


        $reports = Report::whereBetween('date', [$periode->start, $periode->end])
            ->where('employee_id', $employee_id)
            ->with('shift')
            ->with(['verifikasi' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->with(['cuti' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->with(['lembur' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->get()
            ->keyBy('date');


        $index = 9;
        $holidays = Holiday::whereBetween('date', [$periode->start, $periode->end])->get()->keyBy('date');
        foreach (CarbonPeriod::create($periode->start, $periode->end) as $forDate) {
            $day = $forDate->isoFormat('dddd');
            $tanggal = $forDate->isoFormat('DD MMMM Y');
            $date = $forDate->format('Y-m-d');
            $sheet->setCellValue('B' . $index, $day);
            $sheet->setCellValue('C' . $index, $tanggal);

            if (!empty($reports[$date])) {
                $sheet->setCellValue('D' . $index, $reports[$date]['shift']['name'] ?? null);
                $sheet->setCellValue('E' . $index, $reports[$date]['shift']['jam_masuk'] ?? null);
                $sheet->setCellValue('F' . $index, $reports[$date]->scanMasuk());
                $sheet->setCellValue('G' . $index, $reports[$date]['shift']['jam_keluar'] ?? null);
                $sheet->setCellValue('H' . $index, $reports[$date]->scanKeluar());
                $sheet->setCellValue('I' . $index, $reports[$date]->durasiMurni());
                $sheet->setCellValue('J' . $index, $reports[$date]->durasiEfektif());
                $sheet->setCellValue('K' . $index, $reports[$date]->jamHilangMurni());
                $sheet->setCellValue('L' . $index, $reports[$date]->jamHilangEfektif());
                $sheet->setCellValue('M' . $index, $reports[$date]->potongan());
                $sheet->setCellValue('N' . $index, $reports[$date]['ut']);
                $sheet->setCellValue('O' . $index, $reports[$date]['uk']);
                $sheet->setCellValue('P' . $index, $reports[$date]['um']);
                $sheet->setCellValue('Q' . $index, $reports[$date]->lemburMurni());
                $sheet->setCellValue('R' . $index, $reports[$date]->lemburEfektif());
                $sheet->setCellValue('S' . $index, $reports[$date]->lemburAkumulasi());
                $sheet->setCellValue('T' . $index, $reports[$date]['utl']);
                $sheet->setCellValue('U' . $index, $reports[$date]['uml']);
                $sheet->setCellValue('V' . $index, $reports[$date]['umll']);

                if ($reports[$date]['is_lembur_libur'] == 1) {
                    $sheet->getStyle('A' . $index . ':U' . $index)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
                }
                if (is_null($reports[$date]->scanMasuk()) || is_null($reports[$date]->scanKeluar())) {
                    $sheet->getStyle('A' . $index . ':U' . $index)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFCC');
                }
                $shift = $reports[$date]['shift'];
                if (empty($shift)) {
                    $sheet->getStyle('A' . $index . ':U' . $index)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFCC');
                }
            } else if (!empty($holidays[$date])) {
                $sheet->setCellValue('D' . $index, 'Libur ' . $holidays[$date]['note']);
                $sheet->getStyle('A' . $index . ':U' . $index)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
            } else {
                $sheet->setCellValue('D' . $index, 'Libur Rutin');
                $sheet->getStyle('A' . $index . ':U' . $index)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCCCC');
            }
            $index++;
        }
        $writer = new WriterXlsx($spreadsheet);
        $filename = 'report-employee-' . $employee->nama . '.xlsx';
        $writer->save($filename);
        return response()->download($filename)->deleteFileAfterSend(true);
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $filename . '"');
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
    }

    function getUniqueUnitNames(array $employeeIds)
    {
        // Dengan menggunakan Eloquent (Model)
        return Employee::whereIn('id', $employeeIds)
            ->with('unit')
            ->get()
            ->pluck('unit.unit')
            ->unique()
            ->values()
            ->toArray();
    }

    function getUniquePangkatNames(array $employeeIds)
    {
        // Mengambil pangkat unik langsung dari database
        $input = Employee::whereIn('id', $employeeIds)
            ->with('pangkat')
            ->get()
            ->pluck('pangkat.pangkat')
            ->map(function ($item) {
                // Menghilangkan angka dan spasi langsung di sini
                return preg_replace('/[^a-zA-Z]/', '', $item);
            })
            ->unique() // Menyaring elemen yang duplikat
            ->values() // Mengatur ulang indeks
            ->toArray();

        return $input;
    }


    function printPICReport($picID)
    {
        $arc = new ApiReportController();
        $pic = User::find($picID);
        $listEmployeeIDs = json_decode($pic->employees, true);
        $employees = Employee::whereIn('id', $listEmployeeIDs)->get(['id', 'nama', 'nip'])->keyBy('id')->toArray();
        $listUnitNames = count($listUnitNames = $this->getUniqueUnitNames($listEmployeeIDs)) > 1 ? implode(', ', $listUnitNames) : (count($listUnitNames) == 1 ? $listUnitNames[0] : '-');
        $listPangkatNames = count($listPangkatNames = $this->getUniquePangkatNames($listEmployeeIDs)) > 1 ? implode('-', $listPangkatNames) : (count($listPangkatNames) == 1 ? $listPangkatNames[0] : '-');

        $periode = $arc->getPeriodeReport($picID);
        $startDate = $periode->start;
        $endDate = $periode->end;

        $startDateDmY = Carbon::parse($startDate)->format('d/m/Y');
        $endDateDmY = Carbon::parse($endDate)->format('d/m/Y');
        $year = Carbon::parse($startDate)->format('Y');

        $filename = public_path('storage/templates/laporan-absen-unit-v1.xlsx');
        $reader = new ReaderXlsx();
        $spreadsheet = $reader->load($filename);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Set border style (e.g., thin)
                    'color' => ['argb' => '000000'], // Set border color (black)
                ],
            ],
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absen PIC ' . $pic->pic);
        $sheet->setCellValue('A2', 'PIC ' . $pic->pic);
        $sheet->setCellValue('C3', $listUnitNames);
        $sheet->setCellValue('C4', $listPangkatNames);
        $sheet->setCellValue('C5', $startDateDmY . ' - ' . $endDateDmY);
        $sheet->setCellValue('C6', $periode->name . ' ' . $year);
        $sheet->setCellValue('I3', $pic->nama);
        $sheet->setCellValue('I4', Carbon::now()->format('d/m/Y H:i'));
        $sheet->setCellValue('I5', count($listEmployeeIDs) . ' Karyawan');

        $row = 10;
        $indexEmployee = 1;
        foreach ($listEmployeeIDs as $employee_id) {
            $reportEmployee = $arc->getAttendanceStats($employee_id, $periode)->getData()->data;
            $sheet->setCellValue('A' . $row, $indexEmployee);
            $sheet->setCellValue('B' . $row, $employees[$employee_id]['nip']);
            $sheet->setCellValue('C' . $row, $employees[$employee_id]['nama']);
            $sheet->setCellValue('D' . $row, $reportEmployee->total_ut);
            $sheet->setCellValue('E' . $row, $reportEmployee->total_um);
            $sheet->setCellValue('F' . $row, $reportEmployee->total_uk);
            $sheet->setCellValue('G' . $row, $reportEmployee->total_jam_potongan);
            $sheet->setCellValue('H' . $row, $reportEmployee->total_utl);
            $sheet->setCellValue('I' . $row, $reportEmployee->total_uml);
            $sheet->setCellValue('J' . $row, $reportEmployee->total_umll);
            $sheet->setCellValue('K' . $row, $reportEmployee->total_jam_lembur);
            $sheet->setCellValue('L' . $row, $reportEmployee->total_jam_akumulasi_lembur);
            $sheet->getStyle('A' . $row . ':' . 'L' . $row)->applyFromArray($styleArray);
            $row++;
            $indexEmployee++;
        }

        $writer = new WriterXlsx($spreadsheet);
        $filename = 'report-pic-' . $pic->nama . '.xlsx';
        $writer->save($filename);
        return response()->download($filename)->deleteFileAfterSend(true);
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $filename . '"');
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
    }



    function getAttendanceStats($employee_id, $periode = null)
    {
        if (is_null($periode)) {
            $controller = new Controller();
            $periode = $controller->getPeriodeReport();
        }
        $startDate = $periode->start;
        $endDate = $periode->end;

        $employee = Employee::find($employee_id);
        $shifts = $employee->shifts($startDate, $endDate);
        $reports = Report::where('employee_id', $employee_id)
            ->whereNotNull('shift_id')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('shift')
            ->with(['izin' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->with(['cuti' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->with(['lembur' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->with(['verifikasi' => function ($query) use ($employee_id) {
                $query->where('employee_id', $employee_id);
            }])
            ->get();


        $jenisIzinList = JenisIzin::pluck('izin', 'id')->toArray();



        $stats = array_fill_keys(
            [
                'total_hari',
                'hadir',
                'tidak_hadir',
                'cuti',
                'izin',
                'sakit',
                'alpha',
                'verifikasi',
                'pergi_kembali',
                'pergi_tidak_kembali',
                'terlambat',
                'pulang_cepat',
                'lembur',
                'total_jam_potongan',
                'total_menit_potongan',
            ],
            0
        );
        $stats['total_hari'] = count($shifts);
        $stats['verifikasi'] = $reports->where('is_verifikasi', 1)->count();

        $totalMenitKerja = $reports->sum(function ($report) {
            if (is_null($report->shift_id)) {
                return 0;
            }
            return $report->shift->total_menit_kerja;
        });

        $stats['total_menit_kerja'] = $totalMenitKerja;
        $stats['total_jam_kerja'] = $totalMenitKerja / 60;

        $totalMenitHadir = $reports->sum(function ($report) {
            if ($report->status == 'Hadir') {
                return $report->jam_kerja_efektif;
            }
            return 0;
        });

        $stats['total_menit_hadir'] = $totalMenitHadir;
        $stats['total_jam_hadir'] = $totalMenitHadir / 60;

        $totalMenitTidakHadir = $reports->sum(function ($report) {
            if ($report->status != 'Hadir') {
                if ($report->is_cuti == 1) {
                    return $report->jam_kerja_efektif;
                } else {
                    return $report->jam_hilang_efektif;
                }
            }
            return 0;
        });

        $stats['total_menit_tidak_hadir'] = $totalMenitTidakHadir;
        $stats['total_jam_tidak_hadir'] = $totalMenitTidakHadir / 60;

        $totalMenitHilan = $reports->sum(function ($report) {
            return $report->jam_hilang_efektif;
        });
        $stats['total_menit_hilang'] = $totalMenitHilan;
        $stats['total_jam_hilang'] = $totalMenitHilan / 60;


        // Potongan
        if ($employee->pangkat_id == 1) {
            $totalMenitPotongan = $reports->sum(function ($report) use ($jenisIzinList) {
                if ($report->izin && $jenisIzinList[$report->izin->jenis_izin] != 'Izin Sakit' && $report->is_cuti != 1) {
                    return $report->jam_hilang_efektif;
                }
                if ($report->status == 'Hadir' && ($report->status_masuk == 'Terlambat' || $report->status_keluar == 'Pulang Cepat')) {
                    return $report->jam_hilang_efektif;
                }
                return 0;
            });
            $stats['total_menit_potongan'] = $totalMenitPotongan;
            $stats['total_jam_potongan'] = $totalMenitPotongan / 60;
        }

        $totalMenitTerlambat = $reports->sum(function ($report) {
            if ($report->status_masuk == 'Terlambat') {
                return $report->jam_hilang_efektif;
            }
            return 0;
        });

        $stats['total_menit_terlambat'] = $totalMenitTerlambat;
        $stats['total_jam_terlambat'] = $totalMenitTerlambat / 60;

        $totalMenitPulangCepat = $reports->sum(function ($report) {
            if ($report->status_keluar == 'Pulang Cepat') {
                return $report->jam_hilang_efektif;
            }
            return 0;
        });

        $stats['total_menit_pulang_cepat'] = $totalMenitPulangCepat;
        $stats['total_jam_pulang_cepat'] = $totalMenitPulangCepat / 60;

        $totalMenitLembur = $reports->sum(function ($report) {
            if ($report->is_lembur == 1) {
                return $report->lembur_efektif;
            }
            if ($report->is_lembur_libur == 1) {
                return $report->jam_kerja_efektif;
            }
            return 0;
        });

        $stats['total_menit_lembur'] = $totalMenitLembur;
        $stats['total_jam_lembur'] = $totalMenitLembur / 60;

        $totalMenitAkumulasiLembur = $reports->sum(function ($report) {
            if ($report->is_lembur == 1 || $report->is_lembur_libur == 1) {
                return $report->lembur_akumulasi;
            }
            return 0;
        });

        $stats['total_menit_akumulasi_lembur'] = $totalMenitAkumulasiLembur;
        $stats['total_jam_akumulasi_lembur'] = $totalMenitAkumulasiLembur / 60;

        $stats['total_ut'] = 22;
        $stats['total_um'] = 22;
        $stats['total_uk'] = 22;

        if ($employee->pangkat_id == 1) {
            $stats['total_ut'] = $reports->sum(function ($report) {
                return $report->ut;
            });

            $stats['total_uk'] = $reports->sum(function ($report) {
                return $report->uk;
            });

            $stats['total_um'] = $reports->sum(function ($report) {
                return $report->um;
            });
        }
        $stats['total_uml'] = $reports->sum(function ($report) {
            return $report->uml;
        });

        $stats['total_umll'] = $reports->sum(function ($report) {
            return $report->umll;
        });

        $stats['total_utl'] = $reports->sum(function ($report) {
            return $report->utl;
        });


        foreach ($reports as $report) {
            if ($report->status == 'Hadir' && $report->shift_id) {
                $stats['hadir']++;
                if ($report->status_masuk == 'Terlambat') {
                    $stats['terlambat']++;
                }
                if ($report->status_keluar == 'Pulang Cepat') {
                    $stats['pulang_cepat']++;
                }
                if ($report->is_lembur == 1 || $report->is_lembur_libur == 1) {
                    $stats['lembur']++;
                }
            } else {
                if ($report->is_cuti == 1) {
                    $stats['cuti']++;
                }
                if ($report->is_izin == 1) {
                    $stats['izin']++;
                }
                if ($report->izin) {
                    if (isset($jenisIzinList[$report->izin->jenis_izin])) {
                        if ($jenisIzinList[$report->izin->jenis_izin] == 'Izin Sakit') {
                            $stats['sakit']++;
                        }
                        if ($jenisIzinList[$report->izin->jenis_izin] == 'Izin Tidak Masuk') {
                            $stats['alpha']++;
                        }
                        if ($jenisIzinList[$report->izin->jenis_izin] == 'Izin Pergi Kembali') {
                            $stats['pergi_kembali']++;
                        }
                        if ($jenisIzinList[$report->izin->jenis_izin] == 'Izin Pergi Tidak Kembali') {
                            $stats['pergi_tidak_kembali']++;
                        }
                    }
                }
                $stats['tidak_hadir']++;
            }
        }

        $stats['persentase_kehadiran'] = $stats['total_hari'] > 0 ? round(($stats['hadir'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_tidak_hadir'] = $stats['total_hari'] > 0 ? round(($stats['tidak_hadir'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_cuti'] = $stats['total_hari'] > 0 ? round(($stats['cuti'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_izin'] = $stats['total_hari'] > 0 ? round(($stats['izin'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_sakit'] = $stats['total_hari'] > 0 ? round(($stats['sakit'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_alpha'] = $stats['total_hari'] > 0 ? round(($stats['alpha'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_pergi_kembali'] = $stats['total_hari'] > 0 ? round(($stats['pergi_kembali'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_pergi_tidak_kembali'] = $stats['total_hari'] > 0 ? round(($stats['pergi_tidak_kembali'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_lembur'] = $stats['total_hari'] > 0 ? round(($stats['lembur'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_terlambat'] = $stats['total_hari'] > 0 ? round(($stats['terlambat'] / $stats['total_hari']) * 100, 1) : 0;
        $stats['persentase_pulang_cepat'] = $stats['total_hari'] > 0 ? round(($stats['pulang_cepat'] / $stats['total_hari']) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }


    // Section: Generate Report
    function asyncGenerateReportEmployeesPic($picID)
    {
        $progress = Progress::where('name', 'progress-generate-report-employee-' . $picID)->first();
        if ($progress && $progress->total > 0 && $progress->steps < $progress->total) {
            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'Report sudah di generate'
            ]);
        }
        if ($progress) {
            $progress->delete();
        }
        $arc = new ApiReportController();
        $periode = $arc->getPeriodeReport($picID);
        $pic = User::find($picID);
        $totalEmployes = count(json_decode($pic->employees, true));
        $timeEveryEmployee = 5; // in Second
        $timeout = $totalEmployes * $timeEveryEmployee;

        AsyncHandler::timeout($timeout)->dispatch(function () use ($picID, $periode, $timeout) {
            info("Starting report generation for user: " . $picID);
            info("Periode: " . $periode->start . " - " . $periode->end);
            $startDatetime = Carbon::now();
            $endDatetime = $startDatetime->copy()->addSeconds($timeout);
            info("Timeout: " . $startDatetime . " - " . $endDatetime);
            info("PHP Version: " . phpversion());

            $arc = new ApiReportController();
            $arc->generateReportPic($picID, $periode);

            info("Completed report generation for user: " . $picID);
        });
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Report sedang di generate'
        ]);
    }

    function generateReportPic($picID, $periode)
    {
        $arc = new ApiReportController();
        $pic = User::find($picID);
        $listEmployeeIDs = json_decode($pic->employees, true);
        $indexEmployee = 0;
        foreach ($listEmployeeIDs as $employee_id) {
            $arc = new ApiReportController();
            $arc->generateReportEmployee($employee_id, $periode);
            Progress::updateOrCreate(
                [
                    'name' => 'progress-generate-report-employee-' . $picID,
                ],
                [
                    'total' => count($listEmployeeIDs),
                    'steps' => $indexEmployee + 1,
                    'persentase' => ($indexEmployee + 1) / count($listEmployeeIDs) * 100
                ]
            );
            info("Progress: " . ($indexEmployee + 1) . " / " . count($listEmployeeIDs));
            $indexEmployee++;
        }
    }

    function getPeriodeReport($picID = null)
    {
        $arc = new ApiReportController();
        $periode = Session::get('periode-' . $picID);
        if (!$periode) {
            $defaultStart = Carbon::now()->subMonth()->startOfMonth()->day(21)->toDateString();
            $defaultEnd = Carbon::now()->day(20)->toDateString();
            $name = $arc->getPeriodeName($defaultStart, $defaultEnd);
            Session::put('periode-' . $picID, ['start' => $defaultStart, 'end' => $defaultEnd, 'name' => $name]);
        }
        return (object)Session::get('periode-' . $picID);
    }

    function getPeriodeName(string $start, string $end): string
    {
        $startDate = new Carbon($start);
        $endDate = new Carbon($end);

        $startMonth = $startDate->month;
        $endMonth = $endDate->month;

        $name = 'Custom Range';

        if ($endMonth - $startMonth === 1 && $startDate->day === 21 && $endDate->day === 20) {
            $name = $endDate->locale('id_ID')->monthName;
        }

        return $name;
    }

    function generateReportEmployee($employee_id, $periode = null)
    {
        $generate = new GenerateReportController();
        if (!$periode) {
            $periode = $this->getPeriodeReport();
        }
        $startDate = $periode->start;
        $endDate = $periode->end;

        $employee = Employee::where('id', $employee_id)->first(['nama', 'pin', 'id', 'pangkat_id']);
        if (!$employee) {
            throw new Exception('Employee not found');
        }
        $employeePin = $employee->pin;

        $shifts = $employee->shifts($startDate, $endDate);
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();
        $izins = Izin::where('employee_id', $employee_id)->whereBetween('date', [$startDate, $endDate])->get()->keyBy('date');

        Report::whereIn('date', $holidays)->where('is_lembur_libur', 0)->delete();
        $tempVerifikasi = [];
        VerifikasiAbsen::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee_id)
            ->get(['date', 'data_scan'])
            ->map(function ($ver) use (&$tempVerifikasi) {
                $data = json_decode($ver->data_scan);
                $tempVerifikasi = array_merge($tempVerifikasi, $data);
                return $ver;
            });
        $tempLembur = [];
        Lembur::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee_id)
            ->get(['date', 'data_scan', 'lembur'])
            ->map(function ($lembur) use (&$tempLembur) {
                $data = json_decode($lembur->data_scan);
                $data[0]->lembur = $lembur->lembur;
                $tempLembur = array_merge($tempLembur, $data);
                return $lembur;
            });
        $tempIzin = [];
        Izin::whereBetween('date', [$startDate, $endDate])
            ->where('employee_id', $employee_id)
            ->where('data_scan', '!=', null)
            ->get(['date', 'data_scan'])
            ->map(function ($izin) use (&$tempIzin) {
                $data = json_decode($izin->data_scan);
                $tempIzin = array_merge($tempIzin, $data);
                return $izin;
            });

        $shifts = array_filter($shifts, function ($shift) use ($holidays) {
            return !in_array($shift->date, $holidays);
        });

        $endDateForAttLog = (new Carbon($endDate))->addDay(1)->format('Y-m-d');
        $scanLogs = AttLog::whereBetween('scan_date', [$startDate, $endDateForAttLog])
            ->where('pin', $employeePin)
            ->orderBy('scan_date')
            ->get(['scan_date'])
            ->map(function ($scanLog) {
                $scanLog->date = Carbon::parse($scanLog->scan_date)->format('Y-m-d');
                return $scanLog;
            });

        $dataReport = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $day = $date->locale('id')->translatedFormat('l');
            $date = $date->format('Y-m-d');
            $report = [
                'nama_karyawan' => $employee->nama,
                'employee_id' => $employee->id,
                'pin' => $employee->pin,
                'date' => $date,
                'day' => $day,
                'status' => 'Tidak Hadir',
                "scan_masuk_murni" => null,
                "scan_keluar_murni" => null,
                "scan_masuk_efektif" => null,
                "scan_keluar_efektif" => null,
                "status_masuk" => null,
                "status_keluar" => null,
                'is_cuti' => 0,
                'is_izin' => 0,
                'is_sakit' => 0,
                'potongan' => 0,
                'is_lembur' => 0,
                'is_verifikasi' => 0,
                'is_lembur_libur' => 0,
                'lembur_akumulasi' => 0,
            ];

            $scanLogForDate = $scanLogs->where('date', $date);

            $verifikasiForDate = array_values(array_filter($tempVerifikasi, function ($item) use ($date) {
                return $item->date == $date;
            }));

            if ($verifikasiForDate) {
                $report['is_verifikasi'] = 1;
            }

            $scanLogForDate = $scanLogForDate->concat($verifikasiForDate);

            $lemburForDate = array_values(array_filter($tempLembur, function ($item) use ($date) {
                return $item->date == $date;
            }));

            if ($lemburForDate) {
                if ($lemburForDate[0]->lembur == 'terusan') {
                    $report['is_lembur'] = 1;
                } else {
                    $report['is_lembur_libur'] = 1;
                }
            }

            $scanLogForDate = $scanLogForDate->concat($lemburForDate);

            $izinForDate = array_values(array_filter($tempIzin, function ($item) use ($date) {
                return $item->date == $date;
            }));

            $scanLogForDate = $scanLogForDate->concat($izinForDate);

            if ($izins->has($date)) {
                $izinForDate = $izins[$date];
                $report['is_izin'] = 1;
            }

            $shift = $shifts[$date] ?? null;

            if (!$shift) {
                $report['shift_id'] = null;
                if ($scanLogForDate->isNotEmpty()) {
                    $check = $generate->getCheckInCheckOutWithoutShift($scanLogForDate);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $durasiLembur = $generate->hitungJamKerjaMurni($check->in, $check->out);
                    $report['jam_kerja_murni'] = $durasiLembur;
                    $report['lembur_murni'] = $durasiLembur;
                    $report['jam_kerja_efektif'] = floor($durasiLembur / 30) * 30;
                    $report['lembur_efektif'] = floor($durasiLembur / 30) * 30;
                    if (!empty($lemburForDate)) {
                        $report['is_lembur_libur'] = 1;
                        $report['shift_id'] = $this->shiftLemburID();
                        $report['utl'] = 1;
                        $report['umll'] = $durasiLembur > 240 ? 1 : 0;
                    }
                    $report['uk'] = 0;
                    $report['um'] = 0;
                    $report['ut'] = 0;
                } else {
                    continue;
                }
            } else {
                $report['shift_id'] = $shift->id;
                if ($scanLogForDate->isNotEmpty()) {
                    $check = $generate->getCheckInCheckOut($scanLogForDate, $shift);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $report['scan_masuk_efektif'] = $shift->jam_masuk;
                    $report['scan_keluar_efektif'] = $shift->jam_keluar;
                    $jamHilangMurni = 0;
                    $jamHilangEfektif = 0;

                    $status = $generate->getStatusCheckInCheckOut($check, $shift);
                    $jam_selesai_istirahat = Carbon::parse($shift->jam_selesai_istirahat);

                    $report['status_masuk'] = $status->check_in;
                    $report['status_keluar'] = $status->check_out;

                    if ($status->check_in != 'Tepat Waktu') {
                        $terlambat = $generate->getJamHilangTerlambat($check->in, $shift);
                        $jamHilangEfektif += $terlambat->efektif;
                        $jamHilangMurni += $terlambat->murni;
                        $report['scan_masuk_efektif'] = Carbon::parse($shift->jam_masuk)
                            ->addMinutes($terlambat->efektif)
                            ->format('Y-m-d H:i:s');
                    }

                    if ($status->check_out != 'Tepat Waktu') {
                        $pulangCepat = $generate->getJamHilangPulangCepat($check->out, $shift);
                        if ($check->out !== null) {
                            if ($jam_selesai_istirahat->gt($check->out)) {
                                $jamHilangMurni -= $shift->total_menit_istirahat;
                                $jamHilangEfektif -= $shift->total_menit_istirahat;
                            }
                        }
                        $jamHilangEfektif += $pulangCepat->efektif;
                        $jamHilangMurni += $pulangCepat->murni;

                        $report['scan_keluar_efektif'] = Carbon::parse($shift->jam_keluar)
                            ->subMinutes($pulangCepat->efektif)
                            ->format('Y-m-d H:i:s');
                    }

                    if ($status->check_in == 'Belum Scan Masuk' || $status->check_out == 'Belum Scan Keluar') {
                        $jamHilangMurni = $shift->total_menit_kerja;
                        $jamHilangEfektif = $shift->total_menit_kerja;
                    }

                    $report['istirahat_murni'] = $shift->total_menit_istirahat;
                    $report['istirahat_efektif'] = $shift->total_menit_istirahat;

                    $report['jam_hilang_murni'] = $jamHilangMurni;
                    $report['jam_hilang_efektif'] = $jamHilangEfektif;
                    if ($employee->pangkat_id == 1) {
                        $report['potongan'] = $jamHilangEfektif;
                        if ($izinForDate && $izinForDate->is_full_day == 0) {
                            $report['potongan'] = $jamHilangEfektif + $izinForDate->jam_izin;
                        }
                    }

                    $report['jam_kerja_murni'] = $shift->total_menit_kerja - $jamHilangMurni;
                    if ($check->in && $check->out) {
                        $report['jam_kerja_murni'] = floor(Carbon::parse($check->in)->diffInMinutes($check->out)) - $shift->total_menit_istirahat;
                        if ($jam_selesai_istirahat->gt($check->out)) {
                            $report['jam_kerja_murni'] = floor(Carbon::parse($check->in)->diffInMinutes($check->out));
                        }
                    }

                    $report['jam_kerja_efektif'] = $shift->total_menit_kerja - $jamHilangEfektif;

                    if ($status->check_out == 'Tepat Waktu') {
                        $lembur = $generate->getJamLemburTerusan($check->out, $shift);
                        $report['lembur_murni'] = $lembur->murni;
                        if (Carbon::parse($check->out)->gt(Carbon::parse($date . ' 18:45'))) {
                            $lembur->efektif -= 60;
                        }
                        $report['lembur_efektif'] = $lembur->efektif;
                        $report['lembur_akumulasi'] = $generate->getJamLemburAkumulasi($lembur->efektif, 'Terusan', $employee->pangkat_id);
                    }

                    if ($employee->pangkat_id == 1 && $report['jam_kerja_efektif'] <= 240) {
                        $report['uk'] = 0;
                        $report['um'] = 0;
                    }
                } else {
                    if ($employee->pangkat_id == 1) {
                        $report['uk'] = 0;
                        $report['um'] = 0;
                        $report['ut'] = 0;
                    }
                    $report['jam_hilang_murni'] = $shift->total_menit_kerja;
                    $report['jam_hilang_efektif'] = $shift->total_menit_kerja;
                    if ($employee->pangkat_id == 1) {
                        $report['potongan'] = $shift->total_menit_kerja;
                    }
                }
            }
            Report::updateOrCreate(
                [
                    'date' => $date,
                    'pin' => $employee->pin,
                ],
                $report
            );
            $dataReport[$date] = $report;
        }

        try {
            $response = response()->json([
                'success' => true,
                'data' => $dataReport
            ]);
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    function getProgressGenerateReportPic($picID)
    {
        return Progress::where('name', 'progress-generate-report-employee-' . $picID)->first() ?? (object) [
            'total' => 0,
            'steps' => 0,
            'persentase' => 0
        ];
    }
    // End of Section: Generate Report

    function datatableReportPic(Request $request)
    {
        if (Auth::user()->role == 'pic') {
            $user = Auth::user();
            $listEmployee = json_decode($user->employees, true);
            return DataTables()->of(
                Employee::query()
                    ->whereIn('id', $listEmployee)
                    ->where('is_deleted', 0)
                    ->with(['unit', 'divisi'])
            )
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $action = '<div class="d-flex justify-content-center">';
                    $action .= '<div onclick="javascript:generateReportEmployee(' . $row->id . ',`' . $row->nama . '`)" class="btn btn-sm btn-outline-warning m-1"><i class="fas fa-sync-alt"></i></div>';
                    $action .= '<a href="' . url('/report/employee/' . $row->id) . '"  class="btn btn-sm btn-outline-success m-1"><i class="fas fa-eye"></i></a>';
                    $action .= '</div>';
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses sebagai PIC'
            ]);
        }
    }
}
