<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\Schedule;
use App\Models\Shift;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private $script;
    private $css;


    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    function delete($schedule_id)
    {
        $holiday = Schedule::find($schedule_id);
        $holiday->delete();
        return response()->json(['success' => 'Holiday deleted successfully.']);
    }

    function getSchedule($schedule_id)
    {
        $schedule = Schedule::find($schedule_id);
        return response()->json($schedule);
    }

    function edit(Request $request)
    {
        $request->validate([
            'schedule' => 'required|unique:schedules,schedule,' . $request->shcedule_id,
        ]);

        $dataSchedule = $request->except(['_token', 'shcedule_id']);
        $shifts = $request->senin;
        $shifts .= ',' . $request->selasa;
        $shifts .= ',' . $request->rabu;
        $shifts .= ',' . $request->kamis;
        $shifts .= ',' . $request->jumat;
        $shifts .= ',' . $request->sabtu;
        $shifts .= ',' . $request->minggu;
        $dataSchedule['shifts'] = $shifts;
        Schedule::where('id', $request->shcedule_id)->update($dataSchedule);

        return redirect(url('/settings'))->with('updated-schedule', 'Schedule update successfully.');
    }

    function datatableSchedule()
    {

        $shifts = Shift::where('is_active', 1)->get();
        return DataTables()->of(
            Schedule::query()
        )
            ->addIndexColumn()
            ->addColumn('_senin', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->senin)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_selasa', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->selasa)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_rabu', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->rabu)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_kamis', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->kamis)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_jumat', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->jumat)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_sabtu', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->sabtu)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })
            ->addColumn('_minggu', function ($row) use ($shifts) {
                $shift = $shifts->where('id', $row->minggu)->first();
                if ($shift) {
                    return '<a href="javascript:showDetailShift(' . $shift->id . ')" data-toggle="tooltip" title="Show detail shift!" data-placement="top"><span>' . $shift->name . '</span></a>';
                } else {
                    return '<i class="fas fa-times-circle text-danger" style="font-size: 1.5em;"></i>';
                }
            })

            ->addColumn('action', function ($row) {
                $action = '<div onclick="javascript:deleteSchedule(' . $row->id . ')" class="btn btn-sm btn-outline-danger m-1"><i class="far fa-trash-alt"></i></div>';
                $action .= '<div onclick="javascript:editSchedule(' . $row->id . ')" id="modal-edit-' . $row->id . '" class="btn btn-sm btn-outline-primary m-1"><i class="far fa-edit"></i></div>';
                return $action;
            })
            ->rawColumns(['_senin', '_selasa', '_rabu', '_kamis', '_jumat', '_sabtu', '_minggu', 'action'])
            ->make(true);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Schedule',
            'slug' => 'settings',
            'scripts' => $this->script->getListScript('schedule-tambah'),
            'csses' => $this->css->getListCss('schedule-tambah'),
            'shift' => Shift::where('is_active', 1)->get(),
        ];
        return view('settings.schedule-tambah', $data);
    }

    public function store(Request $request)
    {
        $dataSchedule = $request->except(['_token']);
        $request->validate([
            'schedule' => 'required|unique:schedules,schedule',
            'senin' => 'required',
            'selasa' => 'required',
            'rabu' => 'required',
            'kamis' => 'required',
            'jumat' => 'required',
            'sabtu' => 'required',
            'minggu' => 'required',
        ]);
        $shifts = $request->senin;
        $shifts .= ',' . $request->selasa;
        $shifts .= ',' . $request->rabu;
        $shifts .= ',' . $request->kamis;
        $shifts .= ',' . $request->jumat;
        $shifts .= ',' . $request->sabtu;
        $shifts .= ',' . $request->minggu;
        $dataSchedule['shifts'] = $shifts;

        Schedule::create($dataSchedule);
        return redirect('/settings')->with('success-schedule', 'Schedule created successfully.');
    }
}
