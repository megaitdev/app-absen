<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function getListScript($page = null)
    {
        switch ($page) {
            case "dashboard-pic":
                return [
                    // Put path script below here
                    "js/pic-dashboard.js",
                ];
            case "report-employee":
                return [
                    // Put path script below here
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "library/select2/dist/js/select2.min.js",
                    "library/timepicker/jquery.timepicker.min.js",
                    "library/datepair/dist/jquery.datepair.min.js",
                    "js/report/{$page}.js",
                ];
            case "schedule-tambah":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",
                ];
            case "report-pic":
                return [
                    // Put path script below here
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "js/report/{$page}.js",
                ];
            case "report":
                return [
                    // Put path script below here
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/select2/dist/js/select2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "js/report/{$page}.js",
                ];
            case "need-update":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "js/employee/{$page}.js",
                ];
            case "employee":
                return [
                    // Put path script below here
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "js/employee/{$page}.js",
                ];
            case "shift-tambah":
                return [
                    // Put path script below here
                    "library/bootstrap-timepicker/js/bootstrap-timepicker.js",
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "js/settings/{$page}.js",
                ];
            case "holidays-tambah":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "js/settings/{$page}.js",
                ];
            case "pic-tambah":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                ];
            case "settings":
                return [
                    // Put path script below here
                    "library/select2/dist/js/select2.min.js",
                    "library/bootstrap-timepicker/js/bootstrap-timepicker.js",
                    "library/bootstrap-daterangepicker/daterangepicker.js",
                    "library/sweetalert2/dist/sweetalert2.min.js",
                    "library/yearpicker/dist/yearpicker.js",
                    "library/izitoast/dist/js/iziToast.min.js",
                    "library/datatables/dist/js/datatables.min.js",
                    "js/settings/{$page}.js",
                ];
            case "profile":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-nomor":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",
                    "library/sweetalert/dist/sweetalert.min.js",
                    "library/moment/min/moment.min.js",
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-email":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",
                    "library/sweetalert/dist/sweetalert.min.js",
                    "library/moment/min/moment.min.js",
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
