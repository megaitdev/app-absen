@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/perizinan/perizinan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .form-header .icon {
            font-size: 24px;
            margin-right: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .form-group label {
            font-weight: 600;
        }

        .btn-submit {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            transform: translateY(-2px);
        }

        .info-card {
            background: #fbe9e7;
            border-left: 4px solid #dc3545;
        }

        .required {
            color: #dc3545;
        }

        #team-members-list .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .select2-container--default .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            padding: .375rem .75rem;
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card form-card">
                            <div class="form-header">
                                <div class="icon">
                                    <i class="fas fa-business-time"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">Form Pengajuan Lembur</h4>
                                    <p class="mb-0 mt-1 opacity-75">Lengkapi form untuk mengajukan lembur.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="lemburForm" action="{{ route('perizinan.lembur.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Informasi Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 style="color: #6f42c1;"><i class="fas fa-user mr-2"></i>Informasi Karyawan
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
                                                                {{ $employee->nama }} - {{ $employee->nip ?? 'No NIP' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" id="pilih_karyawan" name="employee_id"
                                                value="{{ $managedEmployees->first()->id ?? '' }}">
                                        @endif
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama_karyawan">Nama Karyawan</label>
                                                <input type="text" class="form-control" id="nama_karyawan"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->nama : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nip">NIP</label>
                                                <input type="text" class="form-control" id="nip"
                                                    value="{{ $managedEmployees->count() == 1 ? $managedEmployees->first()->nip ?? '-' : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Jenis Lembur -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h5 style="color: #dc3545;"><i class="fas fa-users mr-2"></i>Jenis Lembur</h5>
                                            <div class="form-group">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_lembur"
                                                        id="lembur_sendiri" value="sendiri" checked>
                                                    <label class="form-check-label" for="lembur_sendiri">Lembur
                                                        Sendiri</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_lembur"
                                                        id="lembur_tim" value="tim">
                                                    <label class="form-check-label" for="lembur_tim">Lembur Tim</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tim Section (Hidden by default) -->
                                    <div id="tim-section" class="d-none">
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="team_member_search">Tambah Anggota Tim</label>
                                                    <select id="team_member_search" class="form-control"
                                                        style="width: 100%;">
                                                    </select>
                                                    <small class="text-muted">Cari karyawan berdasarkan nama atau
                                                        NIP.</small>
                                                </div>
                                                <div id="team-members-list" class="mt-3">
                                                    <h6>Anggota Tim:</h6>
                                                    <ul class="list-group">
                                                        <!-- Team members will be appended here -->
                                                    </ul>
                                                </div>
                                                <input type="hidden" name="team_members" id="team_members_input">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Detail Lembur -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 style="color: #dc3545;"><i class="fas fa-clock mr-2"></i>Detail Lembur
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
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jam_mulai">Jam Mulai <span class="required">*</span></label>
                                                <input type="time"
                                                    class="form-control @error('jam_mulai') is-invalid @enderror"
                                                    id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}"
                                                    required>
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
                                                    rows="4" placeholder="Jelaskan pekerjaan yang akan dilakukan saat lembur..." required>{{ old('keterangan') }}</textarea>
                                                <small class="text-muted">Minimal 10 karakter.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lampiran -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="lampiran">Lampiran (Opsional)</label>
                                                <input type="file"
                                                    class="form-control-file @error('lampiran.*') is-invalid @enderror"
                                                    id="lampiran" name="lampiran[]" multiple>
                                                <small class="text-muted">Anda bisa melampirkan surat perintah lembur atau
                                                    dokumen pendukung lainnya.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="text-right">
                                        <a href="{{ url('perizinan') }}" class="btn btn-secondary mr-2">Batal</a>
                                        <button type="submit" class="btn btn-submit">Ajukan Lembur</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-info-circle mr-2"></i>Informasi Lembur</h4>
                            </div>
                            <div class="card-body">
                                <div class="info-card p-3 mb-3">
                                    <p class="mb-0 small">Pastikan pengajuan lembur telah disetujui oleh atasan Anda
                                        sebelum dilaksanakan.</p>
                                </div>
                                <div class="info-card p-3">
                                    <p class="mb-0 small">Lembur akan dihitung sesuai dengan kebijakan perusahaan yang
                                        berlaku.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Employee selection logic
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
                const initialEmployeeId = pilihKaryawan.value;
                if (initialEmployeeId) {
                    loadEmployeeInfo(initialEmployeeId);
                }
            }

            function loadEmployeeInfo(employeeId) {
                fetch(`{{ url('perizinan/ajax/employee-info') }}?employee_id=${employeeId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            return;
                        }
                        document.getElementById('nama_karyawan').value = data.nama || '';
                        document.getElementById('nip').value = data.nip || '-';
                    })
                    .catch(error => console.error('Error:', error));
            }

            function clearEmployeeInfo() {
                document.getElementById('nama_karyawan').value = '';
                document.getElementById('nip').value = '';
            }

            // Team overtime logic
            const timSection = document.getElementById('tim-section');
            const teamMembersInput = document.getElementById('team_members_input');
            const teamMembersList = document.querySelector('#team-members-list ul');
            let teamMemberIds = [];

            document.querySelectorAll('input[name="jenis_lembur"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'tim') {
                        timSection.classList.remove('d-none');
                    } else {
                        timSection.classList.add('d-none');
                    }
                });
            });

            $('#team_member_search').select2({
                placeholder: 'Cari Nama atau NIP Karyawan',
                allowClear: true,
                ajax: {
                    url: '{{ route('perizinan.ajax.search-employee') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.items
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
            });

            $('#team_member_search').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.id && !teamMemberIds.includes(data.id)) {
                    teamMemberIds.push(data.id);
                    updateTeamMembersList();
                    const newListItem = `
                        <li class="list-group-item" data-id="${data.id}">
                            <span>${data.text}</span>
                            <button type="button" class="btn btn-sm btn-danger remove-member-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </li>`;
                    teamMembersList.insertAdjacentHTML('beforeend', newListItem);
                }
                $(this).val(null).trigger('change');
            });

            teamMembersList.addEventListener('click', function(e) {
                if (e.target.closest('.remove-member-btn')) {
                    const listItem = e.target.closest('.list-group-item');
                    const memberId = listItem.getAttribute('data-id');
                    teamMemberIds = teamMemberIds.filter(id => id !== memberId);
                    listItem.remove();
                    updateTeamMembersList();
                }
            });

            function updateTeamMembersList() {
                teamMembersInput.value = JSON.stringify(teamMemberIds);
            }
        });
    </script>
@endsection
