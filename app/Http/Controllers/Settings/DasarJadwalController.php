<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\DasarJadwal;
use App\Models\mak_hrd\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;
use App\Models\Shift;

class DasarJadwalController extends Controller
{
    private $script;
    private $css;


    public function getEmployeeDasarJadwal($employeeId)
    {
        $employee = Employee::with('dasarJadwal.schedule')
            ->findOrFail($employeeId);

        $schedules = $employee->dasarJadwal->map(function ($dasarJadwal) {
            return [
                'schedule_id' => $dasarJadwal->schedule_id,
                'schedule_name' => $dasarJadwal->schedule->schedule,
                'start_date' => $dasarJadwal->start_date,
                'end_date' => $dasarJadwal->end_date,
                'is_active' => $dasarJadwal->is_active
            ];
        });

        return response()->json($schedules);
    }

    public function getScheduleStatistics()
    {
        $schedules = Schedule::with('dasarJadwals')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'name' => $schedule->schedule,
                    'employee_count' => $schedule->dasarJadwals->where('is_active', 1)->count(),
                    'shifts' => [
                        'senin' => $schedule->shiftSenin,
                        'selasa' => $schedule->shiftSelasa,
                        'rabu' => $schedule->shiftRabu,
                        'kamis' => $schedule->shiftKamis,
                        'jumat' => $schedule->shiftJumat,
                        'sabtu' => $schedule->shiftSabtu,
                        'minggu' => $schedule->shiftMinggu,
                    ]
                ];
            });

        return response()->json($schedules);
    }

    public function getScheduleInfo()
    {
        try {
            // Eager load relationships and cache shifts
            $shifts = Shift::all()->keyBy('id');
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            $schedules = Schedule::with(['dasarJadwals' => function ($query) {
                $query->where('is_active', 1);
            }])->get()->map(function ($schedule) use ($shifts, $days) {
                $shiftIds = array_pad(explode(',', $schedule->shifts), 7, 'libur');

                return [
                    'id' => $schedule->id,
                    'name' => $schedule->schedule,
                    'employee_count' => $schedule->dasarJadwals->count(),
                    'details' => collect($days)->map(function ($day, $index) use ($shiftIds, $shifts) {
                        return [
                            'day' => $day,
                            'shift' => isset($shifts[$shiftIds[$index]]) ? $shifts[$shiftIds[$index]]->name : '-',
                            'time' => isset($shifts[$shiftIds[$index]])
                                ? $shifts[$shiftIds[$index]]->jam_masuk . ' - ' . $shifts[$shiftIds[$index]]->jam_keluar
                                : '<span class="libur">Libur</span>'
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => 'success',
                'schedules' => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat informasi jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    function getFilterEmployee($schedule_id)
    {
        $query = Employee::where('is_deleted', 0)
            ->with('unit', 'divisi')
            ->whereHas('dasarJadwal', function ($q) use ($schedule_id) {
                $q->where('schedule_id', '<>', $schedule_id)
                    ->where('is_active', 1);
            });

        $employees = $query->get('id');
        $units = $employees->pluck('unit')->unique('id')->sort()->values();
        $divisions = $employees->pluck('divisi')->unique('id')->sort()->values();
        $unitIds = $units->pluck('id')->unique();
        $divisionIds = $divisions->pluck('id')->unique();


        return response()->json([
            'employees' => $employees,
            'units' => $units,
            'divisions' => $divisions,
            'unit_ids' => $unitIds,
            'division_ids' => $divisionIds,

        ]);
    }

    public function statistics()
    {
        try {
            // Get total schedules
            $totalSchedules = Schedule::count();

            // Get total employees with active schedules
            $totalEmployees = Employee::whereHas('dasarJadwal', function ($query) {
                $query->where('is_active', 1);
            })->count();

            // Get employees without schedules
            $employeesWithoutSchedule = Employee::where('is_deleted', 0)
                ->whereDoesntHave('dasarJadwal')
                ->count();

            return response()->json([
                'total_schedules' => $totalSchedules,
                'total_employees' => $totalEmployees,
                'employees_without_schedule' => $employeesWithoutSchedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    function getDasarJadwal($holiday_id)
    {
        $holiday = DasarJadwal::find($holiday_id);
        return response()->json($holiday);
    }

    function datatableDasarJadwalAllEmployee(Request $request)
    {
        $units = $request->input('units', []);
        $divisions = $request->input('divisions', []);
        $search = $request->input('search.value', '');

        // Initialize the query
        $query = Employee::where('is_deleted', 0)
            ->with(['unit', 'divisi', 'dasar_jadwal_active' => function ($query) {
                $query->where('is_active', 1);
            }, 'dasar_jadwal_active.schedule']);

        // Filter by units if provided
        if (!empty($units)) {
            $query->whereHas('unit', function ($q) use ($units) {
                $q->whereIn('unit_id', $units);
            });
        }
        // Filter by divisions if provided
        if (!empty($divisions)) {
            $query->whereHas('divisi', function ($q) use ($divisions) {
                $q->whereIn('divisi_id', $divisions);
            });
        }
        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('unit', 'like', "%{$search}%");
                })
                    ->orWhereHas('divisi', function ($q) use ($search) {
                        $q->where('divisi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dasar_jadwal_active.schedule', function ($q) use ($search) {
                        $q->where('schedule', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables()->of(
            $query
        )
            ->addIndexColumn()
            ->addColumn('jadwal', function ($row) {
                return $row->dasar_jadwal_active->schedule->schedule ?? '-';
            })
            ->addColumn('start_date', function ($row) {
                return $row->dasar_jadwal_active->start_date ?? '-';
            })
            ->addColumn('is_active', function ($row) {
                return $row->dasar_jadwal_active->is_active ?? 0;
            })
            ->rawColumns(['jadwal', 'action'])
            ->make(true);
    }

    public function datatableDasarJadwaAddEmployee(Request $request, $schedule_id = null)
    {
        $units = $request->input('units', []);
        $divisions = $request->input('divisions', []);
        $selectedEmployees = $request->input('employees', []);
        $showSelected = $request->input('show', 'false');
        $search = $request->input('search.value', '');
        if (empty($schedule_id)) {
            $schedule_id = $request->input('schedule_id', null);
        }

        $query = Employee::where('is_deleted', 0)
            ->with('unit', 'divisi')
            ->whereHas('dasarJadwal', function ($q) use ($schedule_id) {
                $q->where('schedule_id', '<>', $schedule_id)
                    ->where('is_active', 1);
            });


        // Filter by units if provided
        if (!empty($units)) {
            $query->whereHas('unit', function ($q) use ($units) {
                $q->whereIn('unit_id', $units);
            });
        }
        // Filter by divisions if provided
        if (!empty($divisions)) {
            $query->whereHas('divisi', function ($q) use ($divisions) {
                $q->whereIn('divisi_id', $divisions);
            });
        }

        // Filter selected employees only
        if ($showSelected == 'true' && !empty($selectedEmployees)) {
            $query->whereIn('id', $selectedEmployees);
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('unit', 'like', "%{$search}%");
                })
                    ->orWhereHas('divisi', function ($q) use ($search) {
                        $q->where('divisi', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables()->of($query)
            ->addIndexColumn()
            ->make(true);
    }
    public function datatableDasarJadwaViewEmployee(Request $request, $schedule_id = null)
    {
        $units = $request->input('units', []);
        $divisions = $request->input('divisions', []);
        $search = $request->input('search.value', '');
        if (empty($schedule_id)) {
            $schedule_id = $request->input('schedule_id', null);
        }

        $query = Employee::where('is_deleted', 0)
            ->with(['unit', 'divisi', 'dasarJadwal' => function ($query) {
                $query->where('is_active', 1);
            }])
            ->whereHas('dasarJadwal', function ($q) use ($schedule_id) {
                $q->where('schedule_id', $schedule_id)
                    ->where('is_active', 1);
            });


        // Filter by units if provided
        if (!empty($units)) {
            $query->whereHas('unit', function ($q) use ($units) {
                $q->whereIn('unit_id', $units);
            });
        }
        // Filter by divisions if provided
        if (!empty($divisions)) {
            $query->whereHas('divisi', function ($q) use ($divisions) {
                $q->whereIn('divisi_id', $divisions);
            });
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('unit', 'like', "%{$search}%");
                })
                    ->orWhereHas('divisi', function ($q) use ($search) {
                        $q->where('divisi', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables()->of($query)
            ->addIndexColumn()
            ->addColumn('count_jadwal', function ($row) {
                $action = '<div onclick="javascript:getJadwalKaryawan(' . $row->id . ')" class="btn btn-sm btn-outline-secondary m-1"><i class="far fa-calendar-alt"></i> ' . $row->countJadwal() . ' Jadwal</div>';
                return $action;
            })
            ->rawColumns(['count_jadwal'])
            ->make(true);
    }

    // Menyimpan holiday baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'note' => 'required|string',
        ]);
        $start_date = $this->getTanggalFromString($request->date, 'mulai');
        $end_date = $this->getTanggalFromString($request->date, 'selesai');
        $period = CarbonPeriod::create($start_date, $end_date);
        $note = $request->note;

        foreach ($period as $i => $date) {
            $validate = DasarJadwal::where('date', $date)->first();
            if ($validate) {
                return redirect()->back()->withInput()->withErrors(['date' => 'Tanggal ' . $date->format('d M Y') . ' sudah ada pada tabel dasar-jadwal']);
            }
            $dataDasarJadwal = [
                'note' => $note,
                'date' => $date,
                'pic' => Auth::user()->id,
            ];
            DasarJadwal::create($dataDasarJadwal);
        }

        return redirect(url('/settings'))->with('success', 'DasarJadwal created successfully.');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'note' => 'required|string',
        ]);
        $holiday = DasarJadwal::find($request->holiday_id);
        $date = Carbon::parse($request->date);
        $note = $request->note;
        if ($holiday->date == $request->date) {
            DasarJadwal::where('id', $request->holiday_id)->update([
                'note' => $note,
                'date' => $date,
                'pic' => Auth::user()->id,
            ]);
            return redirect(url('/settings'))->with('updated-holiday', 'DasarJadwal update successfully.');
        }

        $validate = DasarJadwal::where('date', $date)->first();
        if ($validate) {
            return redirect()->back()->withInput()->withErrors(['date' => 'Tanggal ' . $date->format('d M Y') . ' sudah ada pada tabel dasar-jadwal']);
        }
        DasarJadwal::where('id', $request->holiday_id)->update([
            'note' => $note,
            'date' => $date,
            'pic' => Auth::user()->id,
        ]);
        return redirect(url('/settings'))->with('updated-holiday', 'DasarJadwal update successfully.');
    }

    function delete($holiday_id)
    {
        $holiday = DasarJadwal::find($holiday_id);
        $holiday->delete();
        return response()->json(['success' => 'DasarJadwal deleted successfully.']);
    }

    public function addEmployees(Request $request)
    {
        try {
            $schedule_id = (int) $request->schedule_id;
            $employee_ids = json_decode($request->selected_employees);
            $starDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');

            // Start a database transaction
            DB::beginTransaction();

            foreach ($employee_ids as $employee_id) {
                // Deactivate old schedules for this employee
                DasarJadwal::where('employee_id', $employee_id)
                    ->where('is_active', 1)
                    ->update(['is_active' => 0, 'end_date' => $endDate]);
                DasarJadwal::where('employee_id', $employee_id)
                    ->where('start_date', '>=', $starDate)
                    ->delete();

                // Create new schedule for the employee
                DasarJadwal::create([
                    'employee_id' => $employee_id,
                    'schedule_id' => $schedule_id,
                    'start_date' => $starDate, // Current date as start date
                    'end_date' => null, // Null end_date for active schedule
                    'is_active' => 1
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan ke jadwal',
                'data' => [
                    'schedule_id' => $schedule_id,
                    'employee_ids' => $employee_ids,
                    'start_date' => $starDate,
                    'end_date' => $endDate,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan ke jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
