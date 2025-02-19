@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
                <div class="section-header-button">
                    <div onclick="javascript:syncData()" class="btn btn-danger"><i class="fas fa-sync mr-2"></i>Sinkronasi Data
                    </div>
                </div>
            </div>


            <div class="section-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-dark">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Sinkron</h4>
                                </div>
                                <div class="card-body">
                                    <div class="spinner-grow spinner-grow-sm loading-info" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span id="sinkron-count"></span>
                                </div>
                                <div class="card-footer py-0 m-0 text-right text-time">
                                    <i class="fas fa-history mr-1"></i> {{ $last_update['latest'] }}
                                    {{-- <i class="fas fa-exclamation-circle text-warning pulsate"></i> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-dark">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Belum Sinkron</h4>
                                </div>
                                <div class="card-body">
                                    <div class="spinner-grow spinner-grow-sm loading-info" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span id="belum-sinkron-count"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-dark">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Need Update</h4>
                                </div>
                                <div class="card-body">
                                    <div class="spinner-grow spinner-grow-sm loading-info" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <span id="need-update-count"></span>
                                </div>
                                <div class="card-footer py-0 m-0 text-right text-time">
                                    <a href="{{ url('employee/need-update') }}">
                                        <span class="text-small">Show</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>List Karyawan</h4>
                                <div class="card-header-action">
                                    <div class="d-flex justify-content-end">
                                        <div class="dropdown mx-1">
                                            <a href="javascript:void(0)" data-toggle="dropdown"
                                                class="btn btn-outline-dark dropdown-toggle length-info">
                                                <i class="fas fa-layer-group mr-2"></i>10
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <li class="dropdown-title">Select Length</li>
                                                <li>
                                                    <a href="javascript:changeLength(5)"
                                                        class="dropdown-item dropdown-item-dark di-5">5</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLength(10)"
                                                        class="dropdown-item dropdown-item-dark di-10 active">10</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLength(25)"
                                                        class="dropdown-item dropdown-item-dark di-25">25</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLength(50)"
                                                        class="dropdown-item dropdown-item-dark di-50">50</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLength(100)"
                                                        class="dropdown-item dropdown-item-dark di-100">100</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="search-container mx-1">
                                            <input type="text" class="search-input-dark" id="search-employee"
                                                placeholder="Search">
                                            <button class="search-button">
                                                <svg class="search-icon-dark" viewBox="0 0 24 24">
                                                    <path
                                                        d="M21.71 20.29l-5.01-5.01C17.54 13.68 18 11.91 18 10c0-4.41-3.59-8-8-8S2 5.59 2 10s3.59 8 8 8c1.91 0 3.68-0.46 5.28-1.3l5.01 5.01c0.39 0.39 1.02 0.39 1.41 0C22.1 21.32 22.1 20.68 21.71 20.29zM10 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6S13.31 16 10 16z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-light table-bordered table-employee">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th>#</th>
                                                <th>Nama Karyawan</th>
                                                <th>NIK</th>
                                                <th>PIN</th>
                                                <th>Divisi</th>
                                                <th>Unit</th>
                                                <th>Action</th>
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
