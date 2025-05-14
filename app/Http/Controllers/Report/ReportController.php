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
use App\Models\Holiday;
use App\Models\Izin;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\Lembur;
use App\Models\Progress;
use App\Models\Schedule;
use App\Models\VerifikasiAbsen;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
            'periode' => $this->getPeriodeReport(),
        ];

        if (Auth::user()->role === 'admin') {
            $data['report_tab'] = $this->syncReportTab();
            return view('report.report', $data);
        } else if (Auth::user()->role === 'pic') {
            $data['scripts'] = $this->script->getListScript('report-pic');
            $data['csses'] = $this->css->getListCss('report-pic');
            return view('report.pic.report', $data);
        }
    }

    public function setTabActive($tab)
    {
        Session::put('report-tab-' . Auth::user()->id, $tab);
        return response()->json(Session::get('report-tab-' . Auth::user()->id));
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


    function hitungJamKerjaMurni($scan_masuk, $scan_keluar)
    {
        $scan_masuk = strlen($scan_masuk) == 19 ? substr($scan_masuk, -8) : substr($scan_masuk, -5);
        $scan_keluar = strlen($scan_keluar) == 19 ? substr($scan_keluar, -8) : substr($scan_keluar, -5);

        $scan_masuk_time = Carbon::parse($scan_masuk);
        $scan_keluar_time = Carbon::parse($scan_keluar);

        $total_menit_kerja = $scan_masuk_time->diffInMinutes($scan_keluar_time);
        $menit_istirahat = 0;

        $istirahat_times = [
            ['start' => '12:00', 'end' => '12:45'],
            ['start' => '18:00', 'end' => '18:45']
        ];

        foreach ($istirahat_times as $istirahat) {
            $istirahat_start = Carbon::parse($istirahat['start']);
            $istirahat_end = Carbon::parse($istirahat['end']);
            if ($scan_masuk_time < $istirahat_start && $scan_keluar_time > $istirahat_end) {
                $menit_istirahat += $istirahat_start->diffInMinutes($istirahat_end);
            }
        }
        $jam_kerja_murni = $total_menit_kerja - $menit_istirahat;
        return $jam_kerja_murni;
    }

    function hitungJamKerjaEfektif($jam_kerja_murni)
    {
        // Hitung jumlah periode 30 menit
        $periode_30_menit = floor($jam_kerja_murni / 30);

        // Hitung sisa menit
        $sisa_menit = $jam_kerja_murni % 30;

        // Jika sisa menit lebih dari 0, tambahkan satu periode
        if ($sisa_menit > 0) {
            $periode_30_menit++;
        }
    }

    function getWeekendDates($startDate, $endDate)
    {
        $weekendDates = [];
        $datePeriod = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            new DateTime($endDate)
        );

        foreach ($datePeriod as $date) {
            if ($date->format('N') >= 6) {
                $weekendDates[] = $date->format('Y-m-d');
            }
        }
        return $weekendDates;
    }

    function getHolidaysInPeriod($startDate, $endDate)
    {
        return response()->json(Holiday::whereBetween('date', [$startDate, $endDate])->get(['date', 'note'])->keyBy('date'));
    }



    function generateReportEmployee($employee_id, $periode = null)
    {
        if (!$periode) {
            $periode = $this->getPeriodeReport();
        }
        $startDate = $periode->start;
        $endDate = $periode->end;
        // $startDate = '2025-01-24';
        // $endDate = '2025-01-24';

        $employee = Employee::where('id', $employee_id)->first(['nama', 'pin', 'id', 'pangkat_id']);
        if (!$employee) {
            throw new Exception('Employee not found');
        }

        $employeePin = $employee->pin;

        // $shifts = Schedule::where('is_default', 1)->first()->hasShifts($startDate, $endDate);


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



        // Filter out holiday shifts
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

            // Verifikasi
            $verifikasiForDate = array_values(array_filter($tempVerifikasi, function ($item) use ($date) {
                return $item->date == $date;
            }));

            if ($verifikasiForDate) {
                $report['is_verifikasi'] = 1;
            }

            $scanLogForDate = $scanLogForDate->concat($verifikasiForDate);

            // Lembur
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

            // Izin
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
                // Tanpa Shift
                $report['shift_id'] = null;
                if ($scanLogForDate->isNotEmpty()) {
                    $check = $this->getCheckInCheckOutWithoutShift($scanLogForDate);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $durasiLembur = $this->hitungJamKerjaMurni($check->in, $check->out);
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
                    $check = $this->getCheckInCheckOut($scanLogForDate, $shift);
                    $report['status'] = 'Hadir';
                    $report['scan_masuk_murni'] = $check->in;
                    $report['scan_keluar_murni'] = $check->out;
                    $report['scan_masuk_efektif'] = $shift->jam_masuk;
                    $report['scan_keluar_efektif'] = $shift->jam_keluar;
                    $jamHilangMurni = 0;
                    $jamHilangEfektif = 0;

                    $status = $this->getStatusCheckInCheckOut($check, $shift);
                    $jam_selesai_istirahat = Carbon::parse($shift->jam_selesai_istirahat);

                    // dd($jam_selesai_istirahat->lt($check->out));
                    $report['status_masuk'] = $status->check_in;
                    $report['status_keluar'] = $status->check_out;

                    if ($status->check_in != 'Tepat Waktu') {
                        $terlambat = $this->getJamHilangTerlambat($check->in, $shift);
                        $jamHilangEfektif += $terlambat->efektif;
                        $jamHilangMurni += $terlambat->murni;
                        $report['scan_masuk_efektif'] = Carbon::parse($shift->jam_masuk)
                            ->addMinutes($terlambat->efektif)
                            ->format('Y-m-d H:i:s');
                    }

                    if ($status->check_out != 'Tepat Waktu') {
                        $pulangCepat = $this->getJamHilangPulangCepat($check->out, $shift);
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
                        $lembur = $this->getJamLemburTerusan($check->out, $shift);
                        $report['lembur_murni'] = $lembur->murni;
                        if (Carbon::parse($check->out)->gt(Carbon::parse($date . ' 18:45'))) {
                            $lembur->efektif -= 60;
                        }
                        $report['lembur_efektif'] = $lembur->efektif;
                        $report['lembur_akumulasi'] = $this->getJamLemburAkumulasi($lembur->efektif, 'Terusan', $employee->pangkat_id);
                    }

                    // Check if employee is an operator
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

                    // End of IF $scanLogForDate->isNotEmpty()
                }
            }
            Report::updateOrCreate(
                [
                    'date' => $date,
                    'pin' => $employee->pin,
                ],
                $report
            );
            // dump($report);
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


    function getJamLemburAkumulasi($jam_lembur_efektif, $lembur, $pangkat_id)
    {
        $jam_lembur_efektif = round($jam_lembur_efektif / 30) * 30;
        $jam_lembur =  floor($jam_lembur_efektif / 60); // Konversi menit ke jam penuh
        $sisa_menit =  $jam_lembur_efektif % 60; // Sisa menit
        $jam_lembur_akumulasi = 0;
        if ($pangkat_id == 1) {
            switch ($lembur) {
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
        } else {
            return $jam_lembur_efektif;
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

        $murni = (int)Carbon::parse($shift->jam_keluar)->diffInMinutes($scan_out) - 15;
        $efektif = 0;
        if ($murni >= 60) {
            $efektif = $murni - ($murni % 30);
        } else {
            $murni = 0;
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
        $check_in = $check && $check->in ? (Carbon::parse($check->in)->format('H:i') <= Carbon::parse($shift->jam_masuk)->format('H:i') ? 'Tepat Waktu' : 'Terlambat') : 'Belum Scan Masuk';
        $check_out = $check && $check->out ? (Carbon::parse($check->out)->format('H:i') >= Carbon::parse($shift->jam_keluar)->format('H:i') ? 'Tepat Waktu' : 'Pulang Cepat') : 'Belum Scan Keluar';

        return (object) compact('check_in', 'check_out');
    }

    function getCheckInCheckOutWithoutShift($scan_logs)
    {
        $firstLog = $scan_logs->first();
        $lastLog = $scan_logs->last();

        if ($firstLog && $lastLog) {
            $jamFirst = Carbon::parse($firstLog->scan_date)->format('H');
            $jamLast = Carbon::parse($lastLog->scan_date)->format('H');
            $jarakWaktu = abs(Carbon::parse($lastLog->scan_date)->diffInHours(Carbon::parse($firstLog->scan_date)));

            if ($jarakWaktu <= 1) {
                if ($jamFirst < 12) {
                    $check_in = $firstLog->scan_date;
                    $check_out = null;
                } elseif ($jamLast < 12) {
                    $check_in = null;
                    $check_out = $lastLog->scan_date;
                } else {
                    $check_in = $firstLog->scan_date;
                    $check_out = $lastLog->scan_date;
                }
            } else {
                $check_in = $firstLog->scan_date;
                $check_out = $lastLog->scan_date;
            }
        } else {
            $check_in = null;
            $check_out = null;
        }

        return (object) [
            'in' => $check_in,
            'out' => $check_out
        ];
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
        if ($scan_logs->where('jenis', 'scan_masuk')->first()) {
            $result['in'] = $scan_logs->where('jenis', 'scan_masuk')->first()->scan_date;
        }
        if ($scan_logs->where('jenis', 'scan_keluar')->first()) {
            $result['out'] = $scan_logs->where('jenis', 'scan_keluar')->first()->scan_date;
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
            'employee' => $employee,
            'jenis_cuti' => JenisCuti::all(),
            'jenis_izin' => JenisIzin::all(),
        ];
        return view('report.report-employee', $data);
    }

    function datatableReportEmployee($employee_id)
    {
        $periode = $this->getPeriodeReport();
        $reports = Report::whereBetween('date', [$periode->start, $periode->end])
            ->where('employee_id', $employee_id)
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
            ->get()
            ->keyBy('date');


        $dataReport = [];
        foreach (CarbonPeriod::create($periode->start, $periode->end) as $date) {
            $date = $date->format('Y-m-d');
            $day = Carbon::parse($date)->locale('id_ID')->isoFormat('dddd');
            $dataReport[$date] = [
                'tanggal' => $day . ', ' . $date,
                'date' => $date,
                'shift' => null,
                'verifikasi_id' => null,
                'verifikasi' => null,
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
                'durasi_lembur' => null,
                'status' => null,
                'keterangan' => null,
                'tunjangan' => null,
                'potongan' => null,
                'is_cuti' => null,
                'is_lembur_libur' => null,
                'is_izin' => null,
                'izin_id' => null,
                'is_full_day' => null,
                'report_id' => null,
                'perizinan' => '<div class="btn btn-sm btn-warning m-1" onclick="javascript:generatePerizinan(`' . $date . '`,0)">Perizinan</div>',
            ];

            if (isset($reports[$date])) {
                $dataReport[$date]['shift']                 = $reports[$date]['shift']['name'] ?? null;
                $dataReport[$date]['jam_masuk']             = $reports[$date]['shift']['jam_masuk'] ?? null;
                $dataReport[$date]['jam_keluar']            = $reports[$date]['shift']['jam_keluar'] ?? null;
                $dataReport[$date]['scan_masuk']            = $reports[$date]->scanMasuk();
                $dataReport[$date]['scan_keluar']           = $reports[$date]->scanKeluar();
                $dataReport[$date]['durasi_murni']          = $reports[$date]->durasiMurni();
                $dataReport[$date]['durasi_efektif']        = $reports[$date]->durasiEfektif();
                $dataReport[$date]['jam_hilang_murni']      = $reports[$date]->jamHilangMurni();
                $dataReport[$date]['jam_hilang_efektif']    = $reports[$date]->jamHilangEfektif();
                $dataReport[$date]['lembur_murni']          = $reports[$date]->lemburMurni();
                $dataReport[$date]['lembur_efektif']        = $reports[$date]->lemburEfektif();
                $dataReport[$date]['potongan']              = $reports[$date]->potongan();
                $dataReport[$date]['durasi_lembur']         = $reports[$date]['lembur_efektif'];
                $dataReport[$date]['status']                = $reports[$date]['status'];
                $dataReport[$date]['keterangan']            = $reports[$date]['keterangan'];
                $dataReport[$date]['is_cuti']               = $reports[$date]['is_cuti'];
                $dataReport[$date]['is_izin']               = $reports[$date]['is_izin'];
                $dataReport[$date]['cuti_id']               = $reports[$date]['cuti']['id'] ?? null;
                $dataReport[$date]['is_lembur']             = $reports[$date]['is_lembur'];
                $dataReport[$date]['is_lembur_libur']       = $reports[$date]['is_lembur_libur'];
                $dataReport[$date]['lembur_id']             = $reports[$date]['lembur']['id'] ?? null;
                $dataReport[$date]['verifikasi_id']         = $reports[$date]['verifikasi']['id'] ?? null;
                $dataReport[$date]['izin_id']               = $reports[$date]['izin']['id'] ?? null;
                $dataReport[$date]['is_full_day']           = $reports[$date]['izin']['is_full_day'] ?? null;
                $dataReport[$date]['verifikasi']            = $reports[$date]['verifikasi']['jenis'] ?? null;
                $dataReport[$date]['report_id']             = $reports[$date]['id'];
                $dataReport[$date]['ut']                    = $reports[$date]['ut'];
                $dataReport[$date]['um']                    = $reports[$date]['um'];
                $dataReport[$date]['uk']                    = $reports[$date]['uk'];
                $dataReport[$date]['utl']                   = $reports[$date]['utl'];
                $dataReport[$date]['uml']                   = $reports[$date]['uml'];
                $dataReport[$date]['umll']                  = $reports[$date]['umll'];
                $dataReport[$date]['perizinan']             = '<div class="btn btn-sm btn-warning m-1" onclick="javascript:generatePerizinan(`' . $date . '`,' . $reports[$date]['id'] . ')">Perizinan</div>';
            }
        }

        return DataTables()->of(
            $dataReport
        )
            ->addIndexColumn()
            ->rawColumns(['perizinan'])
            ->make(true);
    }
}
