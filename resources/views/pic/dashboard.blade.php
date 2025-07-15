@extends('layouts.app')

@section('title', 'Dashboard PIC')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/chart.js/dist/Chart.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard PIC</h1>
            </div>

            <div class="section-body">
                <h2 class="section-title">Monitoring Karyawan</h2>
                <p class="section-lead">
                    Ringkasan informasi karyawan yang dikelola dan status kehadiran
                </p>

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Karyawan</h4>
                                </div>
                                <div class="card-body">
                                    {{ $totalKaryawan ?? 25 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Kehadiran</h4>
                                </div>
                                <div class="card-body">
                                    {{ $persentaseKehadiran ?? '85%' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Lembur</h4>
                                </div>
                                <div class="card-body">
                                    {{ $persentaseLembur ?? '12%' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Ketidakhadiran</h4>
                                </div>
                                <div class="card-body">
                                    {{ $persentaseKetidakhadiran ?? '15%' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Overview -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Persentase Kehadiran Tiap Unit</h4>
                                <div class="card-header-action">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-primary">Minggu Ini</a>
                                        <a href="#" class="btn">Bulan Ini</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="unitAttendanceChart" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Ringkasan Ketidakhadiran</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="absenceChart" height="250"></canvas>
                                <div class="mt-4">
                                    <div class="mb-2 text-small d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 bullet bullet-success"></span> Cuti
                                        </div>
                                        <span>{{ $persentaseCuti ?? '6%' }}</span>
                                    </div>
                                    <div class="mb-2 text-small d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 bullet bullet-primary"></span> Izin
                                        </div>
                                        <span>{{ $persentaseIzin ?? '4%' }}</span>
                                    </div>
                                    <div class="mb-2 text-small d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 bullet bullet-warning"></span> Sakit
                                        </div>
                                        <span>{{ $persentaseSakit ?? '3%' }}</span>
                                    </div>
                                    <div class="mb-2 text-small d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2 bullet bullet-info"></span> Verifikasi
                                        </div>
                                        <span>{{ $persentaseVerifikasi ?? '2%' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Unit Cards -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Performa Unit</h4>
                                <div class="card-header-action">
                                    <a href="#" class="btn btn-primary">Lihat Semua</a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="unit-performance-table">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Total Karyawan</th>
                                                <th>Kehadiran</th>
                                                <th>Cuti</th>
                                                <th>Izin</th>
                                                <th>Sakit</th>
                                                <th>Lembur</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Content will be loaded via AJAX -->
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-3">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p class="mt-2">Memuat data...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card" id="recent-activities-container">
                            <div class="card-header">
                                <h4>Aktivitas Terbaru</h4>
                                <div class="card-header-action">
                                    <button class="btn btn-sm btn-primary" onclick="loadRecentActivities()" title="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled list-unstyled-border" id="recent-activities-list">
                                    <!-- Content will be loaded via AJAX -->
                                    <li class="text-center text-muted py-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Memuat data...</p>
                                    </li>
                                </ul>
                                <div class="text-center pt-1 pb-1">
                                    <a href="{{ url('report') }}" class="btn btn-primary btn-lg btn-round">
                                        Lihat Semua
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Perhatian Khusus</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <div class="alert-title">Perlu Perhatian</div>
                                    <p>3 karyawan unit Marketing memiliki tingkat kehadiran di bawah 70%</p>
                                </div>
                                <div class="alert alert-info">
                                    <div class="alert-title">Pengajuan Cuti</div>
                                    <p>5 pengajuan cuti baru menunggu verifikasi</p>
                                </div>
                                <div class="alert alert-success">
                                    <div class="alert-title">Pencapaian</div>
                                    <p>Unit HR mencapai 95% kehadiran selama 3 bulan berturut-turut</p>
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
    <!-- JS Libraries -->
    <script src="{{ asset('assets/modules/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script>
        // Unit Attendance Chart - Initialize with empty data
        var unitAttendanceCtx = document.getElementById("unitAttendanceChart").getContext('2d');
        var unitAttendanceChart = new Chart(unitAttendanceCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: [],
                    backgroundColor: 'rgba(67, 94, 190, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100
                        }
                    }]
                },
            }
        });

        // Store chart reference globally
        window.unitAttendanceChart = unitAttendanceChart;

        // Initialize chart with data if available
        if (typeof window.initializeChartsWithData === 'function') {
            window.initializeChartsWithData();
        }

        // Absence Chart - Initialize with empty data
        var absenceCtx = document.getElementById("absenceChart").getContext('2d');
        var absenceChart = new Chart(absenceCtx, {
            type: 'doughnut',
            data: {
                labels: ["Cuti", "Izin", "Sakit", "Verifikasi"],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#63ed7a',
                        '#6777ef',
                        '#ffa426',
                        '#3abaf4'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                    display: false
                },
                cutoutPercentage: 80
            }
        });

        // Store chart reference globally
        window.absenceChart = absenceChart;

        // Initialize chart with data if available
        if (typeof window.initializeChartsWithData === 'function') {
            window.initializeChartsWithData();
        }
    </script>
@endpush
