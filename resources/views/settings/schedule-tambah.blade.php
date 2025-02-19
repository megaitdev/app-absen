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
                                <h4>Formulir Tambah Schedule</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('settings/schedule/store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="schedule">Nama Schedule</label>
                                        <input type="text" class="form-control @error('schedule') is-invalid @enderror"
                                            id="schedule" name="schedule" value="{{ old('schedule') }}">
                                        @error('schedule')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror h-100" id="keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Hari</th>
                                                <th>Shift</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                                <tr>
                                                    <td class="text-center">{{ $day }}</td>
                                                    <td>
                                                        <select
                                                            class="form-control select2 @error(strtolower($day)) is-invalid @enderror"
                                                            name="{{ strtolower($day) }}" id="{{ strtolower($day) }}">
                                                            <option value="">-- Pilih Shift --</option>
                                                            <option value="libur">Libur</option>
                                                            @foreach ($shift as $item)
                                                                <option value="{{ $item->id }}"
                                                                    {{ old(strtolower($day)) == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error(strtolower($day))
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
