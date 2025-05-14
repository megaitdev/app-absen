<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
