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
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                    'library/select2/dist/css/select2.min.css',
                    "library/timepicker/jquery.timepicker.min.css",
                ];
            case "schedule-tambah":
                return [
                    'library/select2/dist/css/select2.min.css',
                ];
            case "report-pic":
                return [
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                ];
            case "report":
                return [
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                    'library/select2/dist/css/select2.min.css',
                ];
            case "need-update":
                return [
                    'library/datatables/dist/css/datatables.min.css',
                    'library/select2/dist/css/select2.min.css',
                ];
            case "employee":
                return [
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                ];
            case "shift-tambah":
                return [
                    'library/bootstrap-timepicker/css/bootstrap-timepicker.css',
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                ];
            case "holidays-tambah":
                return [
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                ];
            case "pic-tambah":
                return [
                    'library/select2/dist/css/select2.min.css',
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/datatables/dist/css/datatables.min.css',
                ];
            case "settings":
                return [
                    'library/select2/dist/css/select2.min.css',
                    'library/bootstrap-timepicker/css/bootstrap-timepicker.css',
                    "library/bootstrap-daterangepicker/daterangepicker.css",
                    'library/sweetalert2/dist/sweetalert2.min.css',
                    'library/yearpicker/dist/yearpicker.css',
                    'library/datatables/dist/css/datatables.min.css',
                    "css/settings/{$page}.css",
                ];

            default:
                return [
                    'css/custom.css'
                ];
        }
    }
}
