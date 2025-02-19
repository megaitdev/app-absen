@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex bd-highlight mb-3">
                <h1>{{ $title }}</h1>
                <button class="btn btn-primary ml-3" id="generate-report">Generate Report</button>
                <div class="btn btn-outline-dark ml-auto" id="filter-periode">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $periode->name }}
                </div>
            </div>

            <div class="section-body">
                {{-- tab --}}
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $report_tab == 'scan-log' ? 'active' : '' }}"
                                    id="scan-log-tab" data-toggle="tab" href="#scan-log">
                                    Scan Log
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $report_tab == 'unit' ? 'active' : '' }}" id="unit-tab"
                                    data-toggle="tab" href="#unit">
                                    Report Unit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $report_tab == 'employee' ? 'active' : '' }}"
                                    id="employee-tab" data-toggle="tab" href="#employee">
                                    Report Karyawan
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- tab content --}}
                <div class="tab-content">
                    <div class="tab-pane fade {{ $report_tab == 'scan-log' ? 'show active' : '' }}" id="scan-log"
                        role="tabpanel">
                        @include('report.tab-scan-log')
                    </div>
                    <div class="tab-pane fade {{ $report_tab == 'unit' ? 'show active' : '' }}" id="unit"
                        role="tabpanel">
                        @include('report.tab-unit')
                    </div>
                    <div class="tab-pane fade {{ $report_tab == 'employee' ? 'show active' : '' }}" id="employee"
                        role="tabpanel">
                        @include('report.tab-employee')
                    </div>
                </div>

                {{-- modal --}}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="modal fade" id="modal-progress-generate-report" data-backdrop="static"
                            data-keyboard="false">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 class="text-dark title-progress">Generate Report</h2>
                                    </div>
                                    <div class="modal-body">
                                        <div class="progress mb-3" data-height="32">
                                            <div class="progress-bar progress-bar-striped bg-dark progress-generate"
                                                role="progressbar" data-width="0"></div>
                                        </div>
                                        <div class="text-center zero-state">
                                            <div class="spinner-grow spinner-grow-sm" role="status">
                                                <span class="visually-hidden"></span>
                                            </div>
                                        </div>
                                        <p class="text-center state-progress" hidden><b class="step">10</b> out of <b
                                                class="total">100</b>
                                            employees
                                            processed.</p>

                                        <div class="form-group">
                                            <button type="button" class="btn btn-lg btn-primary btn-block"
                                                data-dismiss="modal" hidden>Close</button>
                                        </div>
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
