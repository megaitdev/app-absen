@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ url('employee') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('employee') }}">Employee</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5 pr-0">
                        <div class="card mb-2">
                            <div class="card-body p-0">
                                <div class="btn btn-primary h-100 w-100">Add to FTM</div>
                            </div>
                        </div>
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Database HRD</h4>
                                <div class="card-header-action">
                                    <div class="d-flex justify-content-end">
                                        <div class="dropdown mx-1">
                                            <a href="javascript:void(0)" data-toggle="dropdown"
                                                class="btn btn-outline-dark dropdown-toggle length-info-need-update">
                                                <i class="fas fa-layer-group mr-2"></i>10
                                            </a>
                                            <ul
                                                class="dropdown-menu dropdown-menu-sm dropdown-menu-right length-list-need-update">
                                                <li class="dropdown-title">Select Length</li>
                                                <li>
                                                    <a href="javascript:changeLengthNeedUpdate(5)"
                                                        class="dropdown-item dropdown-item-dark di-need-update-5">5</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthNeedUpdate(10)"
                                                        class="dropdown-item dropdown-item-dark di-need-update-10 active">10</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthNeedUpdate(25)"
                                                        class="dropdown-item dropdown-item-dark di-need-update-25">25</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthNeedUpdate(50)"
                                                        class="dropdown-item dropdown-item-dark di-need-update-50">50</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthNeedUpdate(100)"
                                                        class="dropdown-item dropdown-item-dark di-need-update-100">100</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="search-container mx-1">
                                            <input type="text" class="search-input-dark" id="search-employee-need-update"
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
                                    <table class="table table-sm table-light table-bordered table-employee-need-update">
                                        <thead style="height: 78px">
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
                    <div class="col-lg-7 pl-1">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Database FTM</h4>
                                <div class="card-header-action">
                                    <div class="d-flex justify-content-end">
                                        <div class="dropdown mx-1">
                                            <a href="javascript:void(0)" data-toggle="dropdown"
                                                class="btn btn-outline-dark dropdown-toggle length-info-ftm">
                                                <i class="fas fa-layer-group mr-2"></i>10
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right length-list-ftm">
                                                <li class="dropdown-title">Select Length</li>
                                                <li>
                                                    <a href="javascript:changeLengthFtm(5)"
                                                        class="dropdown-item dropdown-item-dark di-ftm-5">5</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthFtm(10)"
                                                        class="dropdown-item dropdown-item-dark di-ftm-10 active">10</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthFtm(25)"
                                                        class="dropdown-item dropdown-item-dark di-ftm-25">25</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthFtm(50)"
                                                        class="dropdown-item dropdown-item-dark di-ftm-50">50</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:changeLengthFtm(100)"
                                                        class="dropdown-item dropdown-item-dark di-ftm-100">100</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="search-container mx-1">
                                            <input type="text" class="search-input-dark" id="search-employee-ftm"
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
                                    <table class="table table-sm table-light table-bordered table-employee-ftm">
                                        <thead style="height: 78px">
                                            <tr class="bg-dark text-white">
                                                <th>#</th>
                                                <th>Nama Karyawan</th>
                                                <th>NIK</th>
                                                <th>PIN</th>
                                                <th>Cabang</th>
                                                <th>Departemen</th>
                                                <th>Sync</th>
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

                <div class="row">
                    <div class="col-lg-12">
                        @if (session('success-ftm'))
                            <input type="text" id="alert-success-ftm" value="1" hidden></input>
                        @endif
                        @if ($errors->any(['alias', 'cabang', 'departemen', 'pin_ftm', 'nik']))
                            <input type="text" id="alert-modal-edit-employee-ftm" value="{{ old('emp_id_auto') }}"
                                hidden>
                            </input>
                        @endif
                        {{-- Modal Edit Employee --}}
                        <div class="modal fade" id="modal-edit-employee-ftm">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="text-dark">Edit Employee FTM</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('employee/ftm/edit') }}" method="post">
                                            @csrf
                                            <input id="edit-id-employee-ftm" type="text" name="emp_id_auto" hidden>
                                            <div class="form-group">
                                                <label for="edit-alias-employee-ftm">Alias</label>
                                                <input type="text"
                                                    class="form-control @error('alias') is-invalid @enderror"
                                                    id="edit-alias-employee-ftm" name="alias"
                                                    value="{{ old('alias') }}" required>
                                                @error('alias')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="edit-nik-employee-ftm">NIK</label>
                                                    <input type="text"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        id="edit-nik-employee-ftm" name="nik"
                                                        value="{{ old('nik') }}">
                                                    @error('nik')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="edit-pin-employee-ftm">PIN</label>
                                                    <input type="text"
                                                        class="form-control @error('pin_ftm') is-invalid @enderror"
                                                        id="edit-pin-employee-ftm" name="pin_ftm"
                                                        value="{{ old('pin_ftm') }}">
                                                    @error('pin_ftm')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="edit-cabang-employee-ftm">Cabang</label>
                                                    <select
                                                        class="form-control select2 @error('cabang') is-invalid @enderror"
                                                        id="edit-cabang-employee-ftm" name="cabang" required>
                                                        @foreach ($form_ftm['cabang'] as $cabang)
                                                            <option value="{{ $cabang->cab_id_auto }}">
                                                                {{ $cabang->cab_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('cabang')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="edit-departemen-employee-ftm">Departemen</label>
                                                    <select
                                                        class="form-control select2 @error('departemen') is-invalid @enderror"
                                                        id="edit-departemen-employee-ftm" name="departemen" required>
                                                        @foreach ($form_ftm['departemen'] as $departemen)
                                                            <option value="{{ $departemen->dept_id_auto }}">
                                                                {{ $departemen->dept_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('departemen')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="button" class="btn btn-light"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        @if (session('success-need-update'))
                            <input type="text" id="alert-success-need-update" value="1" hidden></input>
                        @endif
                        @if ($errors->any(['nama', 'pin_need_update', 'nip']))
                            <input type="text" id="alert-modal-edit-employee-need-update" value="{{ old('id') }}"
                                hidden>
                            </input>
                        @endif
                        <!-- Modal Edit Employee Need Update-->
                        <div class="modal fade" id="modal-edit-employee-need-update">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="text-dark">Edit Employee Need Update</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ url('employee/need-update/edit') }}" method="post">
                                            @csrf
                                            <input id="edit-id-employee-need-update" type="text" name="id"
                                                hidden>
                                            <div class="form-group">
                                                <label for="edit-nama-employee-need-update">Nama Karyawan</label>
                                                <input type="text"
                                                    class="form-control @error('nama') is-invalid @enderror"
                                                    id="edit-nama-employee-need-update" name="nama"
                                                    value="{{ old('nama') }}">
                                                @error('nama')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="edit-nip-employee-need-update">NIK</label>
                                                    <input type="text"
                                                        class="form-control @error('nip') is-invalid @enderror"
                                                        id="edit-nip-employee-need-update" name="nip"
                                                        value="{{ old('nip') }}">
                                                    @error('nip')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="edit-pin-employee-need-update">PIN</label>
                                                    <input type="text"
                                                        class="form-control @error('pin') is-invalid @enderror"
                                                        id="edit-pin-employee-need-update" name="pin_need_update"
                                                        value="{{ old('pin_need_update"') }}">
                                                    @error('pin_need_update"')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="edit-divisi-employee-need-update">Divisi</label>
                                                    <select
                                                        class="form-control select2 @error('divisi') is-invalid @enderror"
                                                        id="edit-divisi-employee-need-update" name="divisi">
                                                        @foreach ($form_need_update['divisi'] as $divisi)
                                                            <option value="{{ $divisi->id }}">
                                                                {{ $divisi->divisi }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('divisi')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="edit-unit-employee-need-update">Unit</label>
                                                    <select
                                                        class="form-control select2 @error('unit') is-invalid @enderror"
                                                        id="edit-unit-employee-need-update" name="unit">
                                                        @foreach ($form_need_update['unit'] as $unit)
                                                            <option value="{{ $unit->id }}">
                                                                {{ $unit->unit }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('unit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="button" class="btn btn-light"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                            </div>
                                        </form>
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
