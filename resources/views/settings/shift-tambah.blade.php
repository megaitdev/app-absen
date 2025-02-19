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
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('settings') }}">Settings</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Formulir Tambah Shift</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('settings/shift/store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Nama Shift</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}">

                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="jam_masuk">Jam Masuk</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_masuk') is-invalid @enderror timepicker"
                                                    id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_masuk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="jam_keluar">Jam Keluar</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_keluar') is-invalid @enderror timepicker"
                                                    id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_keluar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="custom-switch p-0">
                                            <input type="checkbox" name="is_sameday" id="is_sameday"
                                                class="custom-switch-input active" checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Shift Sameday adalah shift dimana berjalan pada hari yang sama atau tidak melebihi jam 12 malam">
                                                Shift Sameday
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="jam_mulai_istirahat">Jam Masuk Istirahat</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_mulai_istirahat') is-invalid @enderror timepicker"
                                                    id="jam_mulai_istirahat" name="jam_mulai_istirahat"
                                                    value="{{ old('jam_mulai_istirahat') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_mulai_istirahat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="jam_selesai_istirahat">Jam Keluar Istirahat</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_selesai_istirahat') is-invalid @enderror timepicker"
                                                    id="jam_selesai_istirahat" name="jam_selesai_istirahat"
                                                    value="{{ old('jam_selesai_istirahat') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_selesai_istirahat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="custom-switch p-0">
                                            <input type="checkbox" name="is_break" class="custom-switch-input active"
                                                id="is_break" onclick="handleIsBreak()" checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Jam Istirahat aktif maka shift memiliki jam istirahat">Jam
                                                Istirahat</span>
                                        </label>
                                    </div>

                                    <script></script>
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror h-100" id="keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-7">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Upload List Liburan</h4>
                                <div class="card-header-action">
                                    <a href="" class="btn btn-outline-dark pt-1 mx-1">
                                        <i class="fas fa-file-download"></i> Template
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">


                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
