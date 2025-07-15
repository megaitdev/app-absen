<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }
    public function dashboard()
    {

        $data = [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'scripts' => $this->script->getListScript('dashboard-pic'),
            'csses' => ['css/custom.css']
        ];

        switch (Auth::user()->role) {
            case 'admin':
                return view('dashboard', $data);
            case 'pic':
                return view('pic.dashboard', $data);
        }
    }
}
