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
                                    <table class="table table-striped">
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
                                        @foreach ($units ?? [['nama' => 'IT', 'total' => 8, 'kehadiran' => '90%', 'cuti' => '5%', 'izin' => '2%', 'sakit' => '3%', 'lembur' => '15%', 'status' => 'Baik'], ['nama' => 'Finance', 'total' => 6, 'kehadiran' => '85%', 'cuti' => '10%', 'izin' => '0%', 'sakit' => '5%', 'lembur' => '5%', 'status' => 'Baik'], ['nama' => 'Marketing', 'total' => 7, 'kehadiran' => '75%', 'cuti' => '5%', 'izin' => '15%', 'sakit' => '5%', 'lembur' => '20%', 'status' => 'Perlu Perhatian'], ['nama' => 'HR', 'total' => 4, 'kehadiran' => '95%', 'cuti' => '5%', 'izin' => '0%', 'sakit' => '0%', 'lembur' => '10%', 'status' => 'Baik']] as $unit)
                                            <tr>
                                                <td>{{ $unit['nama'] }}</td>
                                                <td>{{ $unit['total'] }}</td>
                                                <td>
                                                    <div class="progress mb-1" data-height="4" data-toggle="tooltip"
                                                        title="{{ $unit['kehadiran'] }}">
                                                        <div class="progress-bar bg-success"
                                                            data-width="{{ $unit['kehadiran'] }}"></div>
                                                    </div>
                                                    {{ $unit['kehadiran'] }}
                                                </td>
                                                <td>{{ $unit['cuti'] }}</td>
                                                <td>{{ $unit['izin'] }}</td>
                                                <td>{{ $unit['sakit'] }}</td>
                                                <td>{{ $unit['lembur'] }}</td>
                                                <td>
                                                    @if ($unit['status'] == 'Baik')
                                                        <div class="badge badge-success">Baik</div>
                                                    @elseif($unit['status'] == 'Perlu Perhatian')
                                                        <div class="badge badge-warning">Perlu Perhatian</div>
                                                    @else
                                                        <div class="badge badge-danger">Bermasalah</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Aktivitas Terbaru</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled list-unstyled-border">
                                    @foreach ($recentActivities ?? [['nama' => 'Budi Santoso', 'unit' => 'IT', 'aktivitas' => 'Mengajukan cuti', 'waktu' => '2 jam yang lalu', 'avatar' => 'avatar-1.png'], ['nama' => 'Siti Nurhaliza', 'unit' => 'Finance', 'aktivitas' => 'Izin terlambat', 'waktu' => '3 jam yang lalu', 'avatar' => 'avatar-2.png'], ['nama' => 'Joko Widodo', 'unit' => 'Marketing', 'aktivitas' => 'Mengirim laporan lembur', 'waktu' => '4 jam yang lalu', 'avatar' => 'avatar-3.png'], ['nama' => 'Dewi Safitri', 'unit' => 'HR', 'aktivitas' => 'Mengkonfirmasi kehadiran', 'waktu' => '5 jam yang lalu', 'avatar' => 'avatar-4.png']] as $activity)
                                        <li class="media">
                                            <img class="mr-3 rounded-circle" width="50"
                                                src="{{ asset('img/avatar/' . $activity['avatar']) }}" alt="avatar">
                                            <div class="media-body">
                                                <div class="float-right text-small text-muted">{{ $activity['waktu'] }}
                                                </div>
                                                <div class="media-title">{{ $activity['nama'] }}</div>
                                                <span class="text-small text-muted">{{ $activity['aktivitas'] }} <div
                                                        class="bullet"></div> {{ $activity['unit'] }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-center pt-1 pb-1">
                                    <a href="#" class="btn btn-primary btn-lg btn-round">
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
        // Unit Attendance Chart
        var unitAttendanceCtx = document.getElementById("unitAttendanceChart").getContext('2d');
        var unitAttendanceChart = new Chart(unitAttendanceCtx, {
            type: 'bar',
            data: {
                labels: ["IT", "Finance", "Marketing", "HR", "Production", "Logistics"],
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: [90, 85, 75, 95, 88, 79],
                    backgroundColor: [
                        'rgba(67, 94, 190, 0.8)',
                        'rgba(67, 94, 190, 0.8)',
                        'rgba(67, 94, 190, 0.8)',
                        'rgba(67, 94, 190, 0.8)',
                        'rgba(67, 94, 190, 0.8)',
                        'rgba(67, 94, 190, 0.8)'
                    ],
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

        // Absence Chart
        var absenceCtx = document.getElementById("absenceChart").getContext('2d');
        var absenceChart = new Chart(absenceCtx, {
            type: 'doughnut',
            data: {
                labels: ["Cuti", "Izin", "Sakit", "Verifikasi"],
                datasets: [{
                    data: [6, 4, 3, 2],
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
    </script>
@endpush
