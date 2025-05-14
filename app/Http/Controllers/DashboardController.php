<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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

        switch (Auth::user()->role) {
            case 'admin':
                return view('dashboard', $data);
            case 'pic':
                return view('pic.dashboard', $data);
        }
    }
}
