<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    public function tambah()
    {
        $data = [
            'title' => 'Holidays - Tambah',
            'slug' => 'settings',
            'scripts' => $this->script->getListScript('holidays-tambah'),
            'csses' => $this->css->getListCss('holidays-tambah'),
            'holidays' => Holiday::paginate(10),
        ];
        return view('settings.holidays-tambah', $data);
    }

    function getHoliday($holiday_id)
    {
        $holiday = Holiday::find($holiday_id);
        return response()->json($holiday);
    }

    function datatableHolidays($year)
    {
        return DataTables()->of(
            Holiday::query()
                ->whereYear('date', $year)
                ->with('user')
        )
            ->addIndexColumn()
            ->addColumn('_date', function ($row) {
                $isPass = Carbon::now()->greaterThan($row->date);
                if ($isPass) {
                    return '<span class="badge badge-light">' . Carbon::parse($row->date)->isoFormat('Y-MM-DD') . '</span>';
                }
                return '<span class="badge badge-dark">' . Carbon::parse($row->date)->isoFormat('Y-MM-DD') . '</span>';
            })
            ->addColumn('day', function ($row) {
                $day = Carbon::parse($row->date)->isoFormat('dddd');
                return $day;
            })
            ->addColumn('action', function ($row) {
                $action = '<div onclick="javascript:deleteHoliday(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '<div onclick="javascript:editHoliday(' . $row->id . ')" id="modal-edit-' . $row->id . '" class="btn btn-sm btn-outline-primary m-1"><i class="far fa-edit"></i></div>';
                return $action;
            })
            ->rawColumns(['_date', 'action'])
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
            $validate = Holiday::where('date', $date)->first();
            if ($validate) {
                return redirect()->back()->withInput()->withErrors(['date' => 'Tanggal ' . $date->format('d M Y') . ' sudah ada pada tabel holidays']);
            }
            $dataHoliday = [
                'note' => $note,
                'date' => $date,
                'pic' => Auth::user()->id,
            ];
            Holiday::create($dataHoliday);
        }

        return redirect(url('/settings'))->with('success', 'Holiday created successfully.');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'note' => 'required|string',
        ]);
        $holiday = Holiday::find($request->holiday_id);
        $date = Carbon::parse($request->date);
        $note = $request->note;
        if ($holiday->date == $request->date) {
            Holiday::where('id', $request->holiday_id)->update([
                'note' => $note,
                'date' => $date,
                'pic' => Auth::user()->id,
            ]);
            return redirect(url('/settings'))->with('updated-holiday', 'Holiday update successfully.');
        }

        $validate = Holiday::where('date', $date)->first();
        if ($validate) {
            return redirect()->back()->withInput()->withErrors(['date' => 'Tanggal ' . $date->format('d M Y') . ' sudah ada pada tabel holidays']);
        }
        Holiday::where('id', $request->holiday_id)->update([
            'note' => $note,
            'date' => $date,
            'pic' => Auth::user()->id,
        ]);
        return redirect(url('/settings'))->with('updated-holiday', 'Holiday update successfully.');
    }

    function delete($holiday_id)
    {
        $holiday = Holiday::find($holiday_id);
        $holiday->delete();
        return response()->json(['success' => 'Holiday deleted successfully.']);
    }
}
