<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLaporanHarian;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }

    public function settings()
    {
        $data = [
            'title' => 'Settings - Master Data',
            'slug' => 'settings',
            'scripts' => $this->script->getListScript('settings'),
            'csses' => $this->css->getListCss('settings'),
            'settings_tab' => $this->syncSettingsTab(),
        ];


        // $scanIn = "2024-10-07 06:28:00";
        // $scanIn = Carbon::createFromFormat('Y-m-d H:i:s', $scanIn)->format('Y-m-d H:i:s');
        // $date = '07.00';
        // $date = Carbon::createFromFormat('H.i', $date)->format('Y-m-d H:i:s');

        // $isLessThanDate = function ($scanIn, $date) {
        //     return Carbon::createFromFormat('Y-m-d H:i:s', $scanIn)->lt($date);
        // };

        // $diffInMinutes = function ($scanIn, $date) {
        //     return Carbon::createFromFormat('Y-m-d H:i:s', $scanIn)->diffInMinutes($date);
        // };

        // $convertTo15Minutes = function ($minutes) {
        //     return floor($minutes / 15) * 15;
        // };

        // dump($isLessThanDate($scanIn, $date));
        // dump($diffInMinutes($scanIn, $date));
        // dd($convertTo15Minutes($diffInMinutes($scanIn, $date)));
        return view('settings', $data);
    }

    function syncSettingsTab()
    {
        // Retrieve the value of 'settings-tab' from the session
        $settings_tab = Session::get('settings-tab-' . Auth::user()->id);

        // Check if the value of 'settings-tab' is not set
        if (!$settings_tab) {
            // Set the value of 'settings-tab' in the session to 'shift'
            Session::put('settings-tab-' . Auth::user()->id, 'shift');

            // Retrieve the updated value of 'settings-tab' from the session
            $settings_tab = Session::get('settings-tab-' . Auth::user()->id);
        }

        // Return the value of 'settings-tab'
        return $settings_tab;
    }
    public function setTabActive($tab)
    {
        // Set the value of 'settings-tab' in the session to the provided $tab value
        Session::put('settings-tab-' . Auth::user()->id, $tab);

        // Retrieve the value of 'settings-tab' from the session and return it as a JSON response
        return response()->json(Session::get('settings-tab-' . Auth::user()->id));
    }
}
