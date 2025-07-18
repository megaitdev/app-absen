@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex bd-highlight mb-3">
                <div class="section-header-back">
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
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

                        <!-- Attendance Summary -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h2 class="h5 font-weight-bold mb-3">Ringkasan Kehadiran</h2>

                                <div class="row g-3">
                                    <!-- Attendance Statistics -->
                                    <div id="attendance-summary" class="col-md-3">
                                        <div class="card h-100 bg-light-blue border-0">
                                            <div class="card-body">
                                                <h3 class="card-title h6 font-weight-bolder mb-3">Statistik Kehadiran</h3>
                                                <div id="attendance-percentage" class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Persentase Kehadiran:</span>
                                                    <span class="font-weight-bolder">71.43%</span>
                                                </div>
                                                <div id="attendance-day" class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Hari Hadir:</span>
                                                    <span class="font-weight-bolder">5 dari 7 hari</span>
                                                </div>
                                                <div id="attendance-total-hour" class="d-flex justify-content-between">
                                                    <span class="text-muted">Total Jam Kerja:</span>
                                                    <span class="font-weight-bolder">40 jam</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Financial Summary -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-light-green border-0">
                                            <div class="card-body">
                                                <h3 class="card-title h6 font-weight-bolder mb-3">Ringkasan Keuangan</h3>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Tunjangan Dasar:</span>
                                                    <span class="font-weight-bolder">Rp 1.500.000</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Tunjangan Transportasi:</span>
                                                    <span class="font-weight-bolder">Rp 300.000</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Tunjangan Makan:</span>
                                                    <span class="font-weight-bolder">Rp 500.000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Overtime -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-light-purple border-0">
                                            <div class="card-body">
                                                <h3 class="card-title h6 font-weight-bolder mb-3">Lembur</h3>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Jam Lembur Normal:</span>
                                                    <span class="font-weight-bolder">5.30 jam</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Jam Lembur Efektif:</span>
                                                    <span class="font-weight-bolder">4.80 jam</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Pembayaran Lembur:</span>
                                                    <span class="font-weight-bolder text-purple">Rp 360.000</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deductions -->
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-light-red border-0">
                                            <div class="card-body">
                                                <h3 class="card-title h6 font-weight-bolder mb-3">Potongan</h3>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Jam Hilang:</span>
                                                    <span class="font-weight-bolder">6.15 jam</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Potongan Jam Hilang:</span>
                                                    <span class="font-weight-bolder text-danger">Rp 307.500</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Tunjangan Setelah Potongan:</span>
                                                    <span class="font-weight-bolder">Rp 1.192.500</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Footer -->
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span class="text-muted">Periode: Januari 2025</span>
                                    <div class="text-end">
                                        <p class="text-muted mb-1">Total Penerimaan Bersih:</p>
                                        <p class="fw-bold h5 mb-0">Rp 2.352.500</p>
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

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
