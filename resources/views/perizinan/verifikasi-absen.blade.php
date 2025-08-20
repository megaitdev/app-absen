@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-header {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .file-upload-area {
            border: 2px dashed #28a745;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8fff9;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background: #e8f5e8;
            border-color: #1e7e34;
        }

        .file-upload-area.dragover {
            background: #e8f5e8;
            border-color: #1e7e34;
        }

        .info-card {
            background: linear-gradient(135deg, #e8f5e8, #f8fff9);
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #28a745;
        }

        .required {
            color: #dc3545;
        }

        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .error-list {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .error-list li {
            margin-bottom: 0.25rem;
        }

        .border-danger {
            border-color: #dc3545 !important;
        }

        .file-upload-area.border-danger {
            background: #f8d7da;
        }

        .file-upload-area.border-danger:hover {
            background: #f5c6cb;
        }

        .verification-type {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .verification-type:hover {
            border-color: #28a745;
            background: #f8fff9;
        }

        .verification-type.selected {
            border-color: #28a745;
            background: #e8f5e8;
        }

        .verification-type input[type="radio"] {
            margin-right: 10px;
        }

        .time-input-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .attendance-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 10px;
        }

        .status-hadir {
            background: #d4edda;
            color: #155724;
        }

        .status-terlambat {
            background: #fff3cd;
            color: #856404;
        }

        .status-tidak-hadir {
            background: #f8d7da;
            color: #721c24;
        }

        .status-pulang-awal {
            background: #cce7ff;
            color: #004085;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Verifikasi Absen</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ url('perizinan') }}">Perizinan</a></div>
                    <div class="breadcrumb-item">Verifikasi Absen</div>
                </div>
            </div>

            <div class="section-body">
                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat Kesalahan!</h5>
                                <p class="mb-2">Silakan perbaiki kesalahan berikut:</p>
                                <ul class="error-list">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-check-circle mr-2"></i>Berhasil!</h5>
                                <p class="mb-0">{{ session('success') }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan!</h5>
                                <p class="mb-0">{{ session('error') }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card form-card">
                            <div class="form-header">
                                <h4 class="mb-0"><i class="fas fa-check-circle mr-2"></i>Form Verifikasi Absen</h4>
                                <p class="mb-0 mt-2 opacity-75">Ajukan verifikasi untuk memperbaiki data kehadiran yang
                                    tidak sesuai</p>
                            </div>
                            <div class="card-body">
                                <form id="verifikasiForm" action="{{ url('perizinan/verifikasi-absen/store') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Informasi Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-success mb-3"><i class="fas fa-user mr-2"></i>Informasi Karyawan
                                            </h5>
                                        </div>
                                        @if ($managedEmployees->count() > 1)
                                            <div class="col-12 mb-3">
                                                <div class="form-group">
                                                    <label for="pilih_karyawan">Pilih Karyawan <span
                                                            class="required">*</span></label>
                                                    <select class="form-control @error('employee_id') is-invalid @enderror"
                                                        id="pilih_karyawan" name="employee_id" required>
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        @foreach ($managedEmployees as $employee)
                                                            <option value="{{ $employee->id }}"
                                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                                {{ $employee->nama }} -
                                                                {{ $employee->nip ?? 'No NIP' }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('employee_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" id="pilih_karyawan" name="employee_id"
                                                value="{{ $managedEmployees->first()->id ?? '' }}">
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama_karyawan">Nama Karyawan <span
                                                        class="required">*</span></label>
                                                <input type="text" class="form-control" id="nama_karyawan"
                                                    name="nama_karyawan"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->nama : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nip">NIP <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="nip" name="nip"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->nip ?? '-' : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jabatan">Jabatan</label>
                                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->posisi->nama ?? '-' : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="unit_kerja">Unit Kerja</label>
                                                <input type="text" class="form-control" id="unit_kerja"
                                                    name="unit_kerja"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->unit->nama ?? '-' : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Detail Verifikasi -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-success mb-3"><i class="fas fa-calendar-check mr-2"></i>Detail
                                                Verifikasi</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal_absen">Tanggal Absen <span
                                                        class="required">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('tanggal_absen') is-invalid @enderror"
                                                    id="tanggal_absen" name="tanggal_absen"
                                                    value="{{ old('tanggal_absen') }}" required>
                                                @error('tanggal_absen')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Pilih tanggal yang ingin diverifikasi</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status Absen Saat Ini</label>
                                                <div class="mt-2" id="current-status">
                                                    <span class="attendance-status status-tidak-hadir">Tidak Ada
                                                        Data</span>
                                                    <small class="text-muted d-block">Pilih tanggal untuk melihat
                                                        status</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jenis Verifikasi -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Jenis Verifikasi <span class="required">*</span></label>
                                                @error('jenis_verifikasi')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="mt-2">
                                                    <div class="verification-type"
                                                        onclick="selectVerificationType('absen_masuk')">
                                                        <input type="radio" id="absen_masuk" name="jenis_verifikasi"
                                                            value="absen_masuk"
                                                            {{ old('jenis_verifikasi') == 'absen_masuk' ? 'checked' : '' }}
                                                            required>
                                                        <label for="absen_masuk" class="mb-0">
                                                            <strong>Verifikasi Absen Masuk</strong>
                                                            <br><small class="text-muted">Saya sudah hadir tetapi tidak
                                                                tercatat absen masuk</small>
                                                        </label>
                                                    </div>

                                                    <div class="verification-type"
                                                        onclick="selectVerificationType('absen_pulang')">
                                                        <input type="radio" id="absen_pulang" name="jenis_verifikasi"
                                                            value="absen_pulang"
                                                            {{ old('jenis_verifikasi') == 'absen_pulang' ? 'checked' : '' }}>
                                                        <label for="absen_pulang" class="mb-0">
                                                            <strong>Verifikasi Absen Pulang</strong>
                                                            <br><small class="text-muted">Saya sudah pulang tetapi tidak
                                                                tercatat absen pulang</small>
                                                        </label>
                                                    </div>

                                                    <div class="verification-type"
                                                        onclick="selectVerificationType('koreksi_waktu')">
                                                        <input type="radio" id="koreksi_waktu" name="jenis_verifikasi"
                                                            value="koreksi_waktu"
                                                            {{ old('jenis_verifikasi') == 'koreksi_waktu' ? 'checked' : '' }}>
                                                        <label for="koreksi_waktu" class="mb-0">
                                                            <strong>Koreksi Waktu Absen</strong>
                                                            <br><small class="text-muted">Waktu absen tercatat tidak sesuai
                                                                dengan waktu sebenarnya</small>
                                                        </label>
                                                    </div>

                                                    <div class="verification-type"
                                                        onclick="selectVerificationType('absen_lengkap')">
                                                        <input type="radio" id="absen_lengkap" name="jenis_verifikasi"
                                                            value="absen_lengkap"
                                                            {{ old('jenis_verifikasi') == 'absen_lengkap' ? 'checked' : '' }}>
                                                        <label for="absen_lengkap" class="mb-0">
                                                            <strong>Verifikasi Absen Lengkap</strong>
                                                            <br><small class="text-muted">Tidak ada catatan absen sama
                                                                sekali padahal sudah hadir</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Waktu yang Benar -->
                                    <div class="row mb-4" id="time-correction-section" style="display: none;">
                                        <div class="col-12">
                                            <div class="time-input-group">
                                                <h6 class="text-success mb-3"><i class="fas fa-clock mr-2"></i>Waktu yang
                                                    Benar</h6>
                                                <div class="row">
                                                    <div class="col-md-6" id="jam-masuk-section">
                                                        <div class="form-group">
                                                            <label for="jam_masuk_benar">Jam Masuk yang Benar</label>
                                                            <input type="time"
                                                                class="form-control @error('jam_masuk_benar') is-invalid @enderror"
                                                                id="jam_masuk_benar" name="jam_masuk_benar"
                                                                value="{{ old('jam_masuk_benar') }}">
                                                            @error('jam_masuk_benar')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <small class="text-muted">Waktu sebenarnya saat masuk
                                                                kerja</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" id="jam-pulang-section">
                                                        <div class="form-group">
                                                            <label for="jam_pulang_benar">Jam Pulang yang Benar</label>
                                                            <input type="time"
                                                                class="form-control @error('jam_pulang_benar') is-invalid @enderror"
                                                                id="jam_pulang_benar" name="jam_pulang_benar"
                                                                value="{{ old('jam_pulang_benar') }}">
                                                            @error('jam_pulang_benar')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <small class="text-muted">Waktu sebenarnya saat pulang
                                                                kerja</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alasan Verifikasi -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="alasan_verifikasi">Alasan/Penjelasan Verifikasi <span
                                                        class="required">*</span></label>
                                                <textarea class="form-control @error('alasan_verifikasi') is-invalid @enderror" id="alasan_verifikasi"
                                                    name="alasan_verifikasi" rows="4"
                                                    placeholder="Jelaskan secara detail mengapa data absen perlu diverifikasi..." required>{{ old('alasan_verifikasi') }}</textarea>
                                                @error('alasan_verifikasi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">
                                                    Contoh: "Saya sudah masuk kerja jam 08:00 tetapi lupa absen karena
                                                    langsung meeting. Saya baru ingat absen saat jam istirahat."
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Saksi/Referensi -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="saksi_nama">Nama Saksi/Referensi</label>
                                                <input type="text"
                                                    class="form-control @error('saksi_nama') is-invalid @enderror"
                                                    id="saksi_nama" name="saksi_nama"
                                                    placeholder="Nama rekan kerja yang dapat membuktikan"
                                                    value="{{ old('saksi_nama') }}">
                                                @error('saksi_nama')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Opsional, tetapi sangat membantu
                                                    verifikasi</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="saksi_jabatan">Jabatan Saksi</label>
                                                <input type="text"
                                                    class="form-control @error('saksi_jabatan') is-invalid @enderror"
                                                    id="saksi_jabatan" name="saksi_jabatan" placeholder="Jabatan saksi"
                                                    value="{{ old('saksi_jabatan') }}">
                                                @error('saksi_jabatan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Jabatan dari saksi yang disebutkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lokasi Kerja -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="lokasi_kerja">Lokasi Kerja Saat Itu</label>
                                                <input type="text"
                                                    class="form-control @error('lokasi_kerja') is-invalid @enderror"
                                                    id="lokasi_kerja" name="lokasi_kerja"
                                                    placeholder="Dimana Anda bekerja pada tanggal tersebut"
                                                    value="{{ old('lokasi_kerja') }}">
                                                @error('lokasi_kerja')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Contoh: Kantor pusat, WFH, Meeting di client,
                                                    dll.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lampiran Bukti -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="lampiran">Lampiran Bukti Pendukung</label>
                                                <div class="file-upload-area @error('lampiran.*') border-danger @enderror"
                                                    onclick="document.getElementById('lampiran').click()">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-success mb-3"></i>
                                                    <h5>Klik untuk upload atau drag & drop file</h5>
                                                    <p class="text-muted mb-0">Format: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)
                                                    </p>
                                                    <input type="file" class="d-none" id="lampiran"
                                                        name="lampiran[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                        multiple>
                                                </div>
                                                @error('lampiran.*')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div id="file-list" class="mt-3"></div>
                                                <small class="text-muted">
                                                    <strong>Bukti yang dapat dilampirkan:</strong><br>
                                                    • Screenshot email/chat dengan atasan<br>
                                                    • Foto saat berada di kantor<br>
                                                    • Dokumen meeting/agenda kerja<br>
                                                    • Bukti transportasi (tiket, e-toll, dll)<br>
                                                    • Screenshot aplikasi lain yang menunjukkan aktivitas kerja
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pernyataan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="alert alert-success">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('pernyataan') is-invalid @enderror"
                                                        type="checkbox" id="pernyataan" name="pernyataan"
                                                        {{ old('pernyataan') ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="pernyataan">
                                                        <strong>Pernyataan:</strong> Saya menyatakan bahwa informasi yang
                                                        saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya
                                                        memahami bahwa verifikasi absen yang tidak benar dapat berdampak
                                                        pada penilaian kinerja dan tindakan disipliner.
                                                    </label>
                                                    @error('pernyataan')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="text-right">
                                                <a href="{{ url('perizinan') }}" class="btn btn-cancel text-white mr-2">
                                                    <i class="fas fa-times mr-2"></i>Batal
                                                </a>
                                                <button type="submit" class="btn btn-submit text-white">
                                                    <i class="fas fa-check mr-2"></i>Ajukan Verifikasi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle mr-2"></i>Panduan Verifikasi</h4>
                            </div>
                            <div class="card-body">
                                <div class="info-card mb-3">
                                    <h6 class="text-success"><i class="fas fa-clock mr-2"></i>Batas Waktu</h6>
                                    <p class="mb-0 small">Pengajuan verifikasi maksimal 7 hari setelah tanggal absen yang
                                        bermasalah.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-success"><i class="fas fa-search mr-2"></i>Proses Verifikasi</h6>
                                    <p class="mb-0 small">Tim HRD akan melakukan pengecekan CCTV, log sistem, dan
                                        konfirmasi dengan saksi jika ada.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-success"><i class="fas fa-file-alt mr-2"></i>Bukti Pendukung</h6>
                                    <p class="mb-0 small">Semakin lengkap bukti yang dilampirkan, semakin cepat proses
                                        verifikasi.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-success"><i class="fas fa-exclamation-triangle mr-2"></i>Penting</h6>
                                    <p class="mb-0 small">Verifikasi absen yang terbukti tidak benar dapat berakibat pada
                                        tindakan disipliner.</p>
                                </div>

                                <div class="info-card">
                                    <h6 class="text-success"><i class="fas fa-phone mr-2"></i>Bantuan</h6>
                                    <p class="mb-0 small">Hubungi HRD (ext. 123) untuk bantuan pengisian form verifikasi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Global function for displaying files
        function displayFiles(files) {
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center';
                fileItem.innerHTML = `
                    <div>
                        <i class="fas fa-file mr-2"></i>
                        <strong>${file.name}</strong>
                        <small class="text-dark ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${i})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileList.appendChild(fileItem);
            }
        }

        // Global function for removing files
        function removeFile(index) {
            const fileInput = document.getElementById('lampiran');
            const dt = new DataTransfer();
            const files = fileInput.files;

            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }

            fileInput.files = dt.files;
            displayFiles(dt.files);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Employee selection change handler
            const pilihKaryawan = document.getElementById('pilih_karyawan');
            if (pilihKaryawan) {
                if (pilihKaryawan.tagName === 'SELECT') {
                    pilihKaryawan.addEventListener('change', function() {
                        const employeeId = this.value;
                        if (employeeId) {
                            loadEmployeeInfo(employeeId);
                        } else {
                            clearEmployeeInfo();
                        }
                    });
                }

                // Load initial employee info if only one employee or pre-selected
                const initialEmployeeId = pilihKaryawan.value;
                if (initialEmployeeId) {
                    loadEmployeeInfo(initialEmployeeId);
                }
            }

            function loadEmployeeInfo(employeeId) {
                // Show loading state
                const fields = ['nama_karyawan', 'nip', 'jabatan', 'unit_kerja'];
                fields.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        element.value = 'Loading...';
                    }
                });

                // Make AJAX request
                fetch(`{{ url('perizinan/ajax/employee-info') }}?employee_id=${employeeId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            clearEmployeeInfo();
                            return;
                        }

                        // Update form fields
                        document.getElementById('nama_karyawan').value = data.nama || '-';
                        document.getElementById('nip').value = data.nip || '-';
                        document.getElementById('jabatan').value = data.jabatan || '-';
                        document.getElementById('unit_kerja').value = data.unit_kerja || '-';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(
                        'Terjadi kesalahan saat memuat data karyawan. Pastikan Anda terhubung ke server.');
                        clearEmployeeInfo();
                    });
            }

            function clearEmployeeInfo() {
                document.getElementById('nama_karyawan').value = '';
                document.getElementById('nip').value = '';
                document.getElementById('jabatan').value = '';
                document.getElementById('unit_kerja').value = '';
            }

            // Handle verification type selection
            const verificationTypes = document.querySelectorAll('.verification-type');
            const timeCorrectionSection = document.getElementById('time-correction-section');
            const jamMasukSection = document.getElementById('jam-masuk-section');
            const jamPulangSection = document.getElementById('jam-pulang-section');

            verificationTypes.forEach(type => {
                type.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Remove selected class from all types
                    verificationTypes.forEach(t => t.classList.remove('selected'));
                    // Add selected class to clicked type
                    this.classList.add('selected');

                    // Show/hide time correction section based on selection
                    const value = radio.value;
                    if (value === 'absen_masuk' || value === 'absen_pulang' || value ===
                        'koreksi_waktu' || value === 'absen_lengkap') {
                        timeCorrectionSection.style.display = 'block';

                        // Show/hide specific time inputs based on verification type
                        if (value === 'absen_masuk' || value === 'absen_lengkap') {
                            jamMasukSection.style.display = 'block';
                        } else {
                            jamMasukSection.style.display = 'none';
                        }

                        if (value === 'absen_pulang' || value === 'absen_lengkap') {
                            jamPulangSection.style.display = 'block';
                        } else {
                            jamPulangSection.style.display = 'none';
                        }

                        if (value === 'koreksi_waktu') {
                            jamMasukSection.style.display = 'block';
                            jamPulangSection.style.display = 'block';
                        }
                    } else {
                        timeCorrectionSection.style.display = 'none';
                    }
                });
            });

            // Initialize selected verification type on page load
            const selectedVerification = document.querySelector('input[name="jenis_verifikasi"]:checked');
            if (selectedVerification) {
                selectedVerification.closest('.verification-type').classList.add('selected');
                selectedVerification.closest('.verification-type').click();
            }

            // Handle date change to show current attendance status
            document.getElementById('tanggal_absen').addEventListener('change', function() {
                const selectedDate = this.value;
                const currentStatus = document.getElementById('current-status');

                if (selectedDate) {
                    // Simulate fetching attendance data (replace with actual API call)
                    setTimeout(() => {
                        const statuses = [
                            '<span class="attendance-status status-hadir">Hadir</span><small class="text-muted d-block">Masuk: 08:15, Pulang: 17:30</small>',
                            '<span class="attendance-status status-terlambat">Terlambat</span><small class="text-muted d-block">Masuk: 08:45, Pulang: 17:30</small>',
                            '<span class="attendance-status status-tidak-hadir">Tidak Hadir</span><small class="text-muted d-block">Tidak ada catatan absen</small>',
                            '<span class="attendance-status status-pulang-awal">Pulang Awal</span><small class="text-muted d-block">Masuk: 08:00, Pulang: 16:00</small>'
                        ];
                        const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];
                        currentStatus.innerHTML = randomStatus;
                    }, 500);
                }
            });

            // File upload handling
            const fileInput = document.getElementById('lampiran');
            const uploadArea = document.querySelector('.file-upload-area');

            fileInput.addEventListener('change', function() {
                addNewFiles(this.files);
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                addNewFiles(e.dataTransfer.files);
            });

            // Function to add new files to existing files
            function addNewFiles(newFiles) {
                const dt = new DataTransfer();

                // Add existing files
                const existingFiles = fileInput.files;
                for (let i = 0; i < existingFiles.length; i++) {
                    dt.items.add(existingFiles[i]);
                }

                // Add new files
                for (let i = 0; i < newFiles.length; i++) {
                    // Check if file already exists (by name and size)
                    let fileExists = false;
                    for (let j = 0; j < existingFiles.length; j++) {
                        if (existingFiles[j].name === newFiles[i].name &&
                            existingFiles[j].size === newFiles[i].size) {
                            fileExists = true;
                            break;
                        }
                    }

                    // Only add if file doesn't exist
                    if (!fileExists) {
                        dt.items.add(newFiles[i]);
                    }
                }

                // Update file input
                fileInput.files = dt.files;
                displayFiles(dt.files);
            }

            // Form validation
            document.getElementById('verifikasiForm').addEventListener('submit', function(e) {
                const alasan = document.getElementById('alasan_verifikasi').value;
                const pernyataan = document.getElementById('pernyataan').checked;
                const jenisVerifikasi = document.querySelector('input[name="jenis_verifikasi"]:checked');

                if (alasan.length < 20) {
                    e.preventDefault();
                    alert('Alasan verifikasi minimal 20 karakter untuk penjelasan yang memadai');
                    return false;
                }

                if (!pernyataan) {
                    e.preventDefault();
                    alert('Anda harus menyetujui pernyataan');
                    return false;
                }

                if (!jenisVerifikasi) {
                    e.preventDefault();
                    alert('Pilih jenis verifikasi');
                    return false;
                }
            });

            // Set maximum date to today and minimum to 7 days ago
            const today = new Date();
            const sevenDaysAgo = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000));

            document.getElementById('tanggal_absen').setAttribute('max', today.toISOString().split('T')[0]);
            document.getElementById('tanggal_absen').setAttribute('min', sevenDaysAgo.toISOString().split('T')[0]);
        });

        function selectVerificationType(value) {
            document.getElementById(value).click();
        }
    </script>
@endsection
