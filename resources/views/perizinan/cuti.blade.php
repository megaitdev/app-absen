@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
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
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
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
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            background: #e3f2fd;
            border-color: #0056b3;
        }

        .file-upload-area.dragover {
            background: #e3f2fd;
            border-color: #0056b3;
        }

        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #f8f9ff);
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #007bff;
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
    </style>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pengajuan Cuti</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ url('perizinan') }}">Perizinan</a></div>
                    <div class="breadcrumb-item">Pengajuan Cuti</div>
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
                                <h4 class="mb-0"><i class="fas fa-calendar-times mr-2"></i>Form Pengajuan Cuti</h4>
                                <p class="mb-0 mt-2 opacity-75">Silakan lengkapi form di bawah ini untuk mengajukan cuti</p>
                            </div>
                            <div class="card-body">
                                <form id="cutiForm" action="{{ url('perizinan/cuti/store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Informasi Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Informasi Karyawan
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

                                    <!-- Detail Cuti -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="text-primary mb-3"><i class="fas fa-calendar-alt mr-2"></i>Detail
                                                Cuti</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jenis_cuti">Jenis Cuti <span class="required">*</span></label>
                                                <select class="form-control @error('jenis_cuti') is-invalid @enderror"
                                                    id="jenis_cuti" name="jenis_cuti" required>
                                                    <option value="">Pilih Jenis Cuti</option>
                                                    <option value="tahunan"
                                                        {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan
                                                    </option>
                                                    <option value="sakit"
                                                        {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit
                                                    </option>
                                                    <option value="melahirkan"
                                                        {{ old('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>Cuti
                                                        Melahirkan</option>
                                                    <option value="menikah"
                                                        {{ old('jenis_cuti') == 'menikah' ? 'selected' : '' }}>Cuti Menikah
                                                    </option>
                                                    <option value="khitan"
                                                        {{ old('jenis_cuti') == 'khitan' ? 'selected' : '' }}>Cuti Khitan
                                                        Anak</option>
                                                    <option value="baptis"
                                                        {{ old('jenis_cuti') == 'baptis' ? 'selected' : '' }}>Cuti Baptis
                                                        Anak</option>
                                                    <option value="keluarga_meninggal"
                                                        {{ old('jenis_cuti') == 'keluarga_meninggal' ? 'selected' : '' }}>
                                                        Cuti Keluarga Meninggal</option>
                                                    <option value="ibadah_haji"
                                                        {{ old('jenis_cuti') == 'ibadah_haji' ? 'selected' : '' }}>Cuti
                                                        Ibadah Haji</option>
                                                    <option value="penting"
                                                        {{ old('jenis_cuti') == 'penting' ? 'selected' : '' }}>Cuti Penting
                                                    </option>
                                                    <option value="besar"
                                                        {{ old('jenis_cuti') == 'besar' ? 'selected' : '' }}>Cuti Besar
                                                    </option>
                                                </select>
                                                @error('jenis_cuti')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sisa_cuti">Sisa Cuti Tahunan</label>
                                                <input type="text" class="form-control" id="sisa_cuti"
                                                    name="sisa_cuti" value="12 hari" readonly>
                                                <small class="text-muted">Sisa cuti tahunan yang dapat digunakan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tanggal Cuti -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal_mulai">Tanggal Mulai Cuti <span
                                                        class="required">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                                    id="tanggal_mulai" name="tanggal_mulai"
                                                    value="{{ old('tanggal_mulai') }}" required>
                                                @error('tanggal_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tanggal_selesai">Tanggal Selesai Cuti <span
                                                        class="required">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                                    id="tanggal_selesai" name="tanggal_selesai"
                                                    value="{{ old('tanggal_selesai') }}" required>
                                                @error('tanggal_selesai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="duration-display" id="duration-display" style="display: none;">
                                                <strong><i class="fas fa-clock mr-2"></i>Durasi Cuti: <span
                                                        id="duration-text">0 hari</span></strong>
                                                <br><small class="text-muted">Tidak termasuk hari libur dan weekend</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keterangan">Keterangan/Alasan Cuti <span
                                                        class="required">*</span></label>
                                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                    rows="4" placeholder="Jelaskan alasan pengajuan cuti..." required>{{ old('keterangan') }}</textarea>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Minimal 10 karakter</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alamat Selama Cuti -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="alamat_cuti">Alamat Selama Cuti</label>
                                                <textarea class="form-control @error('alamat_cuti') is-invalid @enderror" id="alamat_cuti" name="alamat_cuti"
                                                    rows="3" placeholder="Alamat yang dapat dihubungi selama cuti...">{{ old('alamat_cuti') }}</textarea>
                                                @error('alamat_cuti')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
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
                                                    <strong>Lampiran yang diperlukan berdasarkan jenis cuti:</strong><br>
                                                    • Cuti Sakit: Surat keterangan dokter<br>
                                                    • Cuti Melahirkan: Surat keterangan dokter/bidan<br>
                                                    • Cuti Menikah: Undangan pernikahan<br>
                                                    • Cuti Keluarga Meninggal: Surat kematian
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
                                                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Cuti
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
                                    <h6 class="text-primary"><i class="fas fa-clock mr-2"></i>Waktu Pengajuan</h6>
                                    <p class="mb-0 small">Pengajuan cuti harus diajukan minimal 3 hari sebelum tanggal cuti
                                        dimulai.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-primary"><i class="fas fa-check-circle mr-2"></i>Proses Persetujuan
                                    </h6>
                                    <p class="mb-0 small">Pengajuan akan melalui persetujuan atasan langsung dan HRD.</p>
                                </div>

                                <div class="info-card mb-3">
                                    <h6 class="text-primary"><i class="fas fa-file-alt mr-2"></i>Dokumen Pendukung</h6>
                                    <p class="mb-0 small">Lampirkan dokumen pendukung sesuai jenis cuti yang diajukan.</p>
                                </div>

                                <div class="info-card">
                                    <h6 class="text-primary"><i class="fas fa-phone mr-2"></i>Kontak</h6>
                                    <p class="mb-0 small">Hubungi HRD (ext. 123) jika ada pertanyaan terkait pengajuan
                                        cuti.</p>
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
            if (pilihKaryawan && pilihKaryawan.tagName === 'SELECT') {
                pilihKaryawan.addEventListener('change', function() {
                    const employeeId = this.value;
                    if (employeeId) {
                        loadEmployeeInfo(employeeId);
                    } else {
                        clearEmployeeInfo();
                    }
                });

                // Load initial employee info if only one employee
                @if ($managedEmployees->count() == 1)
                    loadEmployeeInfo('{{ $managedEmployees->first()->id }}');
                @endif
            }

            function loadEmployeeInfo(employeeId) {
                // Show loading state
                const fields = ['nama_karyawan', 'nip', 'jabatan', 'unit_kerja', 'sisa_cuti'];
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
                        document.getElementById('sisa_cuti').value = data.sisa_cuti || '0 hari';
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
                document.getElementById('sisa_cuti').value = '';
            }

            // Calculate duration
            const startDate = document.getElementById('tanggal_mulai');
            const endDate = document.getElementById('tanggal_selesai');
            const durationDisplay = document.getElementById('duration-display');
            const durationText = document.getElementById('duration-text');

            function calculateDuration() {
                if (startDate.value && endDate.value) {
                    const start = new Date(startDate.value);
                    const end = new Date(endDate.value);

                    if (end >= start) {
                        let days = 0;
                        let current = new Date(start);

                        while (current <= end) {
                            // Skip weekends (Saturday = 6, Sunday = 0)
                            if (current.getDay() !== 0 && current.getDay() !== 6) {
                                days++;
                            }
                            current.setDate(current.getDate() + 1);
                        }

                        durationText.textContent = days + ' hari kerja';
                        durationDisplay.style.display = 'block';
                    } else {
                        durationDisplay.style.display = 'none';
                    }
                } else {
                    durationDisplay.style.display = 'none';
                }
            }

            startDate.addEventListener('change', calculateDuration);
            endDate.addEventListener('change', calculateDuration);

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
            document.getElementById('cutiForm').addEventListener('submit', function(e) {
                const keterangan = document.getElementById('keterangan').value;
                if (keterangan.length < 10) {
                    e.preventDefault();
                    alert('Keterangan minimal 10 karakter');
                    return false;
                }

                if (!startDate.value || !endDate.value) {
                    e.preventDefault();
                    alert('Tanggal mulai dan selesai cuti harus diisi');
                    return false;
                }

                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                if (end < start) {
                    e.preventDefault();
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                    return false;
                }
            });
        });
    </script>
@endsection
