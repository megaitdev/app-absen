@extends('layouts.app')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="/dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Workflow Approval</div>
                </div>
            </div>

            <div class="section-body">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="far fa-calendar-times"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Cuti</h4>
                                </div>
                                <div class="card-body">
                                    {{ $pending_cuti ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="far fa-clock"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Izin</h4>
                                </div>
                                <div class="card-body">
                                    {{ $pending_izin ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-business-time"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Lembur</h4>
                                </div>
                                <div class="card-body">
                                    {{ $pending_lembur ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Verifikasi</h4>
                                </div>
                                <div class="card-body">
                                    {{ $pending_verifikasi ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daftar Pending Approval</h4>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="workflowTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all"
                                            role="tab">
                                            Semua
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="cuti-tab" data-toggle="tab" href="#cuti" role="tab">
                                            Cuti
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="izin-tab" data-toggle="tab" href="#izin" role="tab">
                                            Izin
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="lembur-tab" data-toggle="tab" href="#lembur" role="tab">
                                            Lembur
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="verifikasi-tab" data-toggle="tab" href="#verifikasi"
                                            role="tab">
                                            Verifikasi
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="workflowTabContent">
                                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                                        <div id="all-content" class="mt-3">
                                            <!-- Content will be loaded via AJAX -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="cuti" role="tabpanel">
                                        <div id="cuti-content" class="mt-3">
                                            <!-- Content will be loaded via AJAX -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="izin" role="tabpanel">
                                        <div id="izin-content" class="mt-3">
                                            <!-- Content will be loaded via AJAX -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="lembur" role="tabpanel">
                                        <div id="lembur-content" class="mt-3">
                                            <!-- Content will be loaded via AJAX -->
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="verifikasi" role="tabpanel">
                                        <div id="verifikasi-content" class="mt-3">
                                            <!-- Content will be loaded via AJAX -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalTitle">Approval</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="approvalForm">
                    <div class="modal-body">
                        <input type="hidden" id="approval-type" name="type">
                        <input type="hidden" id="approval-id" name="id">
                        <input type="hidden" id="approval-action" name="action">

                        <div class="form-group">
                            <label for="approval-notes">Catatan</label>
                            <textarea class="form-control" id="approval-notes" name="notes" rows="3"
                                placeholder="Masukkan catatan (opsional)"></textarea>
                        </div>

                        <div class="form-group" id="rejection-reason-group" style="display: none;">
                            <label for="rejection-reason">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection-reason" name="reason" rows="3"
                                placeholder="Masukkan alasan penolakan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="approval-submit-btn">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load initial data
            loadPendingApprovals('all');

            // Tab click handlers
            $('#workflowTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('href').substring(1); // Remove #
                loadPendingApprovals(target);
            });

            // Load pending approvals
            function loadPendingApprovals(type) {
                const contentDiv = $(`#${type}-content`);
                contentDiv.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

                $.get('/workflow/pending-approvals', {
                    type: type
                }, function(response) {
                    if (response.success) {
                        let html = '';

                        // Process each type of data
                        Object.keys(response.data).forEach(function(dataType) {
                            const items = response.data[dataType];
                            if (items && items.length > 0) {
                                html += generateItemsHtml(dataType, items);
                            }
                        });

                        if (html === '') {
                            html = '<div class="alert alert-info">Tidak ada data pending approval</div>';
                        }

                        contentDiv.html(html);
                    } else {
                        contentDiv.html('<div class="alert alert-danger">Gagal memuat data</div>');
                    }
                }).fail(function() {
                    contentDiv.html(
                        '<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
                });
            }

            // Generate HTML for items
            function generateItemsHtml(type, items) {
                let html = `<h6 class="text-uppercase text-muted mb-3">${getTypeLabel(type)}</h6>`;

                items.forEach(function(item) {
                    html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-1">${item.employee ? item.employee.nama : 'N/A'}</h6>
                                <p class="text-muted mb-1">
                                    <small>
                                        <i class="fas fa-calendar"></i> ${formatDate(item.date)}
                                        ${item.jenis_cuti ? ' - ' + item.jenis_cuti.cuti : ''}
                                        ${item.jenis_izin ? ' - ' + item.jenis_izin.izin : ''}
                                        ${item.lembur ? ' - ' + item.lembur : ''}
                                    </small>
                                </p>
                                <p class="text-muted mb-1">
                                    <small>
                                        <i class="fas fa-user"></i> Diajukan oleh: ${item.submitted_by ? item.submitted_by.nama : 'N/A'}
                                        <i class="fas fa-clock ml-2"></i> ${formatDateTime(item.submitted_at)}
                                    </small>
                                </p>
                                ${item.keterangan ? `<p class="mb-0"><small><i class="fas fa-comment"></i> ${item.keterangan}</small></p>` : ''}
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="badge badge-warning mb-2">${getStatusLabel(item.status)}</span><br>
                                <div class="btn-group-vertical btn-group-sm">
                                    <button class="btn btn-info btn-sm detail-btn" data-type="${type}" data-id="${item.id}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    ${canApprove(item.status) ? `
                                            <button class="btn btn-success btn-sm approve-btn" data-type="${type}" data-id="${item.id}" data-action="approve">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-btn" data-type="${type}" data-id="${item.id}" data-action="reject">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                return html;
            }

            // Approve button click
            $(document).on('click', '.approve-btn', function() {
                const type = $(this).data('type');
                const id = $(this).data('id');
                const action = $(this).data('action');

                $('#approval-type').val(type);
                $('#approval-id').val(id);
                $('#approval-action').val(action);
                $('#approvalModalTitle').text('Setujui Pengajuan');
                $('#approval-submit-btn').text('Setujui').removeClass('btn-danger').addClass('btn-success');
                $('#rejection-reason-group').hide();
                $('#approval-notes').prop('required', false);

                $('#approvalModal').modal('show');
            });

            // Reject button click
            $(document).on('click', '.reject-btn', function() {
                const type = $(this).data('type');
                const id = $(this).data('id');
                const action = $(this).data('action');

                $('#approval-type').val(type);
                $('#approval-id').val(id);
                $('#approval-action').val(action);
                $('#approvalModalTitle').text('Tolak Pengajuan');
                $('#approval-submit-btn').text('Tolak').removeClass('btn-success').addClass('btn-danger');
                $('#rejection-reason-group').show();
                $('#rejection-reason').prop('required', true);

                $('#approvalModal').modal('show');
            });

            // Approval form submit
            $('#approvalForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const action = $('#approval-action').val();

                let url = '';
                if (action === 'approve') {
                    url =
                        '{{ Auth::user()->is_hrd ? '/workflow/approve-hrd' : '/workflow/approve-supervisor' }}';
                } else if (action === 'reject') {
                    url = '/workflow/reject';
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#approvalModal').modal('hide');

                            // Reload current tab
                            const activeTab = $('#workflowTabs .nav-link.active').attr('href')
                                .substring(1);
                            loadPendingApprovals(activeTab);

                            // Update counters
                            location.reload(); // Simple reload for now

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

            // Helper functions
            function getTypeLabel(type) {
                const labels = {
                    'cuti': 'Cuti',
                    'izin': 'Izin',
                    'lembur': 'Lembur',
                    'verifikasi': 'Verifikasi Absen'
                };
                return labels[type] || type;
            }

            function getStatusLabel(status) {
                const labels = {
                    'pending': 'Menunggu Persetujuan',
                    'approved_supervisor': 'Disetujui Atasan',
                    'approved_hrd': 'Disetujui HRD',
                    'rejected': 'Ditolak',
                    'cancelled': 'Dibatalkan'
                };
                return labels[status] || status;
            }

            function canApprove(status) {
                @if (Auth::user()->is_hrd)
                    return status === 'approved_supervisor';
                @elseif (Auth::user()->is_supervisor)
                    return status === 'pending';
                @else
                    return false;
                @endif
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('id-ID');
            }

            function formatDateTime(dateString) {
                return new Date(dateString).toLocaleString('id-ID');
            }
        });
    </script>
@endpush
