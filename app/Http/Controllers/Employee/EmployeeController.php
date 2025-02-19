<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\ftm\Cabang;
use App\Models\ftm\Departemen;
use App\Models\ftm\EmployeeFtm;
use App\Models\mak_hrd\Divisi;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Posisi;
use App\Models\mak_hrd\Unit;
use App\Models\mak_matrix_produk\Kategori;
use App\Models\mak_matrix_produk\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{

    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }


    function synchronizePinEmployee()
    {
        // Define the two database connections
        $hrdConnection = DB::connection('mysql_hrd');
        $ftmConnection = DB::connection('mysql_ftm');

        // Query from the HRD database
        $hrdEmployees = $hrdConnection->table('employees')
            ->select(
                'employees.id',
                'employees.nip',
                'employees.nama',
                'employees.pin'
            )
            ->where('employees.is_deleted', 0)
            ->whereNull('employees.pin')
            ->get();

        // Query from the FTM database
        $ftmEmployees = $ftmConnection->table('emp')
            ->select(
                'emp.nik',
                'emp.emp_id_auto',
                'emp.pin'
            )
            ->get();

        // Combine the results
        $joinedEmployees = $hrdEmployees->map(function ($hrdEmployee) use ($ftmEmployees) {
            $ftmEmployee = $ftmEmployees->where('nik', $hrdEmployee->nip)->first();
            return (object) array_merge(
                (array) $hrdEmployee,
                $ftmEmployee ? ['emp_id_auto' => $ftmEmployee->emp_id_auto, 'pin' => $ftmEmployee->pin] : ['emp_id_auto' => null, 'pin' => null]
            );
        });

        // Update the pin of the employees in the HRD database
        foreach ($joinedEmployees as $i => $employee) {
            Employee::where('id', $employee->id)->update([
                'pin' => $employee->pin
            ]);
        }
    }

    function synchronizeEmployee()
    {
        // Synchronize the pin of the employees in the HRD database
        $this->synchronizePinEmployee();

        // Synchronize the employee data in the FTM database
        $employees = Employee::where('is_deleted', 0)->orderBY('nama', 'asc')->get();
        $employee_ftms = EmployeeFtm::all();


        foreach ($employees as $employee) {
            $employee_ftm = $employee_ftms->where('nik', $employee->nip)->first();
            if ($employee_ftm) {
                if ($employee->updated_at->greaterThan($employee_ftm->lastupdate_date)) {
                    $employee_ftm->alias = $employee->nama;
                    $employee_ftm->nik = $employee->nip;
                    $employee_ftm->pin = $employee->pin;
                    $employee_ftm->save();
                } else if ($employee->updated_at->lessThan($employee_ftm->lastupdate_date)) {
                    $employee->nama = $employee_ftm->alias;
                    $employee->nip = $employee_ftm->nik;
                    $employee->pin = $employee_ftm->pin;
                    $employee->save();
                }
            }
        }
    }

    function editEmployeeNeedUpdate(Employee $employee, Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required',
            'pin_need_update' => 'required',
            'divisi' => 'required',
            'unit' => 'required',
        ]);

        if ($employee->nip != $request->nip) {
            $nikExist = Employee::where('nip', $request->nip)->first();
            if ($nikExist) {
                return redirect()->back()->withErrors(['nip' => 'NIK sudah ada di database'])->withInput();
            }
        }

        $employee->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'pin' => $request->pin_need_update,
        ]);

        Posisi::where('id', $employee->posisi->id)->update([
            'divisi_id' => $request->divisi,
            'unit_id' => $request->unit
        ]);

        return redirect(url('/employee/need-update'))->with('success-need-update', 'Data karyawan berhasil diupdate');
    }


    function getEmployeeNeedUpdate(Employee $employee)
    {

        $employee->load('divisi', 'unit');
        return response()->json($employee);
    }

    function editEmployeeFtm(EmployeeFtm $emp, Request $request)
    {
        $request->validate([
            'alias' => 'required',
            'nik' => 'required',
            'pin_ftm' => 'required',
            'cabang' => 'required',
            'departemen' => 'required',
        ]);

        if ($emp->nik != $request->nik) {
            $nikExist = EmployeeFtm::where('nik', $request->nik)->where('emp_id_auto', '!=', $emp->emp_id_auto)->first();
            if ($nikExist) {
                return redirect()->back()->withErrors(['nik' => 'NIK sudah ada di database'])->withInput();
            }
        }

        $emp->update([
            'alias' => $request->alias,
            'nik' => $request->nik,
            'cab_id_auto' => $request->cabang,
            'dept_id_auto' => $request->departemen,
            'pin' => $request->pin_ftm,
            'lastupdate_date' => now()->format('Y-m-d H:i:s'),
        ]);

        // return redirect()->back()->with('success-ftm', 'Data karyawan berhasil diupdate');
        return redirect(url('/employee/need-update'))->with('success-ftm', 'Data karyawan berhasil diupdate');
    }
    public function getEmployeeFtm(EmployeeFtm $emp)
    {
        return response()->json($emp);
    }

    function needUpdate()
    {
        $data = [
            'title' => 'Need Update',
            'slug' => 'employee',
            'scripts' => $this->script->getListScript('need-update'),
            'csses' => $this->css->getListCss('need-update'),
            'form_ftm' => $this->getDataFormFtm(),
            'form_need_update' => $this->getDataFormNeedUpdate(),
        ];

        return view('employee.need-update', $data);
    }

    function getDataFormNeedUpdate()
    {
        $data = [
            'divisi' => Divisi::where('status', 1)->get(),
            'unit' => Unit::where('status', 1)->get()
        ];
        return $data;
    }
    function getDataFormFtm()
    {
        $data = [
            'cabang' => Cabang::all(),
            'departemen' => Departemen::all()
        ];
        return $data;
    }

    function employee()
    {
        $data = [
            'title' => 'Employee',
            'slug' => 'employee',
            'scripts' => $this->script->getListScript('employee'),
            'csses' => $this->css->getListCss('employee'),
            'last_update' => $this->getLastUpdate(),
        ];

        return view('employee.employee', $data);
    }

    public function getLastUpdate()
    {
        $employeeLastUpdate = Employee::max('updated_at');
        $employeeFtmLastUpdate = EmployeeFtm::max('lastupdate_date');

        return [
            'employee' => $employeeLastUpdate,
            'employee_ftm' => $employeeFtmLastUpdate,
            'latest' => max($employeeLastUpdate, $employeeFtmLastUpdate)
        ];
    }

    function datatableFtmAll()
    {
        return DataTables()->of(
            EmployeeFtm::query()
                ->with('departemen', 'cabang', 'is_sync')
        )
            ->addIndexColumn()
            ->addColumn('_is_sync', function ($row) {
                if ($row->is_sync == null) {
                    return '<i class="fas fa-times text-danger"></i>';
                }
                return '<i class="fas fa-check text-success"></i>';
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex justify-content-around">';
                $action .= '<div onclick="javascript:editEmployeeFtm(' . $row->emp_id_auto . ')" class="btn btn-sm btn-outline-primary m-1"><i class="fas fa-user-edit"></i></div>';
                // $action .= '<div onclick="javascript:deleteEmployeeFtm(' . $row->emp_id_auto . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '</div>';
                return $action;
            })
            ->rawColumns(['_is_sync', 'action'])
            ->make(true);
    }

    function datatableEmployeeNeedUpdate()
    {
        $listNikEmployeeFtm = EmployeeFtm::all()->pluck('nik')->toArray();
        return DataTables()->of(
            Employee::query()
                ->where('is_deleted', 0)
                ->whereNotIn('nip', $listNikEmployeeFtm)
                ->with('unit', 'divisi')
        )
            ->addIndexColumn()
            ->addColumn('_pin', function ($row) {
                if ($row->pin == null) {
                    return '<i class="fas fa-times text-danger"></i>';
                }
                return $row->pin;
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex justify-content-around">';
                $action .= '<div onclick="javascript:editEmployeeNeedUpdate(' . $row->id . ')" class="btn btn-sm btn-outline-primary m-1"><i class="fas fa-user-edit"></i></div>';
                // $action .= '<div onclick="javascript:deleteEmployeeNeedUpdate(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '</div>';
                return $action;
            })
            ->rawColumns(['_pin', 'action'])
            ->make(true);
    }

    function countSynchronizedEmployees()
    {
        $employeesHRD = Employee::where('is_deleted', 0)
            ->whereNotNull('pin')
            ->get();

        $employeesFTM = DB::connection('mysql_ftm')
            ->table('emp')
            ->select('nik', 'alias', 'pin')
            ->get();

        $synchronizedCount = $employeesHRD->filter(function ($employeeHRD) use ($employeesFTM) {
            return $employeesFTM->contains(function ($employeeFTM) use ($employeeHRD) {
                return $employeeFTM->nik == $employeeHRD->nip &&
                    $employeeFTM->alias == $employeeHRD->nama &&
                    $employeeFTM->pin == $employeeHRD->pin;
            });
        })->count();

        return $synchronizedCount;
    }

    function countUnsynchronizedData()
    {
        $employeesHRD = Employee::where('is_deleted', 0)->get(['id', 'nip', 'nama', 'pin']);
        $employeesFTM = EmployeeFtm::all(['nik', 'alias', 'pin'])->keyBy('nik');

        return $employeesHRD->filter(function ($employeeHRD) use ($employeesFTM) {
            $employeeFTM = $employeesFTM->get($employeeHRD->nip);
            return !$employeeFTM ||
                $employeeHRD->nama !== $employeeFTM->alias ||
                $employeeHRD->pin !== $employeeFTM->pin;
        })->count();
    }

    function getInfoEmployee()
    {
        $listNikEmployeeFtm = EmployeeFtm::all()->pluck('nik')->toArray();
        $result = [
            'sinkron' => $this->countSynchronizedEmployees(),
            'belum_sinkron' => $this->countUnsynchronizedData(),
            'need_update' => Employee::where('is_deleted', 0)->whereNotIn('nip', $listNikEmployeeFtm)->count(),
        ];

        return $result;
    }
    function datatableEmployee()
    {
        return DataTables()->of(
            Employee::query()
                ->where('is_deleted', 0)
                ->with('unit', 'divisi')
        )
            ->addIndexColumn()
            ->addColumn('_pin', function ($row) {
                if ($row->pin == null) {
                    return '❌';
                }
                return $row->pin;
            })
            ->addColumn('action', function ($row) {
                if ($row->pin == null) {
                    return '<a href="javascript:pin(' . $row->id . ')" class="btn btn-success m-1"><i class="fas fa-key"></i> Pin</a>';
                }
                return '<a href="javascript:view(' . $row->id . ')" class="btn btn-dark m-1"><i class="fas fa-search"></i> View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    function synchronizeEmployees()
    {
        try {
            $this->synchronizeEmployee();
            return response()->json(['message' => 'Synchronization completed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Synchronization failed: ' . $e->getMessage()], 500);
        }
    }
}
