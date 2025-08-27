@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-header {
            background: linear-gradient(135deg, #dc3545, #b52a37);
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
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #dc3545, #b52a37);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
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
            border: 2px dashed #dc3545;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #fff5f5;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background: #ffe3e6;
            border-color: #b52a37;
        }

        .file-upload-area.dragover {
            background: #ffe3e6;
            border-color: #b52a37;
        }

        .info-card {
            background: linear-gradient(135deg, #fff5f5, #ffe3e6);
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #dc3545;
        }

        .duration-display {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            border-left: 4px solid #dc3545;
        }

        .required {
            color: #dc3545;
        }

        .section-header {
            margin-bottom: 30px;
        }

        .form-header h4 {
            margin-bottom: 5px;
        }

        .form-header p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
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

        .team-member-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .team-member-item .remove-member {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
        }

        .team-member-item .remove-member:hover {
            color: #c82333;
        }

        .jenis-lembur-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .jenis-lembur-card:hover {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .jenis-lembur-card.active {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .jenis-lembur-card input[type="radio"] {
            display: none;
        }

        .jenis-lembur-card .icon {
            font-size: 2rem;
            color: #dc3545;
            margin-bottom: 10px;
        }

        .team-section {
            display: none;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pengajuan Lembur</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ url('perizinan') }}">Perizinan</a></div>
                    <div class="breadcrumb-item">Pengajuan Lembur</div>
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
                                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
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
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
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
                                <div class="d-flex align-items-center">
                                    <div class="icon mr-3">
                                        <i class="fas fa-business-time fa-2x"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">Form Pengajuan Lembur</h4>
                                        <p class="mb-0 mt-1 opacity-75">Lengkapi form untuk mengajukan lembur dengan
                                            workflow
                                            persetujuan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="lemburForm" action="{{ url('perizinan/lembur/store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Informasi Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-danger mb-3"><i class="fas fa-user mr-2"></i>Informasi Karyawan
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

                                    <!-- Jenis Lembur -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-danger mb-3"><i class="fas fa-users mr-2"></i>Jenis Lembur
                                            </h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="jenis-lembur-card" onclick="selectJenisLembur('sendiri')">
                                                <input type="radio" name="jenis_lembur" value="sendiri"
                                                    id="lembur_sendiri"
                                                    {{ old('jenis_lembur') == 'sendiri' ? 'checked' : '' }}>
                                                <div class="icon">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <h6>Lembur Sendiri</h6>
                                                <p class="text-muted mb-0">Lembur individu/personal</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="jenis-lembur-card" onclick="selectJenisLembur('tim')">
                                                <input type="radio" name="jenis_lembur" value="tim" id="lembur_tim"
                                                    {{ old('jenis_lembur') == 'tim' ? 'checked' : '' }}>
                                                <div class="icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <h6>Lembur Tim</h6>
                                                <p class="text-muted mb-0">Lembur bersama tim/kelompok</p>
                                            </div>
                                        </div>
                                        @error('jenis_lembur')
                                            <div class="col-12">
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Tim Section (Hidden by default) -->
                                    <div class="team-section" id="team-section">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-danger mb-3"><i class="fas fa-user-plus mr-2"></i>Anggota
                                                    Tim Lembur</h6>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="team_member_select">Pilih Anggota Tim</label>
                                                    <select class="form-control" id="team_member_select">
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        @foreach ($managedEmployees as $employee)
                                                            <option value="{{ $employee->id }}"
                                                                data-nama="{{ $employee->nama }}"
                                                                data-nip="{{ $employee->nip ?? 'No NIP' }}">
                                                                {{ $employee->nama }} - {{ $employee->nip ?? 'No NIP' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-outline-primary btn-block"
                                                        onclick="addTeamMember()">
                                                        <i class="fas fa-plus mr-2"></i>Tambah
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div id="team-members-list">
                                                    <!-- Team members will be added here dynamically -->
                                                </div>
                                                <input type="hidden" name="team_members" id="team_members_input">
                                                @error('team_members')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detail Lembur -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-danger mb-3"><i class="fas fa-clock mr-2"></i>Detail Lembur
                                            </h5>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tanggal_lembur">Tanggal Lembur <span
                                                        class="required">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('tanggal_lembur') is-invalid @enderror"
                                                    id="tanggal_lembur" name="tanggal_lembur"
                                                    value="{{ old('tanggal_lembur') }}" required>
                                                @error('tanggal_lembur')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam_mulai">Jam Mulai <span class="required">*</span></label>
                                                <input type="time"
                                                    class="form-control @error('jam_mulai') is-invalid @enderror"
                                                    id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}"
                                                    required>
                                                @error('jam_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam_selesai">Jam Selesai <span
                                                        class="required">*</span></label>
                                                <input type="time"
                                                    class="form-control @error('jam_selesai') is-invalid @enderror"
                                                    id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}"
                                                    required>
                                                @error('jam_selesai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="duration-display" id="duration-display" style="display: none;">
                                                <strong><i class="fas fa-clock mr-2"></i>Durasi Lembur: <span
                                                        id="duration-text">0 jam 0 menit</span></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keterangan">Keterangan/Alasan Lembur <span
                                                        class="required">*</span></label>
                                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                    rows="4" placeholder="Jelaskan alasan/keperluan lembur, pekerjaan yang akan dilakukan..." required>{{ old('keterangan') }}</textarea>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Minimal 10 karakter. Jelaskan secara detail
                                                    pekerjaan yang akan dilakukan saat lembur.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lampiran -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="lampiran">Lampiran Dokumen</label>
                                                <div class="file-upload-area @error('lampiran.*') border-danger @enderror"
                                                    onclick="document.getElementById('lampiran').click()">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-danger mb-3"></i>
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
                                                    <strong>Lampiran yang bisa disertakan:</strong><br>
                                                    • Surat tugas/instruksi lembur dari atasan<br>
                                                    • Dokumen pekerjaan yang harus diselesaikan<br>
                                                    • Email atau komunikasi terkait deadline<br>
                                                    • Laporan progress pekerjaan
                                                </small>
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
                                                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Lembur
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
                                <h4><i class="fas fa-info-circle mr-2"></i>Informasi Penting</h4>
                            </div>
                            <div class="card-body">
                                <div class="info-card mb-3">
                                    <h6 class="text-danger"><i class="fas fa-clock mr-2"></i>Waktu Pengajuan</h6>
                                    <p class="mb-0 small">Pengajuan lembur harus diajukan sebelum jam kerja berakhir atau
                                        minimal H-1 untuk lembur terjadwal.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-danger"><i class="fas fa-check-circle mr-2"></i>Proses Persetujuan
                                    </h6>
                                    <p class="mb-0 small">Pengajuan akan melalui persetujuan atasan langsung dan HRD
                                        sebelum disetujui.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-danger"><i class="fas fa-money-bill-wave mr-2"></i>Kompensasi</h6>
                                    <p class="mb-0 small">Lembur yang disetujui akan mendapat kompensasi sesuai kebijakan
                                        perusahaan.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-danger"><i class="fas fa-file-alt mr-2"></i>Dokumen Pendukung</h6>
                                    <p class="mb-0 small">Lampirkan dokumen pendukung untuk memperkuat alasan lembur.</p>
                                </div>

                                <div class="info-card">
                                    <h6 class="text-danger"><i class="fas fa-phone mr-2"></i>Kontak</h6>
                                    <p class="mb-0 small">Hubungi HRD (ext. 123) jika ada pertanyaan terkait pengajuan
                                        lembur.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global variables
            let teamMembers = [];

            // Employee selection handler
            const employeeSelect = document.getElementById('pilih_karyawan');
            if (employeeSelect) {
                employeeSelect.addEventListener('change', function() {
                    updateEmployeeInfo(this.value);
                });
            }

            // Time inputs for duration calculation
            const jamMulai = document.getElementById('jam_mulai');
            const jamSelesai = document.getElementById('jam_selesai');
            const durationDisplay = document.getElementById('duration-display');
            const durationText = document.getElementById('duration-text');

            if (jamMulai && jamSelesai) {
                jamMulai.addEventListener('change', calculateDuration);
                jamSelesai.addEventListener('change', calculateDuration);
            }

            // Set minimum date to today
            const tanggalLembur = document.getElementById('tanggal_lembur');
            if (tanggalLembur) {
                const today = new Date().toISOString().split('T')[0];
                tanggalLembur.min = today;
            }

            // File upload handlers
            const lampiranInput = document.getElementById('lampiran');
            const fileList = document.getElementById('file-list');

            if (lampiranInput) {
                lampiranInput.addEventListener('change', function() {
                    displayFiles(this.files);
                });

                // Drag and drop functionality
                const uploadArea = document.querySelector('.file-upload-area');

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
                    const files = e.dataTransfer.files;
                    lampiranInput.files = files;
                    displayFiles(files);
                });
            }

            // Restore old values if any
            @if (old('jenis_lembur'))
                selectJenisLembur('{{ old('jenis_lembur') }}');
            @endif

            @if (old('team_members'))
                try {
                    teamMembers = {!! json_encode(json_decode(old('team_members'), true)) !!} || [];
                    updateTeamMembersList();
                } catch (e) {
                    console.log('Error parsing old team members:', e);
                }
            @endif

            // Form validation
            document.getElementById('lemburForm').addEventListener('submit', function(e) {
                const keterangan = document.getElementById('keterangan').value;
                if (keterangan.length < 10) {
                    e.preventDefault();
                    alert('Keterangan minimal 10 karakter');
                    return false;
                }

                const jenisLembur = document.querySelector('input[name="jenis_lembur"]:checked');
                if (!jenisLembur) {
                    e.preventDefault();
                    alert('Pilih jenis lembur');
                    return false;
                }

                if (jenisLembur.value === 'tim' && teamMembers.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal 1 anggota tim untuk lembur tim');
                    return false;
                }

                if (!jamMulai.value || !jamSelesai.value) {
                    e.preventDefault();
                    alert('Jam mulai dan selesai lembur harus diisi');
                    return false;
                }

                const start = new Date('2000-01-01 ' + jamMulai.value);
                const end = new Date('2000-01-01 ' + jamSelesai.value);
                if (end <= start) {
                    e.preventDefault();
                    alert('Jam selesai harus lebih besar dari jam mulai');
                    return false;
                }
            });

            // Functions
            function updateEmployeeInfo(employeeId) {
                if (!employeeId) {
                    clearEmployeeInfo();
                    return;
                }

                fetch(`{{ url('perizinan/ajax/employee-info') }}?employee_id=${employeeId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
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
                        alert('Terjadi kesalahan saat memuat data karyawan');
                        clearEmployeeInfo();
                    });
            }

            function clearEmployeeInfo() {
                document.getElementById('nama_karyawan').value = '';
                document.getElementById('nip').value = '';
                document.getElementById('jabatan').value = '';
                document.getElementById('unit_kerja').value = '';
            }

            function calculateDuration() {
                if (jamMulai.value && jamSelesai.value) {
                    const start = new Date('2000-01-01 ' + jamMulai.value);
                    const end = new Date('2000-01-01 ' + jamSelesai.value);

                    if (end > start) {
                        const diff = end.getTime() - start.getTime();
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                        durationText.textContent = `${hours} jam ${minutes} menit`;
                        durationDisplay.style.display = 'block';
                    } else {
                        durationDisplay.style.display = 'none';
                    }
                } else {
                    durationDisplay.style.display = 'none';
                }
            }

            // Global functions for jenis lembur
            window.selectJenisLembur = function(jenis) {
                // Remove active class from all cards
                document.querySelectorAll('.jenis-lembur-card').forEach(card => {
                    card.classList.remove('active');
                });

                // Add active class to selected card
                document.querySelector(`input[value="${jenis}"]`).closest('.jenis-lembur-card').classList.add(
                    'active');

                // Check the radio button
                document.querySelector(`input[value="${jenis}"]`).checked = true;

                // Show/hide team section
                const teamSection = document.getElementById('team-section');
                if (jenis === 'tim') {
                    teamSection.style.display = 'block';
                } else {
                    teamSection.style.display = 'none';
                    teamMembers = [];
                    updateTeamMembersList();
                }
            };

            // Global functions for team management
            window.addTeamMember = function() {
                const select = document.getElementById('team_member_select');
                const selectedOption = select.options[select.selectedIndex];

                if (!selectedOption.value) {
                    alert('Pilih karyawan terlebih dahulu');
                    return;
                }

                const employeeId = selectedOption.value;
                const nama = selectedOption.dataset.nama;
                const nip = selectedOption.dataset.nip;

                // Check if already added
                if (teamMembers.some(member => member.id == employeeId)) {
                    alert('Karyawan sudah ditambahkan');
                    return;
                }

                // Add to team members
                teamMembers.push({
                    id: employeeId,
                    nama: nama,
                    nip: nip
                });

                updateTeamMembersList();
                select.value = '';
            };

            window.removeTeamMember = function(index) {
                teamMembers.splice(index, 1);
                updateTeamMembersList();
            };

            function updateTeamMembersList() {
                const list = document.getElementById('team-members-list');
                const input = document.getElementById('team_members_input');

                list.innerHTML = '';

                teamMembers.forEach((member, index) => {
                    const item = document.createElement('div');
                    item.className = 'team-member-item';
                    item.innerHTML = `
                        <div>
                            <strong>${member.nama}</strong><br>
                            <small class="text-muted">${member.nip}</small>
                        </div>
                        <div class="remove-member" onclick="removeTeamMember(${index})">
                            <i class="fas fa-times"></i>
                        </div>
                    `;
                    list.appendChild(item);
                });

                // Update hidden input
                input.value = JSON.stringify(teamMembers);
            }

            // Global function for displaying files
            window.displayFiles = function(files) {
                const fileList = document.getElementById('file-list');
                fileList.innerHTML = '';
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileItem = document.createElement('div');
                    fileItem.className =
                        'alert alert-info d-flex justify-content-between align-items-center mt-2';
                    fileItem.innerHTML = `
                        <div>
                            <i class="fas fa-file mr-2"></i>
                            <strong>${file.name}</strong>
                            <small class="text-muted ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${i})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
                }
            };

            window.removeFile = function(index) {
                const lampiranInput = document.getElementById('lampiran');
                const dt = new DataTransfer();
                const files = lampiranInput.files;

                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }

                lampiranInput.files = dt.files;
                displayFiles(dt.files);
            };
        });
    </script>
@endsection
