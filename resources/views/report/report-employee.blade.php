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
                <div class="btn btn-outline-dark ml-auto" id="filter-periode">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $periode->name }}
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('report') }}">Report</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-dark">
                            <div class="row px-3 pt-3">
                                <div class="col-lg-4">
                                    <h6 class="text-muted">Nama :</h6>
                                    <h6 class="text-dark">{{ $employee->nama }}</h6>
                                    <p id="employee_id" hidden>{{ $employee->id }}</p>
                                </div>
                                <div class="col-lg-4">
                                    <span class="badge badge-light mb-2">PIN : {{ $employee->pin }}</span>
                                    <span class="badge badge-light mb-2">NIK : {{ $employee->nip }}</span>
                                    <h6 class="text-dark">Unit : {{ $employee->unit->unit }}</h6>
                                </div>
                                <div class="col-lg-3">
                                    <h6 class="text-muted">Jabatan :</h6>
                                    <h6 class="text-dark">{{ preg_replace('/\d/', '', $employee->pangkat->pangkat) }}</h6>
                                </div>
                                <div class="col-lg-1 p-2">
                                    <button class="btn btn-block btn-outline-danger" onclick="window.print()">
                                        <i class="fas fa-print"></i> Print</button>
                                </div>
                            </div>
                            <div class="card-body mt-2 p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm table-report">
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
                                                <th>Verifikasi Absen</th>
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

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
