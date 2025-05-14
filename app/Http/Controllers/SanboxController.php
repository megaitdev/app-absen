<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Report\ApiReportController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\DasarJadwal;
use App\Models\Izin;
use App\Models\JenisCuti;
use App\Models\JenisIzin;
use App\Models\mak_hrd\Employee;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SanboxController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    function sandbox()
    {
        $query = Employee::on('mysql_hrd') // Ensure this uses the mysql_hrd connection
            ->where('is_deleted', 0)
            ->whereIn('id', [513, 514])
            ->with(['unit', 'divisi', 'dasarJadwal']); // Ensure relationships are set up



        // Apply sorting conditionally
        $query = $query->get();
        $query = $query->sortByDesc(function ($item) {
            return count($item->dasarJadwal);
        });
        dd($query);
        $employees = Employee::query()
            ->where('is_deleted', 0)
            ->with('unit', 'divisi', 'dasarJadwal')->get();
        dd($employees[0]);
    }

    function sandboxPrintReportPIC()
    {
        $picID = Auth::user()->id;
        $arc = new ApiReportController();


        $pic = User::find($picID);
        $periode = $arc->getPeriodeReport($picID);

        $arc->printPICReport($picID);


        $listEmployeeIDs = json_decode($pic->employees, true);
        $indexEmployee = 0;
        foreach ($listEmployeeIDs as $employee_id) {
            $reportEmployee = $arc->getAttendanceStats($employee_id)->getData()->data;
            $indexEmployee++;
        }

        $tes = $arc->getAttendanceStats(513)->getData()->data;
        dd($tes);
    }
    function sandboxWhereHas()
    {
        $search = 'HRD';
        $query = Employee::where('is_deleted', 0)
            ->orderBy('nama', 'asc')
            ->with('unit')
            ->whereHas('unit', function ($q) use ($search) {
                $q->where('unit', 'like', "%{$search}%");
            });
        // $units = ['6'];
        // $query = Employee::where('is_deleted', 0)
        //     ->orderBy('nama', 'asc')
        //     ->with('unit')
        //     ->whereHas('unit', function ($q) use ($units) {
        //         $q->wherein('unit_id', $units);
        //     });
        dd($query->get());
    }
    function sandboxDate()
    {
        $reportController = new ReportController(new ScriptController, new CssController);
        $result = $reportController->hitungJamKerjaMurni("08:00", "14:45");
        dd($result);
    }
    function sandboxTestCountingReport()
    {

        $report = Report::find(22);
        $apiReportController = new ApiReportController();
        $apiReportController->countingReport($report);
    }
    function sandboxHttpRequest()
    {

        $reportController = new ReportController(new ScriptController, new CssController);
        $result = $reportController->generateReportEmployee(404);

        dd($result);

        $res = Http::get(url('report/generate/single-employee/404'));
        // $res = Http::get('https://api.sampleapis.com/baseball/hitsSingleSeason');
        dump(url('report/generate/single-employee/404'));
        dump($res->status());
        dump($res->json());
        dump($res->getBody());
        dump($res->getHeaders());
        dd($res);

        // $response = Http::get(url('/report/generate/single-employee/404'));
        $response = Http::withBody(json_encode(['tes']), 'application/json')->get(url('/report/generate/single-employee/404'));

        return $response->object();
        // Menggunakan metode getContents()
        // $data = $body->getContents();
        // dd($data);
    }
    function sandboxBrowserShot()
    {
        $employee = Employee::find(403);
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
        $template = view('report.report-employee', $data)->render();

        // Browsershot::html($template)
        //     // ->showBackground()
        //     ->setIncludePath('$PATH:/home/uit/.nvm/versions/node/v22.14.0/bin')
        //     ->setChromePath('/home/uit/.cache/puppeteer/chrome/linux-134.0.6998.35/chrome-linux64')
        //     // ->setNodeBinary('/home/uit/.nvm/versions/node/v22.14.0/bin/node')
        //     // ->setNpmBinary('/home/uit/.nvm/versions/node/v22.14.0/bin/npm')
        //     // ->setIncludePath('/usr/bin')
        //     ->save('template.pdf');
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
    function syncReportTab()
    {
        $report_tab = Session::get('report-tab-' . Auth::user()->id);
        if (!$report_tab) {
            Session::put('report-tab-' . Auth::user()->id, 'unit');
            $report_tab = Session::get('report-tab-' . Auth::user()->id);
        }
        return $report_tab;
    }
    public function sandboxCountingReport()
    {
        $date = '2025-02-07';
        $employee_id = 403;
        $report = Report::where('employee_id', $employee_id)
            ->where('date', $date)->first()->load('shift');
        $shift = $report->shiftDate();
        $check = (object)[
            'in' => Carbon::parse($report->scan_masuk_murni),
            'out' => Carbon::parse($report->scan_keluar_murni),
        ];

        $employee = Employee::findOrFail(403);
        $pangkat_id = $employee->pangkat_id;

        $dataUpdate = [];
        $dataUpdate['istirahat_murni'] = $shift->total_menit_istirahat;
        $dataUpdate['istirahat_efektif'] = $shift->total_menit_istirahat;
        dump($check->in->format('Y-m-d H:i:s'));
        dump($check->out->format('Y-m-d H:i:s'));

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
        if ($check->out->lt($shift->jam_mulai_istirahat)) {
            $dataUpdate['jam_kerja_murni'] = floor($check->in->diffInMinutes($check->out));
        }

        $dataUpdate['jam_hilang_murni'] = $jamHilangMurni;
        $dataUpdate['jam_hilang_efektif'] = $jamHilangEfektif;

        // dump($check, $shift->jam_keluar);
        if ($status->check_out == 'Tepat Waktu') {
            $lemburMurni = $this->getSelisihWaktu($check->out, $shift->jam_keluar)->murni - 15;

            $istirahat = $this->getJamIstirahat($date, 'sore');
            if ($check->out->gt($istirahat->mulai) && $check->out->lt($istirahat->selesai)) {
                $lemburMurni -= abs($check->out->diffInMinutes($istirahat->mulai));
            }
            if ($check->out->gt($istirahat->selesai)) {
                $lemburMurni -= $istirahat->durasi;
            }
            $lemburEfektif = $this->getEfektifJamLembur($lemburMurni);
            $jenisLembur = $report->shift_id > 0 ? 'Terusan' : 'Lembur Libur';
            $lemburAkumulasi = $this->getAkumulasiJamLembur($lemburEfektif, $jenisLembur, $pangkat_id);
            $dataUpdate['lembur_murni'] = $lemburMurni;
            $dataUpdate['lembur_efektif'] = $lemburEfektif;
            $dataUpdate['lembur_akumulasi'] = $lemburAkumulasi;
        }


        // dump($murni);
        // dump($efektif);



        // $report->update($dataUpdate);
        dd($dataUpdate);
    }


    public function sandboxGetShiftByDate()
    {
        $employee = Employee::findOrFail(403);

        return $employee->shiftByDate('2025-01-03');
    }

    public function getShiftByDate(int $employee_id, string $date)
    {
        $employee = Employee::findOrFail($employee_id);

        $dasarJadwal = $employee->dasarJadwal;

        $dasarJadwal = $employee->dasarJadwal()
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            })
            ->first();
        $schedule = $dasarJadwal->schedule;
        // dd($schedule->shift($date));
        $shift = $schedule->shift($date);
        return $shift;
    }


    public function sandboxSeedingDasarJadwal()
    {
        $employees = Employee::where('is_deleted', 0)->get();
        $defaultSchedule = Schedule::where('is_default', 1)->first();
        foreach ($employees as $employee) {
            DasarJadwal::updateOrCreate([
                'employee_id' => $employee->id,
            ], [
                'schedule_id' => $defaultSchedule->id,
                'start_date' => '2000-01-01',
                'end_date' => null,
                'is_active' => 1,
            ]);
        }
        dd($employees);
    }
    public function sandboxArray()
    {
        $arrayKosong = [
            'nama' => '',
            'alamat' => '',
        ];
        $arrayKosong = array_merge($arrayKosong, [
            'scan_masuk_murni' => 'test',
            'scan_keluar_murni' => 'test',
        ]);
        array_push($arrayKosong, "Daffa", "Aldiansyah", "Kurniawan");
        dd($arrayKosong);
    }
}
