<?php

namespace App\Http\Controllers\Settings;

use App\Models\Shift;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    private $script;
    private $css;


    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }


    function getActiveShift()
    {
        $activeShifts = Shift::where('is_active', 1)->get();
        return response()->json($activeShifts);
    }
    function delete($shift_id)
    {
        $holiday = Shift::find($shift_id);
        $holiday->delete();
        return response()->json(['success' => 'Holiday deleted successfully.']);
    }

    function edit(Request $request)
    {

        $shift = Shift::find($request->shift_id);
        $dataShift = $request->except(['_token', 'is_sameday', 'shift_id', 'is_break']);

        if ($shift->name == $request->name) {
            $request->validate([
                'name' => 'required',
                'jam_masuk' => 'required',
                'jam_keluar' => 'required',
            ]);
        } else {
            $request->validate([
                'name' => 'required|unique:shifts',
                'jam_masuk' => 'required',
                'jam_keluar' => 'required',
            ]);
        }

        if ($request->is_sameday) {
            $dataShift['is_sameday'] = 1;
            $request->validate([
                'jam_keluar' => function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) <= strtotime($request->jam_masuk)) {
                        $fail('Jam Keluar harus lebih besar dari Jam Masuk');
                    }
                },
            ]);
        } else {
            $dataShift['is_sameday'] = 0;
            $request->validate([
                'jam_keluar' => function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) >= strtotime($request->jam_masuk)) {
                        $fail('Jam Keluar harus lebih kecil dari Jam Masuk');
                    }
                },
            ]);
        }

        $totalIstirahatMenit = 0;
        if ($request->is_break) {
            $dataShift['is_break'] = 1;
            if ($request->jam_mulai_istirahat || $request->jam_selesai_istirahat) {
                $request->validate([
                    'jam_mulai_istirahat' => 'after:jam_masuk|before:jam_keluar',
                    'jam_selesai_istirahat' => 'required|after:jam_mulai_istirahat|before:jam_keluar',
                ]);
            }

            if ($request->jam_mulai_istirahat && $request->jam_selesai_istirahat) {
                $startIstirahat = strtotime($request->jam_mulai_istirahat);
                $endIstirahat = strtotime($request->jam_selesai_istirahat);
                $totalIstirahatMenit = ceil(abs($endIstirahat - $startIstirahat) / 60);
            }
        } else {
            $dataShift['is_break'] = 0;
            $dataShift['jam_mulai_istirahat'] = null;
            $dataShift['jam_selesai_istirahat'] = null;
        }

        $start = strtotime($request->jam_masuk);
        $end = strtotime($request->jam_keluar);
        $totalMenit = ceil(abs($end - $start) / 60);

        $totalJamKerjaMenit = $totalMenit - $totalIstirahatMenit;

        $dataShift['mulai_jam_masuk'] = date('H:i', strtotime('-1 hours', $start));
        $dataShift['selesai_jam_masuk'] = date('H:i', strtotime('+2 hours', $start));

        $dataShift['mulai_jam_keluar'] = date('H:i', strtotime('-15 minutes', $end));
        $dataShift['selesai_jam_keluar'] = date('H:i', strtotime('+1 hours', $end));

        $dataShift['total_jam_kerja'] = $this->menitToEfektifJam($totalJamKerjaMenit);
        $dataShift['total_menit_kerja'] = $totalJamKerjaMenit;

        $dataShift['total_jam_istirahat'] = $this->menitToEfektifJam($totalIstirahatMenit);
        $dataShift['total_menit_istirahat'] = $totalIstirahatMenit;

        Shift::where('id', $request->shift_id)->update($dataShift);

        return redirect(url('/settings'))->with('updated-shift', 'Shift update successfully.');
    }

    function getShift(Shift $shift)
    {
        return response()->json($shift);
    }

    function datatableShift()
    {
        return DataTables()->of(
            Shift::query()
        )
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div onclick="javascript:deleteShift(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '<div onclick="javascript:editShift(' . $row->id . ')" id="modal-edit-' . $row->id . '" class="btn btn-sm btn-outline-primary m-1"><i class="far fa-edit"></i></div>';
                return $action;
            })
            ->rawColumns(['_date', 'action'])
            ->make(true);
    }
    public function tambah()
    {
        $data = [
            'title' => 'Shift',
            'slug' => 'settings',
            'scripts' => $this->script->getListScript('shift-tambah'),
            'csses' => $this->css->getListCss('shift-tambah'),
        ];
        return view('settings.shift-tambah', $data);
    }


    public function store(Request $request)
    {
        $dataShift = $request->except(['_token', 'is_sameday', 'is_break']);

        $request->validate([
            'name' => 'required|unique:shifts',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ]);

        if ($request->is_sameday) {
            $dataShift['is_sameday'] = 1;
            $request->validate([
                'jam_keluar' => function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) <= strtotime($request->jam_masuk)) {
                        $fail('Jam Keluar harus lebih besar dari Jam Masuk');
                    }
                },
            ]);
        } else {
            $request->validate([
                'jam_keluar' => function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) >= strtotime($request->jam_masuk)) {
                        $fail('Jam Keluar harus lebih kecil dari Jam Masuk');
                    }
                },
            ]);
        }

        $totalIstirahatMenit = 0;
        if ($request->is_break) {
            $dataShift['is_break'] = 1;
            if ($request->jam_mulai_istirahat || $request->jam_selesai_istirahat) {
                $request->validate([
                    'jam_mulai_istirahat' => 'after:jam_masuk|before:jam_keluar',
                    'jam_selesai_istirahat' => 'required|after:jam_mulai_istirahat|before:jam_keluar',
                ]);
            }

            if ($request->jam_mulai_istirahat && $request->jam_selesai_istirahat) {
                $startIstirahat = strtotime($request->jam_mulai_istirahat);
                $endIstirahat = strtotime($request->jam_selesai_istirahat);
                $totalIstirahatMenit = ceil(abs($endIstirahat - $startIstirahat) / 60);
            }
        }

        $start = strtotime($request->jam_masuk);
        $end = strtotime($request->jam_keluar);
        $totalMenit = ceil(abs($end - $start) / 60);

        $totalJamKerjaMenit = $totalMenit - $totalIstirahatMenit;

        $dataShift['mulai_jam_masuk'] = date('H:i', strtotime('-1 hours', $start));
        $dataShift['selesai_jam_masuk'] = date('H:i', strtotime('+2 hours', $start));

        $dataShift['mulai_jam_keluar'] = date('H:i', strtotime('-15 minutes', $end));
        $dataShift['selesai_jam_keluar'] = date('H:i', strtotime('+1 hours', $end));

        $dataShift['total_jam_kerja'] = $this->menitToEfektifJam($totalJamKerjaMenit);
        $dataShift['total_menit_kerja'] = $totalJamKerjaMenit;

        $dataShift['total_jam_istirahat'] = $this->menitToEfektifJam($totalIstirahatMenit);
        $dataShift['total_menit_istirahat'] = $totalIstirahatMenit;

        Shift::create($dataShift);
        return redirect('/settings')->with('success-shift', 'Shift created successfully.');
    }
}
