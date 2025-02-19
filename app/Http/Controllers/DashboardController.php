<?php

namespace App\Http\Controllers;

use App\Models\EmployeeFtm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function dashboard()
    {

        $data = [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'scripts' => ['js/custom.js'],
            'csses' => ['css/custom.css']
        ];

        return view('dashboard', $data);
    }
}
