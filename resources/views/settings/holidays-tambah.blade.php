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
                    <div class="breadcrumb-item">Holidays</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Formulir Tambah Liburan</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('settings/holidays/store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="date">Tanggal</label>
                                        <input type="text" class="form-control @error('date') is-invalid @enderror"
                                            id="date" name="date" value="{{ old('date') }}" autocomplete="off">
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="note">Keterangan</label>
                                        <input type="text" class="form-control @error('note') is-invalid @enderror"
                                            id="note" name="note" value="{{ old('note') }}">
                                        @error('note')
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
