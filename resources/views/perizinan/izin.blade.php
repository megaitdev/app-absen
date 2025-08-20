@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-header {
            background: linear-gradient(135deg, #ffc107, #e0a800);
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
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
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
            border: 2px dashed #ffc107;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #fffbf0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background: #fff8e1;
            border-color: #e0a800;
        }

        .file-upload-area.dragover {
            background: #fff8e1;
            border-color: #e0a800;
        }

        .info-card {
            background: linear-gradient(135deg, #fff8e1, #fffbf0);
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #ffc107;
        }

        .duration-display {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
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

        .time-option {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .time-option:hover {
            border-color: #ffc107;
            background: #fffbf0;
        }

        .time-option.selected {
            border-color: #ffc107;
            background: #fff8e1;
        }

        .time-option input[type="radio"] {
            margin-right: 10px;
        }
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pengajuan Izin</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ url('perizinan') }}">Perizinan</a></div>
                    <div class="breadcrumb-item">Pengajuan Izin</div>
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
                                <h4 class="mb-0"><i class="fas fa-clock mr-2"></i>Form Pengajuan Izin</h4>
                                <p class="mb-0 mt-2 opacity-75">Silakan lengkapi form di bawah ini untuk mengajukan izin
                                    tidak masuk kerja</p>
                            </div>
                            <div class="card-body">
                                <form id="izinForm" action="{{ url('perizinan/izin/store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Informasi Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-warning mb-3"><i class="fas fa-user mr-2"></i>Informasi Karyawan
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

                                    <!-- Detail Izin -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-warning mb-3"><i class="fas fa-calendar-alt mr-2"></i>Detail
                                                Izin</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jenis_izin">Jenis Izin <span class="required">*</span></label>
                                                <select class="form-control" id="jenis_izin" name="jenis_izin" required>
                                                    <option value="">Pilih Jenis Izin</option>
                                                    <option value="sakit">Izin Sakit</option>
                                                    <option value="keperluan_keluarga">Keperluan Keluarga</option>
                                                    <option value="keperluan_pribadi">Keperluan Pribadi</option>
                                                    <option value="keperluan_mendesak">Keperluan Mendesak</option>
                                                    <option value="keperluan_medis">Keperluan Medis</option>
                                                    <option value="acara_keluarga">Acara Keluarga</option>
                                                    <option value="keperluan_resmi">Keperluan Resmi</option>
                                                    <option value="lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal_izin">Tanggal Izin <span
                                                        class="required">*</span></label>
                                                <input type="date" class="form-control" id="tanggal_izin"
                                                    name="tanggal_izin" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Waktu Izin -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Waktu Izin <span class="required">*</span></label>
                                                <div class="mt-2">
                                                    <div class="time-option" onclick="selectTimeOption('full_day')">
                                                        <input type="radio" id="full_day" name="waktu_izin"
                                                            value="full_day" required>
                                                        <label for="full_day" class="mb-0">
                                                            <strong>Seharian Penuh</strong>
                                                            <br><small class="text-muted">Tidak masuk kerja
                                                                seharian</small>
                                                        </label>
                                                    </div>

                                                    <div class="time-option"
                                                        onclick="selectTimeOption('half_day_morning')">
                                                        <input type="radio" id="half_day_morning" name="waktu_izin"
                                                            value="half_day_morning">
                                                        <label for="half_day_morning" class="mb-0">
                                                            <strong>Setengah Hari (Pagi)</strong>
                                                            <br><small class="text-muted">Izin pagi, masuk siang</small>
                                                        </label>
                                                    </div>

                                                    <div class="time-option"
                                                        onclick="selectTimeOption('half_day_afternoon')">
                                                        <input type="radio" id="half_day_afternoon" name="waktu_izin"
                                                            value="half_day_afternoon">
                                                        <label for="half_day_afternoon" class="mb-0">
                                                            <strong>Setengah Hari (Siang)</strong>
                                                            <br><small class="text-muted">Masuk pagi, izin siang</small>
                                                        </label>
                                                    </div>

                                                    <div class="time-option" onclick="selectTimeOption('custom_time')">
                                                        <input type="radio" id="custom_time" name="waktu_izin"
                                                            value="custom_time">
                                                        <label for="custom_time" class="mb-0">
                                                            <strong>Waktu Tertentu</strong>
                                                            <br><small class="text-muted">Tentukan jam mulai dan selesai
                                                                izin</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Time Fields -->
                                    <div class="row mb-4" id="custom-time-fields" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jam_mulai">Jam Mulai Izin</label>
                                                <input type="time" class="form-control" id="jam_mulai"
                                                    name="jam_mulai">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jam_selesai">Jam Selesai Izin</label>
                                                <input type="time" class="form-control" id="jam_selesai"
                                                    name="jam_selesai">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keterangan">Keterangan/Alasan Izin <span
                                                        class="required">*</span></label>
                                                <textarea class="form-control" id="keterangan" name="keterangan" rows="4"
                                                    placeholder="Jelaskan alasan pengajuan izin secara detail..." required></textarea>
                                                <small class="text-muted">Minimal 10 karakter. Semakin detail alasan,
                                                    semakin mudah persetujuan.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kontak Darurat -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kontak_darurat">Kontak yang Dapat Dihubungi</label>
                                                <input type="text" class="form-control" id="kontak_darurat"
                                                    name="kontak_darurat"
                                                    placeholder="Nomor telepon yang dapat dihubungi">
                                                <small class="text-muted">Opsional, untuk keperluan darurat</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lokasi_izin">Lokasi Selama Izin</label>
                                                <input type="text" class="form-control" id="lokasi_izin"
                                                    name="lokasi_izin" placeholder="Lokasi/tempat selama izin">
                                                <small class="text-muted">Opsional, untuk keperluan darurat</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lampiran -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="lampiran">Lampiran Dokumen Pendukung</label>
                                                <div class="file-upload-area"
                                                    onclick="document.getElementById('lampiran').click()">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>
                                                    <h5>Klik untuk upload atau drag & drop file</h5>
                                                    <p class="text-muted mb-0">Format: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)
                                                    </p>
                                                    <input type="file" class="d-none" id="lampiran"
                                                        name="lampiran[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                        multiple>
                                                </div>
                                                <div id="file-list" class="mt-3"></div>
                                                <small class="text-muted">
                                                    <strong>Lampiran yang disarankan:</strong><br>
                                                    • Izin Sakit: Surat keterangan dokter/resep obat<br>
                                                    • Keperluan Keluarga: Undangan/surat keterangan<br>
                                                    • Keperluan Medis: Surat rujukan/janji temu dokter<br>
                                                    • Keperluan Resmi: Surat panggilan/undangan resmi
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pernyataan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="pernyataan"
                                                        name="pernyataan" required>
                                                    <label class="form-check-label" for="pernyataan">
                                                        <strong>Pernyataan:</strong> Saya menyatakan bahwa informasi yang
                                                        saya berikan adalah benar dan saya bersedia bertanggung jawab atas
                                                        konsekuensi dari izin ini. Saya juga berkomitmen untuk menyelesaikan
                                                        pekerjaan yang tertunda setelah kembali bekerja.
                                                    </label>
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
                                                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Izin
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
                                    <h6 class="text-warning"><i class="fas fa-clock mr-2"></i>Waktu Pengajuan</h6>
                                    <p class="mb-0 small">Pengajuan izin sebaiknya diajukan sebelum jam kerja dimulai atau
                                        sesegera mungkin.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-warning"><i class="fas fa-check-circle mr-2"></i>Proses Persetujuan
                                    </h6>
                                    <p class="mb-0 small">Pengajuan akan diproses oleh atasan langsung. Pastikan alasan
                                        jelas dan lengkap.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-warning"><i class="fas fa-file-alt mr-2"></i>Dokumen Pendukung</h6>
                                    <p class="mb-0 small">Lampirkan dokumen pendukung untuk memperkuat alasan izin Anda.
                                    </p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Catatan
                                        Penting</h6>
                                    <p class="mb-0 small">Izin mendadak tanpa alasan yang jelas dapat mempengaruhi
                                        penilaian kinerja.</p>
                                </div>

                                <div class="info-card">
                                    <h6 class="text-warning"><i class="fas fa-phone mr-2"></i>Kontak</h6>
                                    <p class="mb-0 small">Hubungi atasan langsung (ext. 456) atau HRD (ext. 123) untuk
                                        pertanyaan.</p>
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

            // Handle time option selection
            const timeOptions = document.querySelectorAll('.time-option');
            const customTimeFields = document.getElementById('custom-time-fields');
            const jamMulai = document.getElementById('jam_mulai');
            const jamSelesai = document.getElementById('jam_selesai');

            timeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Remove selected class from all options
                    timeOptions.forEach(opt => opt.classList.remove('selected'));
                    // Add selected class to clicked option
                    this.classList.add('selected');

                    // Show/hide custom time fields
                    if (radio.value === 'custom_time') {
                        customTimeFields.style.display = 'block';
                        jamMulai.required = true;
                        jamSelesai.required = true;
                    } else {
                        customTimeFields.style.display = 'none';
                        jamMulai.required = false;
                        jamSelesai.required = false;
                        jamMulai.value = '';
                        jamSelesai.value = '';
                    }
                });
            });

            // File upload handling
            const fileInput = document.getElementById('lampiran');
            const fileList = document.getElementById('file-list');
            const uploadArea = document.querySelector('.file-upload-area');

            fileInput.addEventListener('change', function() {
                displayFiles(this.files);
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
                fileInput.files = e.dataTransfer.files;
                displayFiles(e.dataTransfer.files);
            });

            function displayFiles(files) {
                fileList.innerHTML = '';
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileItem = document.createElement('div');
                    fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center';
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
            }

            // Form validation
            document.getElementById('izinForm').addEventListener('submit', function(e) {
                const keterangan = document.getElementById('keterangan').value;
                const pernyataan = document.getElementById('pernyataan').checked;
                const waktuIzin = document.querySelector('input[name="waktu_izin"]:checked');

                if (keterangan.length < 10) {
                    e.preventDefault();
                    alert('Keterangan minimal 10 karakter');
                    return false;
                }

                if (!pernyataan) {
                    e.preventDefault();
                    alert('Anda harus menyetujui pernyataan');
                    return false;
                }

                if (!waktuIzin) {
                    e.preventDefault();
                    alert('Pilih waktu izin');
                    return false;
                }

                // Validate custom time if selected
                if (waktuIzin.value === 'custom_time') {
                    const jamMulai = document.getElementById('jam_mulai').value;
                    const jamSelesai = document.getElementById('jam_selesai').value;

                    if (!jamMulai || !jamSelesai) {
                        e.preventDefault();
                        alert('Jam mulai dan selesai harus diisi untuk waktu tertentu');
                        return false;
                    }

                    if (jamMulai >= jamSelesai) {
                        e.preventDefault();
                        alert('Jam selesai harus lebih besar dari jam mulai');
                        return false;
                    }
                }
            });

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_izin').setAttribute('min', today);
        });

        function selectTimeOption(value) {
            document.getElementById(value).click();
        }

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
    </script>
@endsection
