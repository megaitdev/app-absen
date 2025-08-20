@extends('layouts.app')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Perizinan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Perizinan</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <!-- Card Cuti -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('perizinan/cuti') }}" class="perizinan-card">
                            <div class="custom-perizinan-card">
                                <div class="card-icon-custom bg-primary-custom">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Cuti</div>
                                    <div class="card-description">Pengajuan cuti karyawan</div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card Izin -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('perizinan/izin') }}" class="perizinan-card">
                            <div class="custom-perizinan-card">
                                <div class="card-icon-custom bg-warning-custom">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Izin</div>
                                    <div class="card-description">Pengajuan izin tidak masuk</div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card Verifikasi Absen -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('perizinan/verifikasi-absen') }}" class="perizinan-card">
                            <div class="custom-perizinan-card">
                                <div class="card-icon-custom bg-success-custom">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Verifikasi Absen</div>
                                    <div class="card-description">Verifikasi kehadiran manual</div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Card Lembur -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('perizinan/lembur') }}" class="perizinan-card">
                            <div class="custom-perizinan-card">
                                <div class="card-icon-custom bg-danger-custom">
                                    <i class="fas fa-business-time"></i>
                                </div>
                                <div class="card-content">
                                    <div class="card-title">Lembur</div>
                                    <div class="card-description">Pengajuan lembur kerja</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informasi Penting</h5>
                            <ul class="mb-0">
                                <li><strong>Cuti:</strong> Pengajuan cuti tahunan, sakit, atau keperluan khusus</li>
                                <li><strong>Izin:</strong> Pengajuan izin tidak masuk kerja untuk keperluan mendadak</li>
                                <li><strong>Verifikasi Absen:</strong> Pengajuan verifikasi kehadiran jika terjadi masalah
                                    absen</li>
                                <li><strong>Lembur:</strong> Pengajuan lembur untuk pekerjaan di luar jam kerja normal</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
