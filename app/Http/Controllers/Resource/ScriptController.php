<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function getListScript($page = null)
    {
        switch ($page) {
            case "report-employee":
                return [
                    // Put path script below here
                    "library/bootstrap-daterangepicker/daterangepicker.js",     // Library daterangepicker
                    "library/sweetalert2/dist/sweetalert2.min.js",     // Library sweetalert2
                    "library/datatables/dist/js/datatables.min.js",     // Library datatables
                    "js/report/{$page}.js",
                ];
            case "schedule-tambah":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",     // Library datatables
                ];
            case "report":
                return [
                    // Put path script below here
                    "library/bootstrap-daterangepicker/daterangepicker.js",     // Library daterangepicker
                    "library/sweetalert2/dist/sweetalert2.min.js",     // Library sweetalert2
                    "library/select2/dist/js/select2.min.js",     // Library datatables
                    "library/datatables/dist/js/datatables.min.js",     // Library datatables
                    "js/report/{$page}.js",
                ];
            case "need-update":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",     // Library datatables
                    "library/datatables/dist/js/datatables.min.js",     // Library datatables
                    "js/employee/{$page}.js",
                ];
            case "employee":
                return [
                    // Put path script below here
                    "library/sweetalert2/dist/sweetalert2.min.js",     // Library sweetalert2
                    "library/datatables/dist/js/datatables.min.js",     // Library datatables
                    "js/employee/{$page}.js",
                ];
            case "shift-tambah":
                return [
                    // Put path script below here
                    "library/bootstrap-timepicker/js/bootstrap-timepicker.js",     // Library timepicker
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/bootstrap-daterangepicker/daterangepicker.js",     // Library daterangepicker
                    "js/settings/{$page}.js",
                ];
            case "holidays-tambah":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/bootstrap-daterangepicker/daterangepicker.js",     // Library daterangepicker
                    "js/settings/{$page}.js",
                ];
            case "settings":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",     // Library datatables
                    "library/bootstrap-timepicker/js/bootstrap-timepicker.js",     // Library timepicker
                    "library/bootstrap-daterangepicker/daterangepicker.js",     // Library daterangepicker
                    "library/sweetalert2/dist/sweetalert2.min.js",     // Library sweetalert2
                    "library/yearpicker/dist/yearpicker.js",     // Library yearpicker
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/datatables/dist/js/datatables.min.js",     // Library datatables
                    "js/settings/{$page}.js",
                ];
            case "profile":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-nomor":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/sweetalert/dist/sweetalert.min.js",     // Library sweetalert
                    "library/moment/min/moment.min.js",     // Library moment
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-email":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/sweetalert/dist/sweetalert.min.js",     // Library sweetalert
                    "library/moment/min/moment.min.js",     // Library moment
                    "js/profile/{$page}.js",
                ];

            default:
                return [
                    // Put path script below here
                    "js/custom.js",
                ];
        }
    }
}
