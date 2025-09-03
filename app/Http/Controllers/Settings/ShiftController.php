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
        $dataShift = $request->except([
            '_token',
            'is_sameday',
            'shift_id',
            'is_break',
            'is_break_extra',
            'jam_mulai_istirahat_extra',
            'jam_selesai_istirahat_extra',
        ]);

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

        $startDate = $endDate = Carbon::now()->format('Y-m-d');
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
            $endDate = Carbon::now()->addDay()->format('Y-m-d');
            $request->validate([
                'jam_keluar' => function ($attribute, $value, $fail) use ($request) {
                    if (strtotime($value) >= strtotime($request->jam_masuk)) {
                        $fail('Jam Keluar harus lebih kecil dari Jam Masuk');
                    }
                },
            ]);
        }

        $totalIstirahatMenit = 0;
        $dataShift['istirahat'] = null;
        $dataShift['is_break_extra'] = 0;
        if ($request->is_break) {
            $dataShift['is_break'] = 1;
            if ($request->jam_mulai_istirahat && $request->jam_selesai_istirahat) {
                $startIstirahat = strtotime($request->jam_mulai_istirahat);
                $endIstirahat = strtotime($request->jam_selesai_istirahat);
                $totalIstirahatMenit += ceil(abs($endIstirahat - $startIstirahat) / 60);
            }

            // Extra breaks similar to store()
            if ($request->is_break_extra) {
                $dataShift['is_break_extra'] = 1;
                $mulais = $request->input('jam_mulai_istirahat_extra', []);
                $selesais = $request->input('jam_selesai_istirahat_extra', []);
                $count = max(is_array($mulais) ? count($mulais) : 0, is_array($selesais) ? count($selesais) : 0);
                $istirahatList = [];

                for ($i = 0; $i < $count; $i++) {
                    $mulaiVal = trim((string)($mulais[$i] ?? ''));
                    $selesaiVal = trim((string)($selesais[$i] ?? ''));

                    if ($mulaiVal === '' && $selesaiVal === '') {
                        continue; // skip empty rows
                    }
                    if ($mulaiVal === '' || $selesaiVal === '') {
                        return back()->withErrors(['jam_mulai_istirahat_extra.' . $i => 'Jam mulai/selesai istirahat tambahan harus diisi berpasangan'])->withInput();
                    }

                    $startTs = strtotime($mulaiVal);
                    $endTs = strtotime($selesaiVal);

                    if ($endTs <= $startTs) {
                        return back()->withErrors(['jam_selesai_istirahat_extra.' . $i => 'Jam selesai istirahat tambahan harus lebih besar dari jam mulai'])->withInput();
                    }

                    $durMenit = ceil(abs($endTs - $startTs) / 60);
                    $istirahatList[] = [
                        'mulai' => $mulaiVal,
                        'selesai' => $selesaiVal,
                        'total_menit' => $durMenit,
                    ];
                }

                if (!empty($istirahatList)) {
                    $dataShift['istirahat'] = $istirahatList;
                }
            }
        } else {
            $dataShift['is_break'] = 0;
            $dataShift['is_break_extra'] = 0;
            $dataShift['jam_mulai_istirahat'] = null;
            $dataShift['jam_selesai_istirahat'] = null;
        }

        $start = strtotime($startDate . ' ' . $request->jam_masuk);
        $end = strtotime($endDate . ' ' . $request->jam_keluar);
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
            Shift::query()->where('id', '>', 5)
                ->orderBy('created_at', 'desc')
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

        $dataShift = $request->except([
            '_token',
            'is_sameday',
            'is_break',
            'is_break_extra',
            'jam_mulai_istirahat_extra',
            'jam_selesai_istirahat_extra',
        ]);

        $startDate = $endDate = Carbon::now()->format('Y-m-d');

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
            $endDate = Carbon::now()->addDay()->format('Y-m-d');
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
            if ($request->jam_mulai_istirahat && $request->jam_selesai_istirahat) {
                $startIstirahat = strtotime($request->jam_mulai_istirahat);
                $endIstirahat = strtotime($request->jam_selesai_istirahat);
                $totalIstirahatMenit = ceil(abs($endIstirahat - $startIstirahat) / 60);
            }
        }

        // Process multiple extra breaks and store as structured array in istirahat
        $dataShift['istirahat'] = null;
        if ($request->is_break && $request->is_break_extra) {
            $dataShift['is_break_extra'] = 1;
            $mulais = $request->input('jam_mulai_istirahat_extra', []);
            $selesais = $request->input('jam_selesai_istirahat_extra', []);
            $count = max(is_array($mulais) ? count($mulais) : 0, is_array($selesais) ? count($selesais) : 0);
            $istirahatList = [];

            for ($i = 0; $i < $count; $i++) {
                $mulaiVal = trim((string)($mulais[$i] ?? ''));
                $selesaiVal = trim((string)($selesais[$i] ?? ''));

                if ($mulaiVal === '' && $selesaiVal === '') {
                    continue; // skip empty rows
                }
                if ($mulaiVal === '' || $selesaiVal === '') {
                    return back()->withErrors(['jam_mulai_istirahat_extra.' . $i => 'Jam mulai/selesai istirahat tambahan harus diisi berpasangan'])->withInput();
                }

                $startTs = strtotime($startDate . ' ' . $mulaiVal);
                $endTs = strtotime($endDate . ' ' . $selesaiVal);

                if ($endTs <= $startTs) {
                    return back()->withErrors(['jam_selesai_istirahat_extra.' . $i => 'Jam selesai istirahat tambahan harus lebih besar dari jam mulai'])->withInput();
                }

                $durMenit = ceil(abs($endTs - $startTs) / 60);
                $istirahatList[] = [
                    'mulai' => $mulaiVal,
                    'selesai' => $selesaiVal,
                    'total_menit' => $durMenit,
                ];
            }

            if (!empty($istirahatList)) {
                $dataShift['istirahat'] = $istirahatList;
            }
        }


        $start = strtotime($startDate . ' ' . $request->jam_masuk);
        $end = strtotime($endDate . ' ' . $request->jam_keluar);
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

        // dd($dataShift);
        Shift::create($dataShift);
        return redirect('/settings')->with('success-shift', 'Shift created successfully.');
    }
}
