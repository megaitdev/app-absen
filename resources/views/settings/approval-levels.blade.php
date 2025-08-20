@extends('layouts.app')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ $title }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="/dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="/settings">Settings</a></div>
                <div class="breadcrumb-item">Approval Levels</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Pengaturan Level Approval</h4>
                            <div class="card-header-action">
                                <button class="btn btn-primary" id="add-approval-level-btn">
                                    <i class="fas fa-plus"></i> Tambah Approval Level
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="approval-levels-table">
                                    <thead>
                                        <tr>
                                            <th>Supervisor</th>
                                            <th>Scope</th>
                                            <th>Status</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="approvalLevelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalLevelModalTitle">Tambah Approval Level</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="approvalLevelForm">
                <div class="modal-body">
                    <input type="hidden" id="approval-level-id" name="id">
                    
                    <div class="form-group">
                        <label for="supervisor_user_id">Supervisor <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="supervisor_user_id" name="supervisor_user_id" required>
                            <option value="">Pilih Supervisor</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="approval_type">Tipe Approval <span class="text-danger">*</span></label>
                        <select class="form-control" id="approval_type" name="approval_type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="unit">Unit</option>
                            <option value="divisi">Divisi</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="unit-group" style="display: none;">
                        <label for="unit_id">Unit <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="unit_id" name="unit_id">
                            <option value="">Pilih Unit</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="divisi-group" style="display: none;">
                        <label for="divisi_id">Divisi <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="divisi_id" name="divisi_id">
                            <option value="">Pilih Divisi</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="status-group" style="display: none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="approval-level-submit-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#approval-levels-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/settings/approval-levels/datatable',
        columns: [
            { data: 'supervisor_name', name: 'supervisor_name' },
            { data: 'scope', name: 'scope' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    // Load select options
    loadSupervisors();
    loadUnits();
    loadDivisis();

    // Add button click
    $('#add-approval-level-btn').click(function() {
        resetForm();
        $('#approvalLevelModalTitle').text('Tambah Approval Level');
        $('#status-group').hide();
        $('#approvalLevelModal').modal('show');
    });

    // Approval type change
    $('#approval_type').change(function() {
        const type = $(this).val();
        if (type === 'unit') {
            $('#unit-group').show();
            $('#divisi-group').hide();
            $('#unit_id').prop('required', true);
            $('#divisi_id').prop('required', false);
        } else if (type === 'divisi') {
            $('#unit-group').hide();
            $('#divisi-group').show();
            $('#unit_id').prop('required', false);
            $('#divisi_id').prop('required', true);
        } else {
            $('#unit-group').hide();
            $('#divisi-group').hide();
            $('#unit_id').prop('required', false);
            $('#divisi_id').prop('required', false);
        }
    });

    // Form submit
    $('#approvalLevelForm').submit(function(e) {
        e.preventDefault();
        
        const id = $('#approval-level-id').val();
        const url = id ? `/settings/approval-levels/${id}` : '/settings/approval-levels/store';
        const method = id ? 'PUT' : 'POST';
        
        const formData = new FormData(this);
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#approvalLevelModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Edit button click (delegated)
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.get(`/settings/approval-levels/${id}`, function(response) {
            if (response.success) {
                const data = response.data;
                
                $('#approval-level-id').val(data.id);
                $('#supervisor_user_id').val(data.supervisor_user_id).trigger('change');
                $('#approval_type').val(data.approval_type).trigger('change');
                
                if (data.approval_type === 'unit') {
                    $('#unit_id').val(data.unit_id).trigger('change');
                } else if (data.approval_type === 'divisi') {
                    $('#divisi_id').val(data.divisi_id).trigger('change');
                }
                
                $('#is_active').prop('checked', data.is_active);
                
                $('#approvalLevelModalTitle').text('Edit Approval Level');
                $('#status-group').show();
                $('#approvalLevelModal').modal('show');
            }
        });
    });

    // Delete button click (delegated)
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data approval level akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/settings/approval-levels/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire('Berhasil!', response.message, 'success');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    function resetForm() {
        $('#approvalLevelForm')[0].reset();
        $('#approval-level-id').val('');
        $('#unit-group').hide();
        $('#divisi-group').hide();
        $('#supervisor_user_id').val('').trigger('change');
        $('#unit_id').val('').trigger('change');
        $('#divisi_id').val('').trigger('change');
    }

    function loadSupervisors() {
        $.get('/settings/approval-levels/data/supervisors', function(response) {
            if (response.success) {
                const select = $('#supervisor_user_id');
                select.empty().append('<option value="">Pilih Supervisor</option>');
                
                response.data.forEach(function(item) {
                    select.append(`<option value="${item.id}">${item.nama} (${item.username})</option>`);
                });
            }
        });
    }

    function loadUnits() {
        $.get('/settings/approval-levels/data/units', function(response) {
            if (response.success) {
                const select = $('#unit_id');
                select.empty().append('<option value="">Pilih Unit</option>');
                
                response.data.forEach(function(item) {
                    select.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        });
    }

    function loadDivisis() {
        $.get('/settings/approval-levels/data/divisis', function(response) {
            if (response.success) {
                const select = $('#divisi_id');
                select.empty().append('<option value="">Pilih Divisi</option>');
                
                response.data.forEach(function(item) {
                    select.append(`<option value="${item.id}">${item.nama}</option>`);
                });
            }
        });
    }
});
</script>
@endpush
