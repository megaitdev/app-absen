<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PicController extends Controller
{
    private $script;
    private $css;

    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    function getPic($pic_id)
    {
        $pic = User::find($pic_id);
        if ($pic) {
            return response()->json(['data' => $pic]);
        } else {
            return response()->json(['error' => 'PIC not found.'], 404);
        }
    }

    function edit(Request $request)
    {
        $pic = User::find($request->id);
        $pic->nama = $request->nama_pic;
        $pic->nomor_wa = $request->nomor_wa;
        $pic->username = $request->username;
        if (!is_null($request->password)) {
            $pic->password = bcrypt($request->password);
        }
        $pic->pic = $request->pic;
        $pic->employees = $request->selected_employee_ids;
        $pic->save();
        return redirect(url('/settings'))->with('updated-pic', 'PIC update successfully.');
        return response()->json(['success' => 'PIC updated successfully.', 'data' => $request->all()]);
    }

    function editAjax($pic_id)
    {
        $pic = User::find($pic_id);
        $allEmployees = Employee::where('is_deleted', 0)->orderBy('nama', 'asc')->get();
        $units = Unit::where('status', 1)->orderBy('unit', 'asc')->get();

        $selectedEmployeeIds = json_decode($pic->employees);
        if ($pic) {
            return response()->json([
                'pic' => $pic,
                'managed_employees' => $selectedEmployeeIds,
                'all_employees' => $allEmployees,
                'units' => $units
            ]);
        } else {
            return response()->json(['error' => 'PIC not found.'], 404);
        }
    }

    function delete($pic_id)
    {
        $pic = User::find($pic_id);
        $pic->delete();
        return response()->json(['success' => 'PIC deleted successfully.']);
    }

    function tambah()
    {
        $data = [
            'title' => 'PIC',
            'slug' => 'settings',
            'scripts' => $this->script->getListScript('pic-tambah'),
            'csses' => $this->css->getListCss('pic-tambah'),
            'employees' => Employee::where('is_deleted', 0)->orderBy('nama', 'asc')->get(),
            'units' => Unit::where('status', 1)->orderBy('unit', 'asc')->get(),
        ];
        return view('settings.pic-tambah', $data);
    }

    function store(Request $request)
    {
        // Validate common fields
        $request->validate([
            'nama_pic' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
            'pic' => 'required',
            'selected_employee_ids' => 'required',
        ]);

        $employee = Employee::find($request->employee_id);

        if ($employee) {
            User::create([
                'employee_id' => $request->employee_id ?? null,
                'nama' => $request->nama_pic,
                'nomor_wa' => $request->nomor_wa,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'pic' => $request->pic,
                'role' => 'pic',
                'employees' => $request->selected_employee_ids ?? null,
            ]);
            return redirect('/settings')->with('success-pic', 'PIC created successfully.');
        } else {
            return redirect()->back()->with('error', 'Employee not found.');
        }
    }

    function showPic($id)
    {
        $pic = User::find($id);
        $employeeIds = json_decode($pic->employees);

        $employees = [];
        if (!empty($employeeIds)) {
            $employees = Employee::with('unit')
                ->whereIn('id', $employeeIds)
                ->where('is_deleted', 0)
                ->orderBy('nama', 'asc')
                ->get();
        }

        return response()->json([
            'pic' => $pic,
            'employees' => $employees
        ]);
    }

    function datatableEmployeesPic(Request $request)
    {
        $units = $request->input('units', []);
        $selectedEmployeeIds = $request->input('employees', []);
        $showSelected = $request->input('show', 'false');
        $search = $request->input('search.value', '');
        $perPage = $request->input('length', 10);
        $page = $request->input('start', 0) / $perPage + 1;

        $query = Employee::where('is_deleted', 0)
            ->orderBy('nama', 'asc')
            ->with('unit');

        // Filter by units if provided
        if (!empty($units)) {
            $query->whereHas('unit', function ($q) use ($units) {
                $q->wherein('unit_id', $units);
            });
        }

        if ($showSelected == 'true') {
            $query->whereIn('id', $selectedEmployeeIds);
        }

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('unit', 'like', "%{$search}%");
                });
            });
        }

        // Get total records count
        $recordsTotal = $query->count();

        // Get paginated data
        if ($perPage == -1) {
            $employees = $query->paginate($recordsTotal, ['*'], 'page', $page);
        } else {
            $employees = $query->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $employees->items(),
            'pagination' => [
                'more' => $employees->hasMorePages()
            ]
        ]);
    }

    function datatablePic(Request $request)
    {
        $query = User::where('role', 'pic');
        $order = $request->input('order.0.name', '');
        $orderDir = $request->input('order.0.dir', '');
        if ($order == 'mengelola') {
            $query->orderByRaw('JSON_LENGTH(employees) ' . $orderDir);
        }
        return DataTables()->of(
            $query
        )
            ->addIndexColumn()
            ->addColumn('mengelola', function ($row) {
                return '<div class="btn btn-sm btn-info" onclick="javascript:showEmployees(' . $row->id . ')">' . count(json_decode($row->employees)) . ' Karyawan</div>';
            })
            ->addColumn('action', function ($row) {
                $action = '<div onclick="javascript:deletePic(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '<div onclick="javascript:editPic(' . $row->id . ')" id="modal-edit-' . $row->id . '" class="btn btn-sm btn-outline-primary m-1"><i class="far fa-edit"></i></div>';
                return $action;
            })
            ->rawColumns(['_date', 'action', 'mengelola'])
            ->make(true);
    }
}
