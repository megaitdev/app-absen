<?php

namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Lembur;
use App\Models\Report;
use App\Models\VerifikasiAbsen;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PicDashboardController extends Controller
{
    public function getDashboardStats()
    {
        try {
            // 1. Get PIC's managed employees
            $picEmployees = $this->getPicEmployees();

            // 2. Get periode from session (30 days)
            $periode = $this->getPeriodeDashboard();

            if ($picEmployees->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'totalKaryawan' => 0,
                        'persentaseKehadiran' => '0%',
                        'persentaseLembur' => '0%',
                        'persentaseKetidakhadiran' => '0%',
                        'persentaseCuti' => '0%',
                        'persentaseIzin' => '0%',
                        'persentaseSakit' => '0%',
                        'persentaseVerifikasi' => '0%'
                    ]
                ]);
            }

            // 3. Calculate statistics
            $totalKaryawan = $picEmployees->count();

            // Get reports for the period
            $reports = Report::whereIn('employee_id', $picEmployees)
                ->whereBetween('date', [$periode->start, $periode->end])
                ->get();

            $totalHariKerja = $reports->count();

            if ($totalHariKerja > 0) {
                $kehadiran = $reports->where('is_hadir', true)->count();
                $cuti = $reports->where('is_cuti', true)->count();
                $izin = $reports->where('is_izin', true)->count();
                $sakit = $reports->where('is_sakit', true)->count();
                $lembur = $reports->where('is_lembur', true)->count();
                $verifikasi = $reports->where('is_verifikasi', true)->count();

                $persentaseKehadiran = round(($kehadiran / $totalHariKerja) * 100);
                $persentaseLembur = round(($lembur / $totalHariKerja) * 100);
                $persentaseCuti = round(($cuti / $totalHariKerja) * 100);
                $persentaseIzin = round(($izin / $totalHariKerja) * 100);
                $persentaseSakit = round(($sakit / $totalHariKerja) * 100);
                $persentaseVerifikasi = round(($verifikasi / $totalHariKerja) * 100);
                $persentaseKetidakhadiran = 100 - $persentaseKehadiran;
            } else {
                $persentaseKehadiran = 0;
                $persentaseLembur = 0;
                $persentaseCuti = 0;
                $persentaseIzin = 0;
                $persentaseSakit = 0;
                $persentaseVerifikasi = 0;
                $persentaseKetidakhadiran = 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'totalKaryawan' => $totalKaryawan,
                    'persentaseKehadiran' => $persentaseKehadiran . '%',
                    'persentaseLembur' => $persentaseLembur . '%',
                    'persentaseKetidakhadiran' => $persentaseKetidakhadiran . '%',
                    'persentaseCuti' => $persentaseCuti . '%',
                    'persentaseIzin' => $persentaseIzin . '%',
                    'persentaseSakit' => $persentaseSakit . '%',
                    'persentaseVerifikasi' => $persentaseVerifikasi . '%'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUnitAttendanceChart()
    {
        try {
            // 1. Get PIC's managed employees
            $picEmployees = $this->getPicEmployees();

            // 2. Get periode from session (30 days)
            $periode = $this->getPeriodeDashboard();

            if ($picEmployees->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'labels' => [],
                        'data' => []
                    ]
                ]);
            }

            // 3. Get attendance data by unit
            $unitData = Employee::whereIn('id', $picEmployees)
                ->with(['unit'])
                ->get()
                ->groupBy(function ($employee) {
                    return $employee->unit->unit ?? 'Unknown';
                })
                ->map(function ($employees, $unitName) use ($periode) {
                    $employeeIds = $employees->pluck('id');

                    // Get reports for this unit's employees
                    $reports = Report::whereIn('employee_id', $employeeIds)
                        ->whereBetween('date', [$periode->start, $periode->end])
                        ->get();

                    $totalHariKerja = $reports->count();
                    $kehadiran = $reports->where('is_hadir', true)->count();

                    $persentaseKehadiran = $totalHariKerja > 0 ? round(($kehadiran / $totalHariKerja) * 100) : 0;

                    return [
                        'unit' => $unitName,
                        'persentase' => $persentaseKehadiran
                    ];
                })
                ->values();

            $labels = $unitData->pluck('unit')->toArray();
            $data = $unitData->pluck('persentase')->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'data' => $data
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load unit attendance chart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUnitPerformance()
    {
        try {
            // 1. Get PIC's managed employees
            $picEmployees = $this->getPicEmployees();

            // 2. Get periode from session (30 days)
            $periode = $this->getPeriodeDashboard();

            if ($picEmployees->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // 3. Get performance data by unit
            $unitPerformance = Employee::whereIn('id', $picEmployees)
                ->with(['unit'])
                ->get()
                ->groupBy(function ($employee) {
                    return $employee->unit->unit ?? 'Unknown';
                })
                ->map(function ($employees, $unitName) use ($periode) {
                    $employeeIds = $employees->pluck('id');
                    $totalKaryawan = $employees->count();

                    // Get reports for this unit's employees
                    $reports = Report::whereIn('employee_id', $employeeIds)
                        ->whereBetween('date', [$periode->start, $periode->end])
                        ->get();

                    $totalHariKerja = $reports->count();

                    if ($totalHariKerja > 0) {
                        $kehadiran = $reports->where('is_hadir', true)->count();
                        $cuti = $reports->where('is_cuti', true)->count();
                        $izin = $reports->where('is_izin', true)->count();
                        $sakit = $reports->where('is_sakit', true)->count();
                        $lembur = $reports->where('is_lembur', true)->count();

                        $persentaseKehadiran = round(($kehadiran / $totalHariKerja) * 100);
                        $persentaseCuti = round(($cuti / $totalHariKerja) * 100);
                        $persentaseIzin = round(($izin / $totalHariKerja) * 100);
                        $persentaseSakit = round(($sakit / $totalHariKerja) * 100);
                        $persentaseLembur = round(($lembur / $totalHariKerja) * 100);
                    } else {
                        $persentaseKehadiran = 0;
                        $persentaseCuti = 0;
                        $persentaseIzin = 0;
                        $persentaseSakit = 0;
                        $persentaseLembur = 0;
                    }

                    // Determine status based on attendance
                    $status = 'Baik';
                    if ($persentaseKehadiran < 70) {
                        $status = 'Bermasalah';
                    } elseif ($persentaseKehadiran < 85) {
                        $status = 'Perlu Perhatian';
                    }

                    return [
                        'nama' => $unitName,
                        'total' => $totalKaryawan,
                        'kehadiran' => $persentaseKehadiran . '%',
                        'cuti' => $persentaseCuti . '%',
                        'izin' => $persentaseIzin . '%',
                        'sakit' => $persentaseSakit . '%',
                        'lembur' => $persentaseLembur . '%',
                        'status' => $status,
                        'kehadiran_raw' => $persentaseKehadiran // for progress bar
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $unitPerformance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load unit performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecentActivities()
    {
        try {
            // 1. Get PIC's managed employees
            $picEmployees = $this->getPicEmployees();

            // 2. Get periode from session (30 days)
            $periode = $this->getPeriodeDashboard();

            // 3. Collect activities from various sources
            $activities = collect();

            // A. Cuti activities
            $cutiActivities = $this->getCutiActivities($picEmployees, $periode);
            $activities = $activities->merge($cutiActivities);

            // B. Izin activities
            $izinActivities = $this->getIzinActivities($picEmployees, $periode);
            $activities = $activities->merge($izinActivities);

            // C. Lembur activities
            $lemburActivities = $this->getLemburActivities($picEmployees, $periode);
            $activities = $activities->merge($lemburActivities);

            // D. Verifikasi activities
            $verifikasiActivities = $this->getVerifikasiActivities($picEmployees, $periode);
            $activities = $activities->merge($verifikasiActivities);

            // 4. Sort by time and limit
            $recentActivities = $activities
                ->sortByDesc('waktu_raw')
                ->take(10)
                ->map(function ($activity) {
                    return [
                        'nama' => $activity['nama'],
                        'unit' => $activity['unit'],
                        'aktivitas' => $activity['aktivitas'],
                        'waktu' => $this->formatRelativeTime($activity['waktu_raw']),
                        'avatar' => $activity['avatar'] ?? 'default-avatar.png',
                        'type' => $activity['type'],
                        'status' => $activity['status'] ?? null
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $recentActivities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getPicEmployees()
    {
        $user = Auth::user();

        // Parse employees column (assuming comma-separated IDs)
        if (empty($user->employees)) {
            return collect();
        }

        $employeeIds = explode(',', $user->employees);
        $employeeIds = array_map('trim', $employeeIds);
        $employeeIds = array_filter($employeeIds);

        return collect($employeeIds);
    }

    private function getPeriodeDashboard()
    {
        $periode = Session::get('periode-' . Auth::user()->id);
        if (!$periode) {
            $defaultStart = Carbon::now()->subDays(30)->toDateString();
            $defaultEnd = Carbon::now()->toDateString();
            $name = 'Last 30 Days';
            Session::put('periode-' . Auth::user()->id, [
                'start' => $defaultStart,
                'end' => $defaultEnd,
                'name' => $name
            ]);
            $periode = Session::get('periode-' . Auth::user()->id);
        }
        return (object)$periode;
    }

    private function getCutiActivities($employeeIds, $periode)
    {
        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return Cuti::whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$periode->start, $periode->end . ' 23:59:59'])
            ->with(['jenisCuti', 'employee.unit'])
            ->get()
            ->map(function ($cuti) {
                return [
                    'type' => 'cuti',
                    'nama' => $cuti->employee->nama ?? 'Unknown',
                    'unit' => $cuti->employee->unit->unit ?? 'Unknown',
                    'aktivitas' => 'Mengajukan cuti ' . ($cuti->jenisCuti->cuti ?? ''),
                    'waktu_raw' => $cuti->created_at,
                    'avatar' => $cuti->employee->picture ?? 'default-avatar.png',
                    'status' => 'pending'
                ];
            });
    }

    private function getIzinActivities($employeeIds, $periode)
    {
        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return Izin::whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$periode->start, $periode->end . ' 23:59:59'])
            ->with(['jenisIzin', 'employee.unit'])
            ->get()
            ->map(function ($izin) {
                return [
                    'type' => 'izin',
                    'nama' => $izin->employee->nama ?? 'Unknown',
                    'unit' => $izin->employee->unit->unit ?? 'Unknown',
                    'aktivitas' => 'Mengajukan izin ' . ($izin->jenisIzin->izin ?? ''),
                    'waktu_raw' => $izin->created_at,
                    'avatar' => $izin->employee->picture ?? 'default-avatar.png',
                    'status' => 'pending'
                ];
            });
    }

    private function getLemburActivities($employeeIds, $periode)
    {
        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return Lembur::whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$periode->start, $periode->end . ' 23:59:59'])
            ->with(['employee.unit'])
            ->get()
            ->map(function ($lembur) {
                return [
                    'type' => 'lembur',
                    'nama' => $lembur->employee->nama ?? 'Unknown',
                    'unit' => $lembur->employee->unit->unit ?? 'Unknown',
                    'aktivitas' => 'Mengajukan lembur ' . $lembur->lembur,
                    'waktu_raw' => $lembur->created_at,
                    'avatar' => $lembur->employee->picture ?? 'default-avatar.png',
                    'status' => 'pending'
                ];
            });
    }

    private function getVerifikasiActivities($employeeIds, $periode)
    {
        if ($employeeIds->isEmpty()) {
            return collect();
        }

        return VerifikasiAbsen::whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$periode->start, $periode->end . ' 23:59:59'])
            ->with(['employee.unit'])
            ->get()
            ->map(function ($verifikasi) {
                return [
                    'type' => 'verifikasi',
                    'nama' => $verifikasi->employee->nama ?? 'Unknown',
                    'unit' => $verifikasi->employee->unit->unit ?? 'Unknown',
                    'aktivitas' => 'Mengajukan verifikasi absen',
                    'waktu_raw' => $verifikasi->created_at,
                    'avatar' => $verifikasi->employee->picture ?? 'default-avatar.png',
                    'status' => 'pending'
                ];
            });
    }

    private function formatRelativeTime($datetime)
    {
        $diff = now()->diffInMinutes($datetime);

        if ($diff < 60) {
            return $diff . ' menit yang lalu';
        } elseif ($diff < 1440) { // 24 hours
            $hours = floor($diff / 60);
            return $hours . ' jam yang lalu';
        } else {
            $days = floor($diff / 1440);
            return $days . ' hari yang lalu';
        }
    }
}
