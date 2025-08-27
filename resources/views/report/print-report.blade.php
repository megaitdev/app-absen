@extends('layouts.app')


@push('style')
    <!-- CSS Libraries -->
    <style>
        /* Scope Select2 overrides to report controls only */
        .report-controls .select2-container--default .select2-selection--multiple {
            border-color: #007bff;
            min-height: 38px;
        }

        .report-controls .select2-container--default .select2-selection--single {
            border-color: #007bff;
            min-height: 38px;
        }

        .report-controls .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .report-controls .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.9);
        }

        .report-controls .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #007bff;
            color: #fff;
        }

        /* Disabled state keeps default grey to signal non-interactive */
        .report-controls .select2-container--default.select2-container--disabled .select2-selection--multiple,
        .report-controls .select2-container--default.select2-container--disabled .select2-selection--single {
            border-color: #ced4da;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex bd-highlight mb-3">
                <div class="section-header-back">
                    <a href="{{ url('report') }}" class="btn btn-icon mr-2"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
                <input type="hidden" id="pic_id" value="{{ auth()->user()->id }}" />
                <div class="btn btn-outline-dark ml-auto" id="filter-periode">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $periode->name }}
                </div>
            </div>

            <div class="section-body">

                <div class="row">
                    <div class="col-lg-12">
                        <!-- Report Filters (not included in PDF capture) -->
                        <div class="report-controls card mb-3 p-3">
                            <div class="d-flex flex-wrap align-items-center mt-2">
                                <div class="mr-3 mb-2" style="min-width:260px;">
                                    <label class="mb-1" for="filterUnitSelect">Pilih Unit</label>
                                    <select id="filterUnitSelect" class="form-control select2" multiple
                                        data-placeholder="Pilih Unit">
                                    </select>
                                </div>
                                <div class="mr-3 mb-2" style="min-width:360px;">
                                    <label class="mb-1" for="filterEmployeeSelect">Pilih Karyawan</label>
                                    <select id="filterEmployeeSelect" class="form-control" multiple
                                        data-placeholder="Pilih Karyawan">
                                    </select>
                                </div>
                                <div class="mb-2 mt-4">
                                    <button id="apply-report-filter" class="btn btn-sm btn-primary mr-2">Terapkan</button>
                                    <button id="reset-report-filter" class="btn btn-sm btn-secondary">Reset</button>
                                </div>
                                <button onclick="printReport()" class="btn btn-block btn-primary">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">Catatan: Filter hanya mempengaruhi baris yang dicetak ke
                                PDF.</small>
                        </div>
                        <div id="capture" class="report-container report-print" style="min-height: 1240px;">
                            <div class="header">
                                <h1>LAPORAN ABSEN KARYAWAN</h1>
                            </div>

                            <div class="info-tables">
                                <table class="info-table info-table-left">
                                    <tbody>
                                        <tr>
                                            <td class="label">UNIT</td>
                                            <td id="info-unit">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">PANGKAT</td>
                                            <td id="info-pangkat">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">PERIODE</td>
                                            <td id="info-periode">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">GAJI</td>
                                            <td id="info-gaji">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="info-table info-table-right">
                                    <tbody>
                                        <tr>
                                            <td class="label">PIC</td>
                                            <td id="info-pic">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">DICETAK</td>
                                            <td id="info-dicetak">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">TOTAL</td>
                                            <td id="info-mengelola">
                                                <div class="spinner-grow spinner-grow-sm" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="report-table-wrap">
                                <table class="report-table">
                                    <thead class="table-header-group">
                                        <tr>
                                            <th rowspan="2" style="width:30px">NO</th>
                                            <th rowspan="2" style="width:95px">NIK</th>
                                            <th rowspan="2" style="width:170px">NAMA</th>
                                            <th colspan="3" class="col-title">REGULER</th>
                                            <th rowspan="2" style="width:110px">Pot. Absen (Jam)</th>
                                            <th colspan="4" class="col-title">LEMBUR</th>
                                        </tr>
                                        <tr>
                                            <th style="width:30px">UT</th>
                                            <th style="width:30px">UM</th>
                                            <th style="width:30px">UK</th>
                                            <th style="width:30px">UTL</th>
                                            <th style="width:30px">UML</th>
                                            <th style="width:30px">UMLL</th>
                                            <th style="width:30px">JAM LEMBUR EFEKTIF AKUMULASI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="report-tbody">
                                        <tr id="table-loading">
                                            <td colspan="12" class="text-center">
                                                <div class="d-flex align-items-center justify-content-center p-3">
                                                    <div class="spinner-border mr-3" role="status">
                                                    </div>
                                                    <span>Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="table-empty" style="display: none;">
                                            <td colspan="12" class="text-center ">
                                                <div class="d-flex align-items-center justify-content-center p-3">
                                                    <span>Data Tidak Ditemukan...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
