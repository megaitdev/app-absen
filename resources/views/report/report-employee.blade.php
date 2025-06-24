@extends('layouts.app')

@section('title', 'Blank Page')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex bd-highlight mb-3">
                <div class="section-header-back">
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }} </h1>
                <div class="section-header-button ml-auto">
                    <a href="{{ url('api/v1/report/print/employee/' . $employee->id) }}" target="_blank"
                        class="btn btn-block btn-outline-primary">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Header Actions -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="btn btn-sm btn-primary ml-1" id="filter-periode">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ $periode->name }}
                                </div>
                            </div>
                            <div class="breadcrumb">
                                <div class="breadcrumb-item active"><a href="{{ url('report') }}">Report</a></div>
                                <div class="breadcrumb-item">{{ $title }}</div>
                            </div>
                        </div>

                        <!-- Employee Info -->
                        <div class="card mb-4 card-shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="text-muted mb-1">Nama:</p>
                                        <p class="font-weight-medium text-dark">{{ $employee->nama }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex">
                                            <div class="mr-4">
                                                <p class="text-muted mb-1">PIN:</p>
                                                <p class="font-weight-medium text-dark">{{ $employee->pin }}</p>
                                            </div>
                                            <div>
                                                <p class="text-muted mb-1">NIK:</p>
                                                <p class="font-weight-medium text-dark">{{ $employee->nip }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex">
                                            <div class="mr-4">
                                                <p class="text-muted mb-1">Unit:</p>
                                                <p class="font-weight-medium text-dark">{{ $employee->unit->unit }}</p>
                                            </div>
                                            <div>
                                                <p class="text-muted mb-1">Jabatan:</p>
                                                <p class="font-weight-medium text-dark">{{ $employee->pangkat->pangkat }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Summary with Collapse/Expand feature -->
                        <div class="card mb-4 shadow-sm" id="attendance-summary-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <h6 class="text-primary my-auto" id="attendance-title">Ringkasan Kehadiran |
                                        {{ $periode->name }} : {{ $periode->start }} ➡️ {{ $periode->end }}</h6>
                                    <button class="btn btn-sm btn-dark" id="toggle-summary" data-toggle="collapse"
                                        data-target="#attendance-content">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                </div>

                                <div id="attendance-content" class="collapse mt-3">
                                    <div class="row g-3">
                                        <!-- Attendance Statistics -->
                                        <div id="attendance-summary" class="col-md-3">
                                            <div class="card h-100 bg-light-blue border-0">
                                                <div class="card-body">
                                                    <h3 class="card-title h6 font-weight-bolder mb-3">Statistik Kehadiran
                                                    </h3>
                                                    <div id="attendance-percentage"
                                                        class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Persentase Kehadiran:</span>
                                                        <span class="font-weight-bolder">
                                                            <i class="fas fa-spinner fa-spin"></i> -
                                                        </span>
                                                    </div>
                                                    <div id="attendance-day" class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Hari Hadir:</span>
                                                        <span class="font-weight-bolder">
                                                            <i class="fas fa-spinner fa-spin"></i> - dari - hari
                                                        </span>
                                                    </div>
                                                    <div id="attendance-total-hour" class="d-flex justify-content-between">
                                                        <span class="text-muted">Total Jam Hadir:</span>
                                                        <span class="font-weight-bolder">
                                                            <i class="fas fa-spinner fa-spin"></i> - jam
                                                        </span>
                                                    </div>
                                                    <div id="available-hour" class="d-flex justify-content-between">
                                                        <span class="text-muted">Total Jam Tersedia:</span>
                                                        <span class="font-weight-bolder">
                                                            <i class="fas fa-spinner fa-spin"></i> - hour
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Allowance Summary -->
                                        <div id="allowance-summary" class="col-md-3">
                                            <div class="card h-100 bg-light-green">
                                                <div class="card-body">
                                                    <h3 class="card-title h6 font-weight-bolder mb-3">Statistik Kehadiran
                                                    </h3>
                                                    <div class="mb-3" id="regular-allowance">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Tunjangan Reguler:</span>
                                                        </div>
                                                        <div class="pl-3 small">
                                                            <div class="d-flex justify-content-between mb-1"
                                                                id="regular-allowance-meal">
                                                                <span class="text-muted">- Makan:</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1"
                                                                id="regular-allowance-transport">
                                                                <span class="text-muted">- Transport:</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between"
                                                                id="regular-allowance-diligence">
                                                                <span class="text-muted">- Kerajinan:</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="overtime-allowance">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Tunjangan Lembur:</span>
                                                        </div>
                                                        <div class="pl-3 small">
                                                            <div class="d-flex justify-content-between mb-1"
                                                                id="overtime-allowance-meal">
                                                                <span class="text-muted">- Makan (Terusan):</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1"
                                                                id="overtime-allowance-transport">
                                                                <span class="text-muted">- Transport:</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between"
                                                                id="overtime-allowance-meal-overtime">
                                                                <span class="text-muted">- Makan (Libur):</span>
                                                                <span class="font-weight-bolder">-</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Overtime -->
                                        <div id="overtime-summary" class="col-md-3">
                                            <div class="card h-100 bg-light-purple border-0">
                                                <div class="card-body">
                                                    <h3 class="card-title h6 font-weight-bolder mb-3">Lembur</h3>
                                                    <div class="d-flex justify-content-between mb-2"
                                                        id="overtime-persentase">
                                                        <span class="text-muted">Persentase Lembur:</span>
                                                        <span class="font-weight-bolder">25%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2"
                                                        id="overtime-jumlah-hari">
                                                        <span class="text-muted">Hari Lembur:</span>
                                                        <span class="font-weight-bolder">2 hari</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2"
                                                        id="overtime-total-jam">
                                                        <span class="text-muted">Total Jam Lembur:</span>
                                                        <span class="font-weight-bolder">6.00 jam</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between"
                                                        id="overtime-total-jam-akumulasi">
                                                        <span class="text-muted">Total Jam Lembur Akumulasi:</span>
                                                        <span class="font-weight-bolder">12.00 jam</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Deductions -->
                                        <div id="deductions-summary" class="col-md-3">
                                            <div class="card h-100 bg-light-red border-0">
                                                <div class="card-body">
                                                    <h3 class="card-title h6 font-weight-bolder mb-3">Perizinan</h3>
                                                    <div class="d-flex justify-content-between mb-2" id="total-leave">
                                                        <span class="text-muted">Total Cuti:</span>
                                                        <span class="font-weight-bolder">-</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2" id="total-permit">
                                                        <span class="text-muted">Total Izin:</span>
                                                        <span class="font-weight-bolder">-</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2" id="verification">
                                                        <span class="text-muted">Verifikasi:</span>
                                                        <span class="font-weight-bolder">-</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2" id="lost-hours">
                                                        <span class="text-muted">Jam Hilang:</span>
                                                        <span class="font-weight-bolder">6.15 jam</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between" id="deductions">
                                                        <span class="text-muted">Potongan:</span>
                                                        <span class="font-weight-bolder">Rp 307.500</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Footer -->
                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <span id="periode-summary" class="text-muted">Periode: Januari 2025</span>
                                        <div class="text-end">
                                            <p class="text-muted mb-1">Total Penerimaan Bersih:</p>
                                            <p class="fw-bold text-primary h5 mb-0">Rp -</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Employee -->
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <p id="employee_id" hidden>{{ $employee->id }}</p>
                                    <table class="table table-bordered table-sm table-report">
                                        <thead>
                                            <tr class="bg-dark" style="color: white !important">
                                                <th>Tanggal</th>
                                                <th>Shift</th>
                                                <th>Jam Masuk</th>
                                                <th>Scan Masuk</th>
                                                <th>Jam Keluar</th>
                                                <th>Scan Keluar</th>
                                                <th>Durasi Murni</th>
                                                <th>Durasi Efektif</th>
                                                <th>Jam Hilang Murni</th>
                                                <th>Jam Hilang Efektif</th>
                                                <th>Lembur Murni</th>
                                                <th>Lembur Efektif</th>
                                                <th>Potongan</th>
                                                <th>Tunjangan</th>
                                                <th>Perizinan</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modal-perizinan" tabindex="-1" role="dialog"
                    aria-labelledby="modal-perizinan" aria-hidden="true" data-backdrop="static" data-keyboard="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="text-primary">Perizinan</h4>
                            </div>
                            <div class="modal-body pb-0">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" id="cuti-tab" data-toggle="tab" href="#cuti"
                                            role="tab" aria-controls="cuti" aria-selected="true">Cuti</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="izin-tab" data-toggle="tab" href="#izin"
                                            role="tab" aria-controls="izin" aria-selected="false">Izin</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="lembur-tab" data-toggle="tab" href="#lembur"
                                            role="tab" aria-controls="lembur" aria-selected="false">Lembur</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="verifikasi-tab" data-toggle="tab" href="#verifikasi"
                                            role="tab" aria-controls="verifikasi"
                                            aria-selected="false">Verifikasi</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="default-tab" data-toggle="tab" href="#default"
                                            role="tab" aria-controls="default" aria-selected="false">Perizinan</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade" id="cuti" role="tabpanel"
                                        aria-labelledby="cuti-tab">
                                        @include('report.perizinan.tab-cuti')
                                    </div>
                                    <div class="tab-pane fade" id="izin" role="tabpanel"
                                        aria-labelledby="izin-tab">
                                        @include('report.perizinan.tab-izin')
                                    </div>
                                    <div class="tab-pane fade show active" id="lembur" role="tabpanel"
                                        aria-labelledby="lembur-tab">
                                        @include('report.perizinan.tab-lembur')
                                    </div>
                                    <div class="tab-pane fade" id="verifikasi" role="tabpanel"
                                        aria-labelledby="verifikasi-tab">
                                        @include('report.perizinan.tab-verifikasi')
                                    </div>
                                    <div class="tab-pane fade" id="default" role="tabpanel"
                                        aria-labelledby="default-tab">
                                        @include('report.perizinan.tab-default')
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
