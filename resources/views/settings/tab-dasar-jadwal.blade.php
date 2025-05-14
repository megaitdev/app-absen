<style>
    .nav-tabs .nav-link {
        color: #000000;
        border: none;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #6366F1;
        border-bottom: 2px solid #6366F1;
        background-color: transparent;
    }

    .toggle-collapse i {
        transition: transform 0.3s ease;
    }

    .toggle-collapse.collapsed i {
        transform: rotate(180deg);
    }

    .libur {
        color: #dc3545;
        font-weight: bold;
    }

    .card-header-action .badge {
        margin-right: 10px;
    }

    .table-sm td,
    .table-sm th {
        padding: 0.5rem;
    }

    .schedule-details {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .schedule-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .schedule-item:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .schedule-item:last-child {
        margin-bottom: 0;
    }

    .schedule-item h6 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .schedule-dates {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .schedule-dates i {
        color: #6366F1;
        margin-right: 5px;
    }

    .date-success {
        background: #d4edda;
        color: #155724;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .date-secondary {
        background: #e2e3e5;
        color: #202224;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .swal2-popup {
        border-radius: 12px;
    }
</style>



<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Overview Jadwal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 id="dasar-jadwal-count">0</h3>
                                <p class="mb-0">Total Jadwal</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 id="registered-employees-count">0</h3>
                                <p class="mb-0">Karyawan Terjadwal</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 id="employees-without-schedule-count">0</h3>
                                <p class="mb-0">Karyawan Belum Terjadwal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="schedules-tab" data-toggle="tab" href="#schedules" role="tab">Dasar
                    Jadwal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="employees-tab" data-toggle="tab" href="#employees" role="tab">Karyawan</a>
            </li>
        </ul>

        <div class="tab-content mt-4" id="myTabContent">
            <!-- Tab Dasar Jadwal -->
            <div class="tab-pane fade show active" id="schedules" role="tabpanel">
                <div class="row">
                </div>
            </div>

            <!-- Tab Karyawan -->
            <div class="tab-pane fade" id="employees" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="section-title mt-4">Semua Karyawan</h5>

                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Filter Divisi</label>
                                    <select id="division_filter_employee" class="form-control select2" multiple>
                                        {{-- Divisions will be populated dynamically --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Filter Unit</label>
                                    <select id="unit_filter_employee" class="form-control select2" multiple>
                                        {{-- Units will be populated dynamically --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cari</label>
                                    <input type="text" class="form-control" id="search-employee"
                                        placeholder="Cari karyawan...">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="all-employee-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NIP</th>
                                        <th>Unit</th>
                                        <th>Divisi</th>
                                        <th>Jadwal</th>
                                        <th>Mulai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Modal Add Employee to Schedule --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan ke Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-add-employee-schedule">
                    @csrf
                    <input type="hidden" id="schedule_id" name="schedule_id">
                    <input type="hidden" id="selected_employee_ids" name="selected_employee_ids">

                    <div class="row">
                        <!-- Left Section - Schedule Info -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <!-- Filter Section -->
                                    <div class="filter-section">
                                        <h6 class="text-primary mb-3">Filter Karyawan</h6>

                                        <div class="form-group">
                                            <label>Divisi</label>
                                            <select id="division_filter" class="form-control select2" multiple>
                                                {{-- Divisions will be populated dynamically --}}
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Unit</label>
                                            <select id="unit_filter" class="form-control select2" multiple>
                                                {{-- Units will be populated dynamically --}}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" id="filter-button"
                                                class="btn btn-sm btn-dark mr-2">Filter</button>
                                            <button type="button" id="reset-filter"
                                                class="btn btn-sm btn-secondary">Reset</button>
                                        </div>



                                        <div class="custom-control custom-checkbox mb-3">
                                            <input type="checkbox" class="custom-control-input"
                                                id="show-selected-only">
                                            <label class="custom-control-label" for="show-selected-only">Tampilkan
                                                Yang Terpilih</label>
                                        </div>

                                        <div class="mb-3">
                                            <button type="button" id="select-all" class="btn btn-info btn-sm">Select
                                                All Visible</button>
                                            <button type="button" id="deselect-all"
                                                class="btn btn-warning btn-sm">Deselect All Visible</button>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Mulai</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" class="form-control datepicker" id="start_date"
                                                    name="start_date" required>
                                            </div>
                                        </div>

                                        <!-- Employee Count Card -->
                                        <div class="card bg-light mb-4">
                                            <div class="card-body text-center">
                                                <h3 id="selected_employee_count">0</h3>
                                                <p class="mb-0">Karyawan Terpilih</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Section - Employee Table -->
                        <div class="col-md-8">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Daftar Karyawan</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Table Length and Search -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select id="table_length" class="form-control">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                    <option value="-1">All</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="search" id="table_search" class="form-control"
                                                    placeholder="Cari karyawan...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="add-employee-table" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="10%">
                                                        <div class="custom-checkbox custom-control">
                                                            <input type="checkbox" data-checkboxes="mygroup"
                                                                data-checkbox-role="dad" class="custom-control-input"
                                                                id="checkbox-all">
                                                            <label for="checkbox-all"
                                                                class="custom-control-label">&nbsp;</label>
                                                        </div>
                                                    </th>
                                                    <th>Nama</th>
                                                    <th>NIP</th>
                                                    <th>Unit</th>
                                                    <th>Divisi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-employees">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal View Employee --}}
<div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Daftar Karyawan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-light table-bordered table-striped" id="view-employee-table">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>NIP</th>
                                <th>Unit</th>
                                <th>Divisi</th>
                                <th>Jadwal</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedEmployees = [];
    let showSelected = false;
    let addEmployeeTable, viewEmployeeTable, allEmployeeTable;



    function dasarJadwalArea() {
        // Call statistics update on page load
        updateDashboardStatistics();

        // Refresh statistics every 30 seconds
        setInterval(updateDashboardStatistics, 30000);

        initializeScheduleInfo();

        // Refresh jadwal setiap 5 menit
        setInterval(initializeScheduleInfo, 30000);


        initAllEmployeeTable();
        $('.toggle-collapse').on('click', function() {
            $(this).toggleClass('collapsed');
        });
        $('.select2').select2();

        $('#select-all').on('click', function() {
            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            addEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });


            let allData = [];
            addEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                let data = this.data();
                allData.push({
                    id: data.id,
                    nama: data.nama,
                    nip: data.nip,
                    unit: data.unit ? data.unit.unit : '',
                    divisi: data.divisi ? data.divisi.divisi : ''
                });
            });
            var tes = addEmployeeTable.rows().data();
            console.log('All Table Data:', tes);

            // Check all visible checkboxes
            $('.employee-checkbox:visible').prop('checked', true);

            // Add all visible employee IDs to selectedEmployees array (if not already included)
            visibleEmployeeIds.forEach(function(id) {
                if (!selectedEmployees.includes(id)) {
                    selectedEmployees.push(id);
                }
            });

            // Update hidden input field
            updateSelectedEmployeesInput();

            // Update select all checkbox
            updateSelectAllCheckbox();

            // Update selected count
            updateSelectedCount();
        });

        // Deselect all button click handler
        $('#deselect-all').on('click', function() {
            // Uncheck all visible checkboxes
            $('.employee-checkbox:visible').prop('checked', false);

            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            addEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });

            // Remove all visible employee IDs from selectedEmployees array
            selectedEmployees = selectedEmployees.filter(function(id) {
                return !visibleEmployeeIds.includes(id);
            });

            // Update hidden input field
            updateSelectedEmployeesInput();

            // Update select all checkbox
            updateSelectAllCheckbox();

            // Update selected count
            updateSelectedCount();
        });
        $('#filter-button').on('click', function() {
            console.log($('#unit_filter').val());
            console.log($('#division_filter').val());

            addEmployeeTable.ajax.reload();
        });

        // Reset filter button click handler
        $('#reset-filter').on('click', function() {
            $('#unit_filter').val(null).trigger('change');
            $('#division_filter').val(null).trigger('change');
            addEmployeeTable.ajax.reload();
        });

        // Checkbox-all click handler
        $('#checkbox-all').on('click', function() {
            let isChecked = $(this).prop('checked');

            // Check/uncheck all visible checkboxes
            $('.employee-checkbox:visible').prop('checked', isChecked);

            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            addEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });

            if (isChecked) {
                // Add all visible employee IDs to selectedEmployees array (if not already included)
                visibleEmployeeIds.forEach(function(id) {
                    if (!selectedEmployees.includes(id)) {
                        selectedEmployees.push(id);
                    }
                });
            } else {
                // Remove all visible employee IDs from selectedEmployees array
                selectedEmployees = selectedEmployees.filter(function(id) {
                    return !visibleEmployeeIds.includes(id);
                });
            }

            // Update hidden input field
            updateSelectedEmployeesInput();

            // Update selected count
            updateSelectedCount();
        });

        // Show selected only toggle handler
        $('#show-selected-only').on('change', function() {
            if ($(this).prop('checked')) {
                showSelected = true;
            } else {
                showSelected = false;
            }
            console.log('showSelected:', showSelected);

            addEmployeeTable.ajax.reload();
        });

        // Save button click handler
        $('#save-employees').on('click', function() {
            let selectedCount = selectedEmployees.length;
            let startDate = $('#start_date').val();



            if (selectedCount === 0) {
                Swal.fire({
                    title: "Tidak Ada Karyawan Terpilih",
                    text: "Silakan pilih setidaknya satu karyawan untuk melanjutkan.",
                    icon: "info",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            Swal.fire({
                    title: "Simpan Perubahan?",
                    text: `Klik Ya untuk menyimpan perubahan pada jadwal dasar untuk ${selectedCount} karyawan yang terpilih. Jadwal akan dimulai pada tanggal ${startDate}!`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        // Save the changes
                        $.ajax({
                            url: base_url() + 'settings/ajax/dasar-jadwal/add-employees',
                            method: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                                selected_employees: JSON.stringify(selectedEmployees),
                                start_date: startDate,
                                schedule_id: $('#schedule_id').val(),
                            },
                            success: function(res) {
                                console.log('Response from server:', res);

                                if (res.success) {
                                    selectedEmployees = [];
                                    updateDashboardStatistics();
                                    initializeScheduleInfo();
                                    Swal.fire({
                                        title: "Perubahan Disimpan!",
                                        text: "Jadwal dasar untuk karyawan yang terpilih telah berhasil disimpan.",
                                        icon: "success",
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok'
                                    }).then(() => {
                                        $('#addEmployeeModal').modal('hide');
                                    });



                                } else {
                                    Swal.fire({
                                        title: "Gagal menyimpan perubahan!",
                                        text: "Terjadi kesalahan saat menyimpan perubahan.",
                                        icon: "error",
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok'
                                    });
                                }
                            },
                            error: function(error) {
                                console.error('Error saving schedule changes:',
                                    error);
                                swal("Gagal menyimpan perubahan!", {
                                    icon: "error",
                                });
                            }
                        });
                    }
                });
        })

    }

    function initAllEmployeeTable() {
        allEmployeeTable = $('#all-employee-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            dom: "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            ajax: {
                url: base_url() + "settings/ajax/datatable/dasar-jadwal/all-employee",
                type: "GET",
                data: function(d) {
                    d.units = $('#unit_filter_employee').val();
                    d.divisions = $('#division_filter_employee').val();
                    d.search.value = $('#search-employee').val();
                    return d;
                }
            },
            columns: [
                // Kolom untuk nomor urut
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                // Kolom untuk nama karyawan
                {
                    data: 'nama',
                    name: 'nama',
                    className: 'align-middle'
                },
                // Kolom untuk NIP karyawan
                {
                    data: 'nip',
                    name: 'nip',
                    className: 'align-middle'
                },
                // Kolom untuk unit karyawan
                {
                    data: 'unit',
                    name: 'unit.unit',
                    className: 'align-middle',
                    render: function(data) {
                        return data ? data.unit : '-';
                    }
                },
                // Kolom untuk divisi karyawan
                {
                    data: 'divisi',
                    name: 'divisi.divisi',
                    className: 'align-middle',
                    render: function(data) {
                        return data ? data.divisi : '-';
                    }
                },
                // Kolom untuk jadwal dasar aktif
                {
                    data: 'jadwal',
                    name: 'dasar_jadwal_active.schedule.schedule',
                    className: 'align-middle',
                    orderable: false
                },
                // Kolom untuk tanggal mulai
                {
                    data: 'start_date',
                    name: 'dasar_jadwal_active.start_date',
                    className: 'align-middle',
                    orderable: false
                },
                // Kolom untuk status aktif
                {
                    data: 'is_active',
                    name: 'dasar_jadwal_active.is_active',
                    className: 'align-middle text-center',
                    render: function(data) {
                        if (data === 1) {
                            return '<span class="badge badge-success">Aktif</span>';
                        }
                        return '<span class="badge badge-secondary">Tidak Aktif</span>';
                    },
                    orderable: false
                }
            ],
            order: [
                [1, 'asc']
            ]
        });

        // Handle filter changes
        $('#unit_filter_employee, #division_filter_employee').on('change', function() {
            allEmployeeTable.ajax.reload();
        });

        // Handle search
        $('#search-employee').on('keyup', function() {
            allEmployeeTable.ajax.reload();
        });
    }

    // Function to update dashboard statistics
    function updateDashboardStatistics() {
        $.ajax({
            url: base_url() + 'settings/ajax/jadwal/statistics',
            method: 'GET',
            success: function(response) {
                if (response) {
                    $('#dasar-jadwal-count').text(response.total_schedules || 0);
                    $('#registered-employees-count').text(response.total_employees || 0);
                    $('#employees-without-schedule-count').text(response
                        .employees_without_schedule || 0);
                }
            },
            error: function(error) {
                console.error('Error fetching schedule statistics:', error);
                // Set default values in case of error
                $('#dasar-jadwal-count').text('0');
                $('#registered-employees-count').text('0');
                $('#employees-without-schedule-count').text('0');
            }
        });
    }

    function showAddEmployeeModal(schedule_id) {
        $('#schedule_id').val(schedule_id);
        $("#addEmployeeModal").appendTo("body").modal("show");
        if (addEmployeeTable) {
            addEmployeeTable
                .destroy();
        }
        populateFilterDivisionsAndUnits(schedule_id);
        initAddEmployeeTable(schedule_id);
    }

    function showViewEmployeeModal(schedule_id) {
        $('#schedule-name').text('Jadwal: ' + schedule_id);
        $('#employee-count').text('Jumlah Karyawan: ' + selectedEmployees.length);
        $("#viewEmployeeModal").appendTo("body").modal("show");

        if (viewEmployeeTable) {
            viewEmployeeTable.destroy();
        }
        initViewEmployeeTable(schedule_id);
    }

    function initViewEmployeeTable(schedule_id) {
        viewEmployeeTable = $('#view-employee-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: base_url() + "settings/ajax/datatable/dasar-jadwal/view-employee/" + schedule_id,
                type: "GET",
                data: function(d) {
                    d.schedule_id = schedule_id;
                    d.units = $('#unit_filter').val();
                    d.divisions = $('#division_filter').val();
                    return d;
                }
            },
            pageLength: 10,
            columns: [{
                    data: null,
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama',
                    name: 'nama',
                    className: 'align-middle'
                },
                {
                    data: 'nip',
                    name: 'nip',
                    className: 'align-middle'
                },
                {
                    data: 'unit',
                    name: 'unit.unit',
                    className: 'align-middle',
                    render: function(data) {
                        return data ? data.unit : '-';
                    }
                },
                {
                    data: 'divisi',
                    name: 'divisi.divisi',
                    className: 'align-middle',
                    render: function(data) {
                        return data ? data.divisi : '-';
                    }
                },
                {
                    data: 'count_jadwal',
                    name: 'count_jadwal',
                    className: 'align-middle text-center',
                    orderable: false,
                    searchable: false,
                }
            ],
        });
    }

    function getJadwalKaryawan(employee_id) {
        $.ajax({
            url: base_url() + 'settings/ajax/dasar-jadwal/employee/' + employee_id,
            method: 'GET',
            success: function(res) {
                console.log(res);

                // Create a timeline display for employee's schedules
                // Menampilkan semua jadwal yang dimiliki karyawan
                let scheduleList = '';

                // Check if response is array of schedules


                if (Array.isArray(res)) {
                    res.forEach(schedule => {
                        let startDate = new Date(schedule.start_date);
                        let endDate = new Date(schedule.end_date);

                        let startDateStr = startDate.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });

                        let endDateStr = endDate.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });

                        let status = schedule.is_active === 1 ?
                            '<span class="date-success">Aktif</span>' :
                            '<span class="date-secondary">Tidak Aktif</span>';

                        scheduleList += `
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary">${schedule.schedule_name}</h6>
                                    ${status}
                                </div>
                                <div class="schedule-dates">
                                    <i class="fas fa-calendar-alt"></i>${startDateStr} - ${endDateStr}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    // Single schedule
                    let startDate = new Date(res.start_date);
                    let endDate = new Date(res.end_date);

                    let startDateStr = startDate.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    let endDateStr = endDate.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    let status = res.is_active === 1 ?
                        '<span class="text-success">Aktif</span>' :
                        '<span class="text-secondary">Tidak Aktif</span>';

                    scheduleList = `
                        <div class="schedule-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-primary">${res.schedule_name}</h6>
                                ${status}
                            </div>
                            <div class="schedule-dates">
                                <i class="fas fa-calendar-alt"></i>${startDateStr} - ${endDateStr}
                            </div>
                        </div>
                    `;
                }

                Swal.fire({
                    title: 'Riwayat Jadwal Karyawan',
                    html: `
                        <div class="schedule-details">
                            ${scheduleList}
                        </div>
                    `,
                    width: '500px',
                    customClass: {
                        container: 'schedule-modal',
                        popup: 'swal-wide',
                        content: 'text-left'
                    }
                });


            },
            error: function(error) {
                console.error('Error fetching employee schedule:', error);
            }
        });

    }

    function populateFilterDivisionsAndUnits(schedule_id) {
        $('#division_filter').empty();
        $('#units_filter').empty();
        $.ajax({
            url: base_url() + 'settings/ajax/dasar-jadwal/filter-employee/' + schedule_id,
            method: 'GET',
            success: function(res) {
                if (res.divisions) {
                    res.divisions.forEach(division => {
                        const option = $('<option>', {
                            value: division.id,
                            text: division.divisi
                        });
                        $('#division_filter').append(option);
                    });
                    $('#division_filter').trigger('change');
                }
                if (res.units) {
                    res.units.forEach(unit => {
                        const option = $('<option>', {
                            value: unit.id,
                            text: unit.unit
                        });
                        $('#unit_filter').append(option);
                    });
                    $('#unit_filter').trigger('change');
                }
            },
            error: function(error) {
                console.error('Error fetching divisions:', error);
            }
        });
    }




    // Dasar Jadwal Section
    function initializeScheduleInfo() {
        $.ajax({
            url: base_url() + 'settings/ajax/jadwal/info',
            method: 'GET',
            success: function(response) {
                if (response.schedules) {
                    const schedulesContainer = $('#schedules .row');
                    schedulesContainer.empty();

                    response.schedules.forEach(schedule => {
                        const scheduleCard = generateScheduleCard(schedule);
                        schedulesContainer.append(scheduleCard);
                    });

                    // Initialize tooltips and other components
                    $('[data-toggle="tooltip"]').tooltip();
                    $('.toggle-collapse').on('click', function() {
                        $(this).toggleClass('collapsed');
                    });
                }
            },
            error: function(error) {
                console.error('Error fetching schedule information:', error);
                iziToast.error({
                    title: 'Error!',
                    message: 'Gagal memuat informasi jadwal',
                    position: 'bottomRight'
                });
            }
        });
    }

    function generateScheduleCard(schedule) {
        const scheduleHtml = `
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">${schedule.name}</h5>
                        <div class="card-header-action">
                            <span class="badge badge-primary">${schedule.employee_count} Karyawan</span>
                            <button class="btn btn-sm btn-light text-primary toggle-collapse"
                                data-toggle="collapse" data-target="#schedule${schedule.id}Collapse"
                                aria-expanded="true" aria-controls="schedule${schedule.id}Collapse">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                        </div>
                    </div>
                    <div id="schedule${schedule.id}Collapse" class="collapse show">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Hari</th>
                                            <th>Shift</th>
                                            <th>Jam Kerja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${generateScheduleRows(schedule.details)}
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn btn-primary" onclick="showAddEmployeeModal('${schedule.id}')">
                                    <i class="fas fa-plus mr-1"></i> Tambah Karyawan
                                </div>
                                <div class="btn btn-outline-primary" onclick="showViewEmployeeModal('${schedule.id}')">
                                    Lihat Semua
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return scheduleHtml;
    }

    function generateScheduleRows(details) {
        const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        let rowsHtml = '';

        days.forEach(day => {
            const daySchedule = details.find(d => d.day === day) || {
                shift: '-',
                time: '<span class="libur">Libur</span>'
            };
            rowsHtml += `
                <tr>
                    <td>${day}</td>
                    <td>${daySchedule.shift}</td>
                    <td>${daySchedule.time}</td>
                </tr>
            `;
        });

        return rowsHtml;
    }




    // Initialize DataTable
    function initAddEmployeeTable(schedule_id) {
        addEmployeeTable = $('#add-employee-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            dom: 'rt<"bottom"ip>', // Hide default length and search
            ajax: {
                url: base_url() + "settings/ajax/datatable/dasar-jadwal/add-employee/" + schedule_id,
                type: "GET",
                data: function(d) {
                    d.schedule_id = schedule_id;
                    d.units = $('#unit_filter').val();
                    d.divisions = $('#division_filter').val();
                    d.employees = selectedEmployees;
                    d.show = showSelected;
                    d.search = {
                        value: $('#table_search').val()
                    }; // Add search value
                    return d;
                }
            },
            pageLength: 10,
            columns: [{
                    data: null,
                    orderable: false,
                    render: function(data) {
                        let isChecked = selectedEmployees.includes(data.id) ?
                            'checked' :
                            '';
                        return `<div class="custom-checkbox custom-control">
                                    <input type="checkbox" class="custom-control-input employee-checkbox"
                                            id="checkbox-${data.id}" value="${data.id}" ${isChecked}>
                                    <label for="checkbox-${data.id}" class="custom-control-label">&nbsp;</label>
                                </div>`;
                    }
                },
                {
                    data: 'nama',
                    name: 'nama',
                },
                {
                    data: 'nip',
                    name: 'nip',
                },
                {
                    data: 'unit',
                    name: 'unit.unit',
                    render: function(data) {
                        return data ? data.unit : '-';
                    }
                },
                {
                    data: 'divisi',
                    name: 'divisi.divisi',
                    render: function(data) {
                        return data ? data.divisi : '-';
                    }
                }
            ],
            drawCallback: function() {
                bindCheckboxEvents();
                updateSelectAllCheckbox();
                updateSelectedCount();
            }
        });

        // Handle custom length change
        $('#table_length').on('change', function() {
            addEmployeeTable.page.len($(this).val()).draw();
        });

        // Handle custom search
        $('#table_search').on('keyup', function() {
            addEmployeeTable.search($(this).val()).draw();
        });
    }


    // Function to bind individual checkbox events
    function bindCheckboxEvents() {
        $('.employee-checkbox').on('click', function() {
            let employeeId = parseInt($(this).val());
            let isChecked = $(this).prop('checked');

            if (isChecked) {
                // Add employee ID to selectedEmployees array if not already included
                if (!selectedEmployees.includes(employeeId)) {
                    selectedEmployees.push(employeeId);
                }
            } else {
                // Remove employee ID from selectedEmployees array
                selectedEmployees = selectedEmployees.filter(function(id) {
                    return id !== employeeId;
                });
            }

            // Update hidden input field
            updateSelectedEmployeesInput();

            // Update select all checkbox
            updateSelectAllCheckbox();

            // Update selected count
            updateSelectedCount();

            // If "show selected only" is active, reapply the filter
            if ($('#show-selected-only').prop('checked')) {
                applySelectedOnlyFilter();
            }
        });
    }

    // Function to update the hidden input field with selected employee IDs
    function updateSelectedEmployeesInput() {
        $('#selected_employee_ids').val(JSON.stringify(selectedEmployees));
    }

    // Function to update the "select all" checkbox based on visible checkboxes state
    function updateSelectAllCheckbox() {
        let allChecked = true;
        $('.employee-checkbox:visible').each(function() {
            if (!$(this).prop('checked')) {
                allChecked = false;
                return false; // Break loop
            }
        });

        $('#checkbox-all').prop('checked', allChecked);
    }

    // Function to update the selected employees count display
    function updateSelectedCount() {
        $('#selected_employee_count').text(selectedEmployees.length);
    }


    // Function to apply filter to show only selected employees
    function applySelectedOnlyFilter() {
        // Create a custom filter function
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let employeeId = parseInt(addEmployeeTable.row(dataIndex).data().id);
                return selectedEmployees.includes(employeeId);
            }
        );

        // Apply the filter
        addEmployeeTable.draw();

        // Remove the custom filter function
        $.fn.dataTable.ext.search.pop();
    }
</script>



<div class="row">
    <div class="session">
        @if (session('success-jadwal'))
            <input type="text" id="alert-success-jadwal" value="1" hidden></input>
        @endif
        @if (session('updated-jadwal'))
            <input type="text" id="alert-updated-jadwal" value="1" hidden></input>
        @endif
        @if ($errors->any(['jadwal']))
            <input type="text" id="alert-modal-jadwal" value="{{ old('shcedule_id') }}" hidden></input>
        @endif
    </div>
</div>
