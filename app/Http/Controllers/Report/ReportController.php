<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\Report;
use App\Models\ScanLog;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Unit;
use App\Models\ftm\AttLog;
use App\Http\Controllers\Controller;
use App\Jobs\generateReportJob;
use App\Models\Progress;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use SaeedVaziry\LaravelAsync\Facades\AsyncHandler;

class ReportController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }
    function report()
    {
        $data = [
            'title' => 'Report',
            'slug' => 'report',
            'scripts' => $this->script->getListScript('report'),
            'csses' => $this->css->getListCss('report'),
            'report_tab' => $this->syncReportTab(),
            'periode' => $this->getPeriodeReport(),
        ];


        return view('report.report', $data);
    }

    public function setTabActive($tab)
    {
        Session::put('report-tab-' . Auth::user()->id, $tab);
        return response()->json(Session::get('report-tab-' . Auth::user()->id));
    }

    function getPeriodeReport()
    {
        $periode = Session::get('periode_' . Auth::user()->id);
        if (!$periode) {
            $defaultStart = Carbon::now()->subMonth()->startOfMonth()->day(21)->toDateString();
            $defaultEnd = Carbon::now()->day(20)->toDateString();
            $name = $this->getPeriodeName($defaultStart, $defaultEnd);
            Session::put('periode_' . Auth::user()->id, ['start' => $defaultStart, 'end' => $defaultEnd, 'name' => $name]);
        }
        return (object)Session::get('periode_' . Auth::user()->id);
    }

    function setPeriodeReport(Request $request)
    {
        $periode  = $request->periode;
        Session::put('periode_' . Auth::user()->id, ['start' => $periode['start'], 'end' => $periode['end'], 'name' => $periode['name']]);
        return response()->json(Session::get('periode_' . Auth::user()->id));
    }

    private function getPeriodeName(string $start, string $end): string
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



    function datatableListUnit()
    {
        return DataTables()->of(
            Unit::query()
                ->with(['report_employees' => function ($query) {
                    $query->selectRaw('unit_id, COUNT(*) as jumlah_karyawan')
                        ->groupBy('unit_id');
                }])
                ->where('status', 1)
        )
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex justify-content-around">';
                $action .= '<div onclick="javascript:detailUnit(' . $row->id . ')" class="btn btn-sm btn-outline-success m-1"><i class="fas fa-eye"></i></div>';
                // $action .= '<div onclick="javascript:generateReportEmployee(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="fas fa-sync-alt"></i></div>';
                $action .= '</div>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    function syncReportTab()
    {
        $report_tab = Session::get('report-tab-' . Auth::user()->id);
        if (!$report_tab) {
            Session::put('report-tab-' . Auth::user()->id, 'unit');
            $report_tab = Session::get('report-tab-' . Auth::user()->id);
        }
        return $report_tab;
    }

    function datatableReportNew(Request $request)
    {

        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $listEmployee = Employee::where('is_deleted', 0)->get('pin')->pluck('pin')->toArray();
        return DataTables()->of(
            Report::query()
                ->whereIn('pin', $listEmployee)
                ->whereBetween('scan_date', [$start_date, $end_date])
        )
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex justify-content-around">';
                $action .= '<div onclick="javascript:editEmployeeFtm(' . $row->emp_id_auto . ')" class="btn btn-sm btn-outline-primary m-1"><i class="fas fa-user-edit"></i></div>';
                // $action .= '<div onclick="javascript:deleteEmployeeFtm(' . $row->emp_id_auto . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '</div>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    function datatableScanLog($start_date, $end_date)
    {

        // dd($start_date, $end_date);
        $listEmployee = Employee::where('is_deleted', 0)->get('pin')->pluck('pin')->toArray();
        return DataTables()->of(
            ScanLog::query()
                ->whereIn('pin', $listEmployee)
                ->whereBetween('scan_date', [$start_date, $end_date])
        )
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex justify-content-around">';
                $action .= '<div onclick="javascript:editEmployeeFtm(' . $row->id . ')" class="btn btn-sm btn-outline-primary m-1"><i class="fas fa-user-edit"></i></div>';
                // $action .= '<div onclick="javascript:deleteEmployeeFtm(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="fas fa-sync-alt"></i></div>';
                $action .= '</div>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    function generateReportEmployees()
    {
        $periode = $this->getPeriodeReport();
        $start_date = $periode->start;
        $end_date = $periode->end;

        // dd($periode);

        $list_employees = Employee::where('is_deleted', 0)->get('pin')->pluck('pin')->toArray();

        $employees = Employee::where('is_deleted', 0)->get(['nama', 'pin', 'id', 'pangkat_id']);

        $shifts = Schedule::where('is_default', 1)->first()->hasShifts($start_date, $end_date);

        $scan_logs = AttLog::whereBetween('scan_date', [$start_date, $end_date])->whereIn('pin', $list_employees)->orderBy('scan_date')->get()->map(function ($scan_log) {
            $scan_log->date = Carbon::parse($scan_log->scan_date)->format('Y-m-d');
            return $scan_log;
        });

        $data_report = [];

        foreach ($employees as $index_employee => $employee) {
            $data_report[$employee->nama] = [];
            foreach ($shifts as $index_shift => $shift) {
                $report = [
                    'nama_karyawan' => $employee->nama,
                    'employee_id' => $employee->id,
                    'pin' => $employee->pin,
                    'date' => $shift->date,
                    'day' => $shift->day,
                    'shift_id' => $shift->id,
                    'status' => 'Tidak Hadir',
                ];

                $scan_log = $scan_logs->where('pin', $employee->pin)->where('date', $shift->date);
                if ($scan_log->isNotEmpty()) {
                    $check = $this->getCheckInCheckOut($scan_log, $shift);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $report['scan_masuk_efektif'] = $shift->jam_masuk;
                    $report['scan_keluar_efektif'] = $shift->jam_keluar;
                    $jam_hilang_murni = 0;
                    $jam_hilang_efektif = 0;

                    $status = $this->getStatusCheckInCheckOut($check, $shift);

                    $report['status_masuk'] = $status->check_in;
                    $report['status_keluar'] = $status->check_out;

                    if ($status->check_in != 'Tepat Waktu') {
                        $terlambat = $this->getJamHilangTerlambat($check->in, $shift);
                        $jam_hilang_efektif += $terlambat->efektif;
                        $jam_hilang_murni += $terlambat->murni;
                        $report['scan_masuk_efektif'] = Carbon::parse($shift->jam_masuk)->addMinutes($terlambat->efektif)->format('Y-m-d H:i:s');
                    }
                    if ($status->check_out != 'Tepat Waktu') {
                        $pulang_cepat = $this->getJamHilangPulangCepat($check->out, $shift);
                        $jam_hilang_efektif += $pulang_cepat->efektif;
                        $jam_hilang_murni += $pulang_cepat->murni;
                        $report['scan_keluar_efektif'] = Carbon::parse($shift->jam_keluar)->subMinutes($pulang_cepat->efektif)->format('Y-m-d H:i:s');
                    }

                    if ($status->check_in == 'Belum Scan Masuk' || $status->check_out == 'Belum Scan Keluar') {
                        $jam_hilang_murni = $shift->total_menit_kerja;
                        $jam_hilang_efektif = $shift->total_menit_kerja;
                    }

                    $report['istirahat_murni'] = $shift->total_menit_istirahat;
                    $report['istirahat_efektif'] = $shift->total_menit_istirahat;

                    $report['jam_hilang_murni'] = $jam_hilang_murni;
                    $report['jam_hilang_efektif'] = $jam_hilang_efektif;

                    $report['jam_kerja_murni'] = $shift->total_menit_kerja - $jam_hilang_murni;
                    if ($check->in && $check->out) {
                        $report['jam_kerja_murni'] =
                            floor(Carbon::parse($check->in)->diffInMinutes(Carbon::parse($check->out))
                                - $shift->total_menit_istirahat);
                    }

                    $report['jam_kerja_efektif'] = $shift->total_menit_kerja - $jam_hilang_efektif;

                    if ($status->check_out == 'Tepat Waktu') {
                        $lembur = $this->getJamLemburTerusan($check->out, $shift);
                        $report['lembur_murni'] = $lembur->murni;
                        $report['lembur_efektif'] = $lembur->efektif;
                        $report['lembur_akumulasi'] = $this->getJamLemburAkumulasi($lembur->efektif, $shift, $employee->pangkat_id);
                    }

                    // Check jika karyawan berpangkat operator
                    if ($employee->pangkat_id == 1 && $report['jam_kerja_efektif'] <= 240) {
                        $report['uk'] = 0;
                        $report['um'] = 0;
                        $report['ut'] = 0;
                    }
                } else {
                    $report['uk'] = 0;
                    $report['um'] = 0;
                    $report['ut'] = 0;
                }

                $data_report[$employee->nama][$shift->date] = $report;
                Report::updateOrCreate(
                    [
                        'date' => $shift->date,
                        'pin' => $employee->pin,
                    ],
                    $report
                );
            }
            Progress::updateOrCreate(
                [
                    'name' => 'progress-generate-report',
                ],
                [
                    'total' => count($employees),
                    'steps' => $index_employee + 1,
                    'persentase' => ($index_employee + 1) / count($employees) * 100
                ]
            );
            info("Progress: " . ($index_employee + 1) . " / " . count($employees));
        }
        // return $data_report;
    }

    function generateReportEmployee($employee_id)
    {
        $periode = $this->getPeriodeReport();
        $start_date = $periode->start;
        $end_date = $periode->end;

        // dd($periode);

        $list_employees = Employee::where('id', $employee_id)->get('pin')->pluck('pin')->toArray();
        $employees = Employee::where('id', $employee_id)->get(['nama', 'pin', 'id', 'pangkat_id']);

        $shifts = Schedule::where('is_default', 1)->first()->hasShifts($start_date, $end_date);

        $scan_logs = AttLog::whereBetween('scan_date', [$start_date, $end_date])->whereIn('pin', $list_employees)->orderBy('scan_date')->get()->map(function ($scan_log) {
            $scan_log->date = Carbon::parse($scan_log->scan_date)->format('Y-m-d');
            return $scan_log;
        });
        // $scan_log = $scan_logs->where('pin', 1030197)->where('date', '2024-12-03');
        // dd($scan_logs);
        $data_report = [];

        foreach ($employees as $index_employee => $employee) {
            $data_report[$employee->nama] = [];
            foreach ($shifts as $index_shift => $shift) {
                $report = [
                    'nama_karyawan' => $employee->nama,
                    'employee_id' => $employee->id,
                    'pin' => $employee->pin,
                    'date' => $shift->date,
                    'day' => $shift->day,
                    'shift_id' => $shift->id,
                    'status' => 'Tidak Hadir',
                ];

                $scan_log = $scan_logs->where('pin', $employee->pin)->where('date', $shift->date);
                if ($scan_log->isNotEmpty()) {
                    $check = $this->getCheckInCheckOut($scan_log, $shift);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $report['scan_masuk_efektif'] = $shift->jam_masuk;
                    $report['scan_keluar_efektif'] = $shift->jam_keluar;
                    $jam_hilang_murni = 0;
                    $jam_hilang_efektif = 0;


                    $status = $this->getStatusCheckInCheckOut($check, $shift);

                    $report['status_masuk'] = $status->check_in;
                    $report['status_keluar'] = $status->check_out;

                    if ($status->check_in != 'Tepat Waktu') {
                        $terlambat = $this->getJamHilangTerlambat($check->in, $shift);
                        $jam_hilang_efektif += $terlambat->efektif;
                        $jam_hilang_murni += $terlambat->murni;
                        $report['scan_masuk_efektif'] = Carbon::parse($shift->jam_masuk)->addMinutes($terlambat->efektif)->format('Y-m-d H:i:s');
                    }
                    if ($status->check_out != 'Tepat Waktu') {
                        $pulang_cepat = $this->getJamHilangPulangCepat($check->out, $shift);
                        $jam_hilang_efektif += $pulang_cepat->efektif;
                        $jam_hilang_murni += $pulang_cepat->murni;
                        $report['scan_keluar_efektif'] = Carbon::parse($shift->jam_keluar)->subMinutes($pulang_cepat->efektif)->format('Y-m-d H:i:s');
                    }

                    if ($status->check_in == 'Belum Scan Masuk' || $status->check_out == 'Belum Scan Keluar') {
                        $jam_hilang_murni = $shift->total_menit_kerja;
                        $jam_hilang_efektif = $shift->total_menit_kerja;
                    }

                    $report['istirahat_murni'] = $shift->total_menit_istirahat;
                    $report['istirahat_efektif'] = $shift->total_menit_istirahat;

                    $report['jam_hilang_murni'] = $jam_hilang_murni;
                    $report['jam_hilang_efektif'] = $jam_hilang_efektif;

                    $report['jam_kerja_murni'] = $shift->total_menit_kerja - $jam_hilang_murni;
                    if ($check->in && $check->out) {
                        $report['jam_kerja_murni'] =
                            floor(Carbon::parse($check->in)->diffInMinutes(Carbon::parse($check->out))
                                - $shift->total_menit_istirahat);
                    }

                    $report['jam_kerja_efektif'] = $shift->total_menit_kerja - $jam_hilang_efektif;

                    if ($status->check_out == 'Tepat Waktu') {
                        $lembur = $this->getJamLemburTerusan($check->out, $shift);
                        $report['lembur_murni'] = $lembur->murni;
                        $report['lembur_efektif'] = $lembur->efektif;
                        $report['lembur_akumulasi'] = $this->getJamLemburAkumulasi($lembur->efektif, $shift, $employee->pangkat_id);
                    }

                    // Check jika karyawan berpangkat operator
                    if ($employee->pangkat_id == 1 && $report['jam_kerja_efektif'] <= 240) {
                        $report['uk'] = 0;
                        $report['um'] = 0;
                        $report['ut'] = 0;
                    }
                } else {
                    $report['uk'] = 0;
                    $report['um'] = 0;
                    $report['ut'] = 0;
                }

                $data_report[$employee->nama][$shift->date] = $report;
                Report::updateOrCreate(
                    [
                        'date' => $shift->date,
                        'pin' => $employee->pin,
                    ],
                    $report
                );
            }
            Progress::updateOrCreate(
                [
                    'name' => 'progress-generate-report',
                ],
                [
                    'total' => count($employees),
                    'steps' => $index_employee + 1,
                    'persentase' => ($index_employee + 1) / count($employees) * 100
                ]
            );
            info("Progress: " . ($index_employee + 1) . " / " . count($employees));
        }
        return $data_report;
    }

    function generateReport()
    {
        $this->clearProgressReport();
        AsyncHandler::dispatch($this->generateReportEmployees());
        return response()->json(['success' => true]);
    }

    function getProgressReport()
    {
        return Progress::where('name', 'progress-generate-report')->first() ?? (object) [
            'total' => 0,
            'steps' => 0,
            'persentase' => 0
        ];
    }

    function clearProgressReport()
    {
        Progress::where('name', 'progress-generate-report')->update(['total' => 0, 'steps' => 0, 'persentase' => 0]);
    }


    function getJamLemburAkumulasi($jam_lembur_efektif, $shift, $pangkat_id)
    {
        $jam_lembur =  floor($jam_lembur_efektif / 60); // Konversi menit ke jam penuh
        $sisa_menit =  $jam_lembur_efektif % 60; // Sisa menit
        $jam_lembur_akumulasi = 0;
        if ($pangkat_id == 1) {
            switch ($shift->day) {
                case 'Terusan':
                    $lembur_pertama = min(60, $jam_lembur_efektif);
                    $lembur_selanjutnya = max(0, $jam_lembur_efektif - 60);
                    $jam_lembur_akumulasi = ($lembur_pertama * 1.5) + ($lembur_selanjutnya * 2);
                    return $jam_lembur_akumulasi;

                case 'Lembur Libur':
                    if ($jam_lembur > 8) {
                        $jam_lembur_akumulasi += ($jam_lembur - 8) * 4 * 60; // 9 jam ke atas kali 4
                        $jam_lembur = 8; // Kurangi jam yang sudah dihitung
                    }
                    if ($jam_lembur == 8) {
                        $jam_lembur_akumulasi += 8 * 3 * 60; // 8 jam kali 3
                        $jam_lembur = 7; // Kurangi jam yang sudah dihitung
                    }
                    if ($jam_lembur <= 7) {
                        $jam_lembur_akumulasi += $jam_lembur * 2 * 60; // 1-7 jam kali 2
                    }

                    // Hitung menit yang tersisa (tarif sesuai jam terakhir)
                    if ($jam_lembur_efektif > 480) { // Di atas 8 jam
                        $jam_lembur_akumulasi += $sisa_menit * 4;
                    } elseif ($jam_lembur_efektif > 420) { // 8 jam
                        $jam_lembur_akumulasi += $sisa_menit * 3;
                    } else { // 1-7 jam
                        $jam_lembur_akumulasi += $sisa_menit * 2;
                    }
                    return $jam_lembur_akumulasi;

                default:
                    return $jam_lembur_akumulasi;
            }
        }
    }

    function getUangKerajinanOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }
    function getUangMakanOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }
    function getUangTransportOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }

    function getJamLemburTerusan($scan_out, $shift)
    {
        $murni = (int)Carbon::parse($shift->jam_keluar)->diffInMinutes($scan_out);
        $efektif = 0;
        if ($murni >= 75) {
            $efektif = $murni - ($murni % 30);
        }
        return (object) ['murni' => $murni, 'efektif' => $efektif];
    }


    function getJamHilangTerlambat($scan_in, $shift)
    {
        $murni = (int)Carbon::parse($shift->jam_masuk)->diffInMinutes($scan_in);
        $efektif = $murni - ($murni % 15) + (($murni % 15) > 0 ? 15 : 0);
        return (object) compact('murni', 'efektif');
    }

    function getJamHilangPulangCepat($scan_out, $shift)
    {
        $murni = (int)Carbon::parse($scan_out)->diffInMinutes($shift->jam_keluar);
        $efektif = $murni - ($murni % 15) + (($murni % 15) > 0 ? 15 : 0);
        return (object) ['murni' => $murni, 'efektif' => $efektif];
    }

    function getStatusCheckInCheckOut($check, $shift)
    {
        $check_in = $check && $check->in ? Carbon::parse($check->in)->lte($shift->jam_masuk) ? 'Tepat Waktu' : 'Terlambat' : 'Belum Scan Masuk';
        $check_out = $check && $check->out ? Carbon::parse($check->out)->gte($shift->jam_keluar) ? 'Tepat Waktu' : 'Pulang Cepat' : 'Belum Scan Keluar';

        return (object) compact('check_in', 'check_out');
    }

    function getCheckInCheckOut($scan_logs, $shift)
    {
        // in Minute

        $scan_in = Carbon::parse($shift->jam_masuk);
        $scan_out = Carbon::parse($shift->jam_keluar);
        $result = [];

        $result['in'] = null;
        $result['out'] = null;

        $closest_scan_in = $scan_logs->map(function ($attendance) use ($scan_in, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            $difference = abs($scan_time->diffInMinutes($scan_in, false));
            return $difference > $shift->total_menit_kerja ? null : [
                'scan_date' => $attendance->scan_date,
                'difference' => $difference
            ];
        })->filter()->sortBy('difference')->first();

        $closest_scan_out_before = $scan_logs->filter(function ($attendance) use ($scan_out, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return $scan_time->lt($scan_out);
        })->map(function ($attendance) use ($scan_out, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            $difference = $scan_time->diffInMinutes($scan_out, true);
            return $difference > $shift->total_menit_kerja ? null : [
                'scan_date' => $attendance->scan_date,
                'difference' => $difference
            ];
        })->filter()->sortBy('difference')->first();

        $farthest_scan_out_after = $scan_logs->filter(function ($attendance) use ($scan_out) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return $scan_time->gt($scan_out);
        })->map(function ($attendance) use ($scan_out) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return [
                'scan_date' => $attendance->scan_date,
                'difference' => $scan_out->diffInMinutes($scan_time, true)
            ];
        })->sortByDesc('difference')->first();


        if ($closest_scan_in) {
            $result['in'] = $closest_scan_in['scan_date'];
        }

        if ($closest_scan_out_before) {
            $result['out'] = $closest_scan_out_before['scan_date'];
        }
        if ($farthest_scan_out_after) {
            $result['out'] = $farthest_scan_out_after['scan_date'];
        }

        return (object)$result;
    }

    function getEffectiveScanOutTime($scan_out, $check_out)
    {
        $total_minutes = Carbon::parse($scan_out)->diffInMinutes($check_out);
        $adjusted_minutes = intdiv($total_minutes, 60) * 60;
        $remaining_minutes = $total_minutes % 60;

        if ($remaining_minutes > 0 && $remaining_minutes < 15) {
            $remaining_minutes = 15;
        } elseif ($remaining_minutes >= 55) {
            $remaining_minutes = 60;
        } else {
            $remaining_minutes = ceil($remaining_minutes / 15) * 15;
        }

        $total_minutes = $adjusted_minutes + $remaining_minutes;
        if ($remaining_minutes < 0) {
            $total_minutes = $adjusted_minutes - $remaining_minutes;
        }

        // dd($scan_out, $check_out, $total_minutes, $adjusted_minutes, $remaining_minutes);

        return Carbon::parse($scan_out)->subMinutes($total_minutes)->subMinute()->format('Y-m-d H:i:s');
    }

    function getEffectiveScanInTime($scan_in, $check_in)
    {
        $total_minutes = Carbon::parse($scan_in)->diffInMinutes($check_in);
        $adjusted_minutes = intdiv($total_minutes, 60) * 60;
        $remaining_minutes = $total_minutes % 60;

        if ($remaining_minutes > 0 && $remaining_minutes < 15) {
            $remaining_minutes = 15;
        } elseif ($remaining_minutes >= 55) {
            $remaining_minutes = 60;
        } else {
            $remaining_minutes = floor($remaining_minutes / 15) * 15;
        }

        $total_minutes = $adjusted_minutes + $remaining_minutes;

        return Carbon::parse($scan_in)->addMinutes($total_minutes)->format('Y-m-d H:i:s');
    }


    function generateScanLog()
    {
        $listEmployee = Employee::where('is_deleted', 0)->get('pin')->pluck('pin')->toArray();

        $hrdEmployees = Employee::query()
            ->select(
                'employees.id',
                'employees.nip',
                'employees.nama',
                'employees.pin'
            )
            ->where('employees.is_deleted', 0)
            ->with('divisi', 'unit')
            ->get();


        $attLogs = AttLog::query()
            ->select(
                'att_log.pin',
                'att_log.scan_date',
                'att_log.sn'
            )
            ->whereIn('pin', $listEmployee)
            ->whereYear('scan_date', '>=', 2024)
            ->whereMonth('scan_date', '=', 10)
            ->with('device')
            // ->limit(10000)
            ->count();

        $totalRecords = $attLogs;

        // dd($totalRecords);
        // 260.000
        $batchSize = 10000;
        $processedRecords = 0;

        $timestamp = Carbon::now();
        while ($processedRecords < $totalRecords) {
            $attLogsBatch = AttLog::query()
                ->select(
                    'att_log.pin',
                    'att_log.scan_date',
                    'att_log.sn'
                )
                ->whereIn('pin', $listEmployee)
                ->whereYear('scan_date', '>=', 2024)
                ->whereMonth('scan_date', '=', 10)
                ->with('device')
                ->skip($processedRecords)
                ->take($batchSize)
                ->get();

            $combinedData = $attLogsBatch->map(function ($attLog) use ($hrdEmployees, $timestamp) {
                $hrdEmployee = $hrdEmployees->where('pin', $attLog->pin ?? null)->first();
                return [
                    'scan_date' => $attLog->scan_date,
                    'date' => date('Y-m-d', strtotime($attLog->scan_date)),
                    'time' => date('H:i', strtotime($attLog->scan_date)),
                    'pin' => $attLog->pin,
                    'nik' => $hrdEmployee->nip ?? null,
                    'nama_karyawan' => $hrdEmployee->nama ?? null,
                    'divisi_id' => $hrdEmployee->divisi->id ?? null,
                    'divisi' => $hrdEmployee->divisi->divisi ?? null,
                    'unit_id' => $hrdEmployee->unit->id ?? null,
                    'unit' => $hrdEmployee->unit->unit ?? null,
                    'sn' => $attLog->sn,
                    'device_name' => $attLog->device->device_name ?? null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            })->toArray();


            // Use chunk insert to handle large datasets efficiently
            foreach (array_chunk($combinedData, 1000) as $chunk) {
                DB::table('scan_logs')->insert($chunk);
            }

            // Process the batch here
            // For example, you can add the processing logic here or call a separate method

            $processedRecords += $batchSize;

            // Optional: Add a small delay to prevent overwhelming the database
            usleep(1000000); // 1 second delay
        }

        return response()->json(['total_processed' => $totalRecords]);
    }


    function datatableListEmployee()
    {
        return DataTables()->of(
            Employee::query()
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
    }

    function reportEmployee(Employee $employee)
    {

        $data = [
            'title' => 'Report Employee',
            'slug' => 'report',
            'scripts' => $this->script->getListScript('report-employee'),
            'csses' => $this->css->getListCss('report-employee'),
            'report_tab' => $this->syncReportTab(),
            'periode' => $this->getPeriodeReport(),
            'employee' => $employee
        ];


        return view('report.report-employee', $data);
    }

    function datatableReportEmployee($employee_id)
    {

        $periode = $this->getPeriodeReport();
        $reports = Report::whereBetween('date', [$periode->start, $periode->end])
            ->where('employee_id', $employee_id)
            ->with('shift')
            ->get()
            ->keyBy('date');



        $dataReport = [];
        foreach (CarbonPeriod::create($periode->start, $periode->end) as $date) {
            $date = $date->format('Y-m-d');
            $day = Carbon::parse($date)->locale('id_ID')->isoFormat('dddd');

            $dataReport[$date] = [
                'tanggal' => $day . ', ' . $date,
                'shift' => null,
                'jam_masuk' => null,
                'jam_keluar' => null,
                'scan_masuk' => null,
                'scan_keluar' => null,
                'durasi_murni' => null,
                'durasi_efektif' => null,
                'jam_hilang_murni' => null,
                'jam_hilang_efektif' => null,
                'lembur_murni' => null,
                'lembur_efektif' => null,
                'status' => null,
                'keterangan' => null,
                'verifikasi' => '<button type="button" class="btn btn-sm btn-outline-info m-1">Verifikasi</button>',
                'perizinan' => '<button type="button" class="btn btn-sm btn-outline-warning m-1">Perizinan</button>',

            ];
            if (isset($reports[$date])) {
                $dataReport[$date]['shift'] = $reports[$date]['shift']['name'];
                $dataReport[$date]['jam_masuk'] = $reports[$date]['shift']['jam_masuk'];
                $dataReport[$date]['jam_keluar'] = $reports[$date]['shift']['jam_keluar'];
                $dataReport[$date]['scan_masuk'] = $reports[$date]->scanMasuk();
                $dataReport[$date]['scan_keluar'] = $reports[$date]->scanKeluar();
                $dataReport[$date]['durasi_murni'] = $reports[$date]->durasiMurni();
                $dataReport[$date]['durasi_efektif'] = $reports[$date]->durasiEfektif();
                $dataReport[$date]['jam_hilang_murni'] = $reports[$date]->jamHilangMurni();
                $dataReport[$date]['jam_hilang_efektif'] = $reports[$date]->jamHilangEfektif();
                $dataReport[$date]['lembur_murni'] = $reports[$date]->lemburMurni();
                $dataReport[$date]['lembur_efektif'] = $reports[$date]->lemburEfektif();
                $dataReport[$date]['status'] = $reports[$date]['status'];
                $dataReport[$date]['keterangan'] = $reports[$date]['keterangan'];


                // dd($reports[$date]);
            }
        }
        // dd($dates);
        return DataTables()->of(
            $dataReport
        )
            ->addIndexColumn()
            // ->addColumn('action', function ($row) {
            //     $action = '<div class="d-flex justify-content-center">';
            //     $action .= '<div onclick="javascript:generateReportEmployee(' . $row->id . ')" class="btn btn-sm btn-outline-warning m-1"><i class="fas fa-sync-alt"></i></div>';
            //     $action .= '<a href="' . url('/report/employee/' . $row->id) . '"  class="btn btn-sm btn-outline-success m-1"><i class="fas fa-eye"></i></a>';
            //     $action .= '</div>';
            //     return $action;
            // })
            ->rawColumns(['verifikasi', 'perizinan'])
            ->make(true);
    }


    function shareUndangan()
    {
        $message = "Yth,\n*Abima Nugraha*\n\nبِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ\nٱلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ ٱللَّٰهِ وَبَرَكَاتُهُ\n\nDengan memohon rahmat dan ridha Allah SWT serta tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i, teman sekaligus sahabat, untuk berkenan hadir di acara pernikahan kami\n\n*Klik link berikut untuk melihat Undangan pernikahan kami :* https://Emooteinvi.com/deca-bima2?to=Abima%20Nugraha\n\nMerupakan suatu kehormatan dan kebahagiaan apabila Bapak/Ibu/Saudara/i berkenan untuk hadir dan memberikan doa restu di hari bahagia kami.\n\nMohon kesediaannya untuk mengirimkan konfirmasi kehadiran melalui form RSVP yang tersedia.\n\nAtas kehadiran dan do'a restunya kami ucapkan terima kasih.\n\n*Deca & Bima*";

        $file = public_path('xls/undangan-rabi.xlsx');
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // dd($highestRow);
        for ($row = 2; $row <= $highestRow; $row++) {
            $nomor_wa = $sheet->getCell("B" . $row)->getValue();
            dump($nomor_wa);
            // $this->rabicanSendMessage($nomor_wa, s$message);
        }

        // return $this->rabicanSendMessage(628989227992, $message);
    }

    function rabicanSendMessage($nomor_wa, $message)
    {
        // Get the Megacan API URL from the environment variable
        $url_megacan = config('services.megacan.url');

        // Prepare the data payload for the API request
        $data = [
            'chatId' => $nomor_wa . '@c.us',
            "contentType" => "string",
            "content" => $message
        ];

        // Send the API request using the HTTP POST method
        $response = Http::withBody(json_encode($data), 'application/json')
            ->post($url_megacan . '/client/sendMessage/rabi');

        // Return the response object from the Megacan API
        return $response->object();
    }
}
