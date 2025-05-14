<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\LampiranController;
use App\Http\Controllers\Pic\PicDashboardController;
use App\Http\Controllers\Settings\HolidayController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\VerifikasiController;
use App\Http\Controllers\Report\ApiReportController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\SanboxController;
use App\Http\Controllers\Settings\DasarJadwalController;
use App\Http\Controllers\Settings\PicController;
use App\Http\Controllers\Settings\ScheduleController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Settings\ShiftController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return redirect()->intended(route('login'));
})->middleware('guest');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    /*
    |--------------------------------------------------------------------------
    | Menu Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/tab/{tab}', [ProfileController::class, 'setTabActive']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

    Route::get('/profile/verifikasi-nomor/{user:id}', [VerifikasiController::class, 'verifikasiNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/send-code/{user:id}', [VerifikasiController::class, 'sendCodeNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/resend-code/{user:id}', [VerifikasiController::class, 'resendCodeNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/verified/{user:id}', [VerifikasiController::class, 'verifiedNomor']);
    Route::get('/profile/ajax/verifikasi-nomor/is-verified/{user:id}', [VerifikasiController::class, 'isVerifiedNomor']);

    Route::get('/profile/verifikasi-email/{user:id}', [VerifikasiController::class, 'verifikasiEmail']);
    Route::get('/profile/ajax/verifikasi-email/send-code/{user:id}', [VerifikasiController::class, 'sendCodeEmail']);
    Route::get('/profile/ajax/verifikasi-email/resend-code/{user:id}', [VerifikasiController::class, 'resendCodeEmail']);
    Route::get('/profile/ajax/verifikasi-email/verified/{user:id}', [VerifikasiController::class, 'verifiedEmail']);
    Route::get('/profile/ajax/verifikasi-email/is-verified/{user:id}', [VerifikasiController::class, 'isVerifiedEmail']);


    /*
    |--------------------------------------------------------------------------
    | Menu Setting Master Data
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [SettingController::class, 'settings']);
    Route::get('/settings/tab/{tab}', [SettingController::class, 'setTabActive']);


    Route::get('/settings/ajax/datatable/holidays/{year}', [HolidayController::class, 'datatableHolidays']);
    Route::get('/settings/holidays/tambah', [HolidayController::class, 'tambah']);
    Route::post('/settings/holidays/store', [HolidayController::class, 'store']);
    Route::get('/settings/holidays/delete/{id}', [HolidayController::class, 'delete']);
    Route::get('/settings/holidays/{id}', [HolidayController::class, 'getHoliday']);
    Route::post('/settings/holidays/edit', [HolidayController::class, 'edit']);


    Route::get('/settings/ajax/datatable/shift', [ShiftController::class, 'datatableShift']);
    Route::get('/settings/shift/tambah', [ShiftController::class, 'tambah']);
    Route::get('/settings/shift/{shift:id}', [ShiftController::class, 'getShift']);
    Route::post('/settings/shift/store', [ShiftController::class, 'store']);
    Route::post('/settings/shift/edit', [ShiftController::class, 'edit']);
    Route::get('/settings/shift/delete/{id}', [ShiftController::class, 'delete']);
    Route::get('/settings/shift/get/active', [ShiftController::class, 'getActiveShift']);

    Route::get('/settings/ajax/datatable/schedule', [ScheduleController::class, 'datatableSchedule']);
    Route::get('/settings/schedule/tambah', [ScheduleController::class, 'tambah']);
    Route::get('/settings/schedule/{schedule:id}', [ScheduleController::class, 'getSchedule']);
    Route::post('/settings/schedule/store', [ScheduleController::class, 'store']);
    Route::post('/settings/schedule/edit', [ScheduleController::class, 'edit']);
    Route::get('/settings/schedule/delete/{id}', [ScheduleController::class, 'delete']);

    Route::get('/settings/ajax/datatable/pic', [PicController::class, 'datatablePic']);
    Route::get('/settings/ajax/datatable/employees-pic', [PicController::class, 'datatableEmployeesPic']);
    Route::get('/settings/pic/tambah', [PicController::class, 'tambah']);
    Route::get('/settings/pic/{pic:id}', [PicController::class, 'getPic']);
    Route::post('/settings/pic/store', [PicController::class, 'store']);
    Route::post('/settings/pic/edit', [PicController::class, 'edit']);
    Route::get('/settings/pic/delete/{id}', [PicController::class, 'delete']);
    Route::get('/settings/pic/ajax/show/{user:id}', [PicController::class, 'showPic']);
    Route::get('/settings/pic/ajax/edit/{pic_id}', [PicController::class, 'editAjax']);

    Route::get('/settings/ajax/jadwal/info', [DasarJadwalController::class, 'getScheduleInfo']);
    Route::get('/settings/ajax/jadwal/statistics', [DasarJadwalController::class, 'statistics']);
    Route::get('/settings/ajax/datatable/dasar-jadwal/all-employee', [DasarJadwalController::class, 'datatableDasarJadwalAllEmployee']);
    Route::get('/settings/ajax/datatable/dasar-jadwal/add-employee/{schedule_id}', [DasarJadwalController::class, 'datatableDasarJadwaAddEmployee']);
    Route::get('/settings/ajax/datatable/dasar-jadwal/view-employee/{schedule_id}', [DasarJadwalController::class, 'datatableDasarJadwaViewEmployee']);
    Route::get('/settings/ajax/dasar-jadwal/filter-employee/{schedule_id}', [DasarJadwalController::class, 'getFilterEmployee']);
    Route::get('/settings/dasar-jadwal/tambah', [DasarJadwalController::class, 'tambah']);
    Route::get('/settings/dasar-jadwal/{dasar-jadwal:id}', [DasarJadwalController::class, 'getDasarJadwal']);
    Route::post('/settings/dasar-jadwal/store', [DasarJadwalController::class, 'store']);
    Route::post('/settings/dasar-jadwal/edit', [DasarJadwalController::class, 'edit']);
    Route::get('/settings/dasar-jadwal/delete/{id}', [DasarJadwalController::class, 'delete']);
    Route::post('/settings/ajax/dasar-jadwal/add-employees', [DasarJadwalController::class, 'addEmployees']);
    Route::get('/settings/ajax/dasar-jadwal/employee/{employee_id}', [DasarJadwalController::class, 'getEmployeeDasarJadwal']);

    /*
    |--------------------------------------------------------------------------
    | Menu Employee
    |--------------------------------------------------------------------------
    */

    Route::get('/employee', [EmployeeController::class, 'employee']);
    Route::get('/employee/info', [EmployeeController::class, 'getInfoEmployee']);
    Route::get('/employee/ajax/datatable/all', [EmployeeController::class, 'datatableEmployee']);
    Route::get('/employee/ajax/datatable/need-update', [EmployeeController::class, 'datatableEmployeeNeedUpdate']);
    Route::get('/employee/ajax/datatable/ftm-all', [EmployeeController::class, 'datatableFtmAll']);
    Route::get('/employee/need-update', [EmployeeController::class, 'needUpdate']);
    Route::get('/employee/ftm/{emp:emp_id_auto}', [EmployeeController::class, 'getEmployeeFtm']);
    Route::post('/employee/ftm/edit/{emp:emp_id_auto}', [EmployeeController::class, 'editEmployeeFtm']);

    Route::get('/employee/need-update/{employee:id}', [EmployeeController::class, 'getEmployeeNeedUpdate']);
    Route::post('/employee/need-update/edit/{employee:id}', [EmployeeController::class, 'editEmployeeNeedUpdate']);
    Route::get('/employee/synchronize', [EmployeeController::class, 'synchronizeEmployees']);

    /*
    |--------------------------------------------------------------------------
    | Menu Report
    |--------------------------------------------------------------------------
    */

    Route::get('/report', [ReportController::class, 'report']);
    Route::get('/report/tab/{tab}', [ReportController::class, 'setTabActive']);
    Route::get('/report/ajax/get-periode', [Controller::class, 'getPeriodeReport']);
    Route::post('/report/ajax/set-periode', [Controller::class, 'setPeriodeReport']);


    Route::get('/report/ajax/datatable/scan-log/{start_date}/{end_date}', [ReportController::class, 'datatableScanLog']);
    Route::post('/report/ajax/datatable/new', [ReportController::class, 'datatableReportNew']);
    Route::get('/report/generate/scan-log', [ReportController::class, 'generateScanLog']);

    Route::get('/report/generate/employee', [ReportController::class, 'generateReport']);
    Route::get('/report/generate/single-employee/{id}', [ReportController::class, 'generateReportEmployee']);
    Route::get('/report/progress-generate', [ReportController::class, 'getProgressReport']);

    Route::get('/report/ajax/datatable/list-unit', [ReportController::class, 'datatableListUnit']);
    Route::get('/report/ajax/datatable/list-employee', [ReportController::class, 'dataTableListEmployee']);


    Route::get('/report/employee/{employee:id}', [ReportController::class, 'reportEmployee']);
    Route::get('/report/ajax/datatable/report-employee/{employee_id}', [ReportController::class, 'dataTableReportEmployee']);
    Route::get('/report/ajax/get-holidays/{start_date}/{end_date}', [ReportController::class, 'getHolidaysInPeriod']);


    /*
    |--------------------------------------------------------------------------
    | API Report
    |--------------------------------------------------------------------------
    */

    Route::get('/api/v1/report/get-report/{report:id}', [ApiReportController::class, 'getReport']);
    Route::get('/api/v1/report/report-by-date/{date}/{employee:id}', [ApiReportController::class, 'getReportByDate']);
    Route::get('/api/v1/report/print/employee/{employee:id}', [ApiReportController::class, 'printEmployeeReport']);
    Route::get('/api/v1/report/print/unit/{unit:id}', [ApiReportController::class, 'printUnitReport']);
    Route::get('/api/v1/report/print/pic/{picID}', [ApiReportController::class, 'printPICReport']);

    Route::get('/api/v1/report/get-verifikasi/{verifikasi:id}', [ApiReportController::class, 'getVerifikasi']);
    Route::get('/api/v1/report/delete-verifikasi/{verifikasi:id}', [ApiReportController::class, 'deleteVerifikasi']);
    Route::post('/api/v1/report/perizinan/verifikasi', [ApiReportController::class, 'storeVerifikasi']);

    Route::post('/api/v1/report/perizinan/cuti', [ApiReportController::class, 'storeCuti']);
    Route::get('/api/v1/report/get-cuti/{cuti:id}', [ApiReportController::class, 'getCuti']);
    Route::get('/api/v1/report/delete-cuti/{cuti:id}', [ApiReportController::class, 'deleteCuti']);

    Route::post('/api/v1/report/perizinan/lembur', [ApiReportController::class, 'storeLembur']);
    Route::post('/api/v1/report/perizinan/lembur-libur', [ApiReportController::class, 'storeLemburLibur']);
    Route::post('/api/v1/report/perizinan/confirm-lembur', [ApiReportController::class, 'confirmLembur']);
    Route::get('/api/v1/report/get-lembur/{lembur:id}', [ApiReportController::class, 'getLembur']);
    Route::get('/api/v1/report/delete-lembur/{lembur:id}', [ApiReportController::class, 'deleteLembur']);

    Route::post('/api/v1/report/perizinan/izin', [ApiReportController::class, 'storeIzin']);
    Route::get('/api/v1/report/get-izin/{izin:id}', [ApiReportController::class, 'getIzin']);
    Route::get('/api/v1/report/delete-izin/{izin:id}', [ApiReportController::class, 'deleteIzin']);

    Route::get('/api/v1/report/get-attlog/{date}/{employee:id}', [ApiReportController::class, 'getAttlog']);
    Route::get('/api/v1/report/get-absen-stats/{employee:id}', [ApiReportController::class, 'getAttendanceStats']);

    Route::get('/api/v1/report/datatable/report-pic', [ApiReportController::class, 'datatableReportPic']);
    Route::get('/api/v1/report/generate/report-pic/{pic_id}', [ApiReportController::class, 'asyncGenerateReportEmployeesPic']);
    Route::get('/api/v1/report/progress/generate-report-pic/{pic_id}', [ApiReportController::class, 'getProgressGenerateReportPic']);


    /*
    |--------------------------------------------------------------------------
    | Lampiran
    |--------------------------------------------------------------------------
    */

    Route::get('/lampiran/{kategori}/{lampiran}', [LampiranController::class, 'showDokumen']);


    /*
    |--------------------------------------------------------------------------
    | Sandbox
    |--------------------------------------------------------------------------
    */

    Route::get('/sandbox', [SanboxController::class, 'sandbox']);
});
