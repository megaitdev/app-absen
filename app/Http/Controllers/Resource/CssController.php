<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CssController extends Controller
{
    public function getListCss($page = null)
    {
        switch ($page) {
            case "report-employee":
                return [
                    // Put path css below here
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                ];
            case "schedule-tambah":
                return [
                    // Put path css below here
                    'library/select2/dist/css/select2.min.css',
                ];
            case "report":
                return [
                    // Put path css below here
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                    'library/select2/dist/css/select2.min.css',
                ];
            case "need-update":
                return [
                    // Put path css below here
                    'library/datatables/dist/css/datatables.min.css',
                    'library/select2/dist/css/select2.min.css',
                ];
            case "employee":
                return [
                    // Put path css below here
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                ];
            case "shift-tambah":
                return [
                    // Put path css below here
                    'library/bootstrap-timepicker/css/bootstrap-timepicker.css', // Library timepicker
                    "library/bootstrap-daterangepicker/daterangepicker.css",     // Library daterangepicker
                ];
            case "holidays-tambah":
                return [
                    // Put path css below here
                    "library/bootstrap-daterangepicker/daterangepicker.css",     // Library daterangepicker
                ];
            case "settings":
                return [
                    // Put path css below here
                    'library/select2/dist/css/select2.min.css',
                    'library/bootstrap-timepicker/css/bootstrap-timepicker.css', // Library timepicker
                    "library/bootstrap-daterangepicker/daterangepicker.css",     // Library daterangepicker
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/yearpicker/dist/yearpicker.css',
                    'library/datatables/dist/css/datatables.min.css',
                    "css/settings/{$page}.css",
                ];

            default:
                return [
                    // Put path css below here
                    'css/custom.css'
                ];
        }
    }
}
