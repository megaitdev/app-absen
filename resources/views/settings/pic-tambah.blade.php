@extends('layouts.app')

@section('title', 'Blank Page')


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('settings') }}">Settings</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Tambah PIC</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('settings/pic/store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="employee_id">Karyawan</label>
                                        <select class="form-control select2 @error('employee_id') is-invalid @enderror"
                                            id="employee_id" name="employee_id">
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($employees as $e)
                                                <option value="{{ $e->id }}"
                                                    {{ old('employee_id') == $e->id ? 'selected' : '' }}>
                                                    {{ $e->nama }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_pic">Nama PIC</label>
                                        <input type="text" class="form-control @error('nama_pic') is-invalid @enderror"
                                            id="nama_pic" name="nama_pic" value="{{ old('nama_pic') }}">

                                        @error('nama_pic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="nomor_wa">Nomor WA</label>
                                        <input type="text" class="form-control @error('nomor_wa') is-invalid @enderror"
                                            id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}">

                                        @error('nomor_wa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username" value="{{ old('username') }}">

                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password">

                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="pic">PIC</label>
                                        <input type="text" class="form-control @error('pic') is-invalid @enderror"
                                            id="pic" name="pic" value="{{ old('pic') }}">

                                        @error('pic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="mengelola">Mengelola</label>
                                        <input type="text" class="form-control @error('mengelola') is-invalid @enderror"
                                            id="mengelola" name="mengelola" value="0 Karyawan">

                                    </div>
                                    <input type="hidden" id="selected_employee_ids" name="selected_employee_ids">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Pilih Karyawan</h4>
                            </div>
                            <div class="card-body">
                                <form id="filter-form">
                                    <div class="form-group">
                                        <label for="unit_filter">Filter Berdasarkan Unit</label>
                                        <select class="form-control select2" id="unit_filter" name="unit_filter[]" multiple>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" id="filter-button" class="btn btn-dark">Filter</button>
                                        <button type="button" id="reset-filter" class="btn btn-secondary">Reset</button>
                                        <button type="button" id="select-all" class="btn btn-info float-right">Select
                                            All</button>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="show-selected-only">
                                            <label class="custom-control-label" for="show-selected-only">Show Selected
                                                Only</label>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <div id="selected-count">Karyawan Terpilih: 0</div>
                                        <div id="total-count">Total Karyawan: 0</div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="employee-table">
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
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <input type="hidden" id="selected_employees" name="selected_employees">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Initialize select2
        $('.select2').select2();

        // Initialize selected employees array
        let selectedEmployees = [];
        let showSelected = false;

        console.log($('#unit_filter').val());

        // Initialize DataTable
        let table = $('#employee-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: base_url() + "settings/ajax/datatable/employees-pic",
                type: "GET",
                data: function(d) {
                    d.units = $('#unit_filter').val();
                    d.employees = selectedEmployees;
                    d.show = showSelected;
                    return d;
                }
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            columns: [{
                    data: null,
                    orderable: false,
                    render: function(data) {
                        let isChecked = selectedEmployees.includes(data.id) ?
                            'checked' : '';
                        return `<div class="custom-checkbox custom-control">
                                <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input employee-checkbox" id="checkbox-${data.id}" value="${data.id}" ${isChecked}>
                                <label for="checkbox-${data.id}" class="custom-control-label">&nbsp;</label>
                            </div>`;
                    }
                },
                {
                    data: 'nama'
                },
                {
                    data: 'nip'
                },
                {
                    data: 'unit',
                    render: function(data) {
                        return data ? data.unit : '';
                    }
                }
            ],
            drawCallback: function() {
                // Re-bind checkbox events after table redraw
                bindCheckboxEvents();

                // Check "Select All" if all visible checkboxes are checked
                updateSelectAllCheckbox();

                // Apply selected-only filter if active
                if ($('#show-selected-only').prop('checked')) {
                    applySelectedOnlyFilter();
                }

                // Update total count
                updateTotalCount();
            }
        });

        // Filter button click handler
        $('#filter-button').on('click', function() {
            console.log($('#unit_filter').val());

            table.ajax.reload();
        });

        // Reset filter button click handler
        $('#reset-filter').on('click', function() {
            $('#unit_filter').val(null).trigger('change');
            table.ajax.reload();
        });

        // Select all button click handler
        $('#select-all').on('click', function() {
            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            table.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });


            let allData = [];
            table.rows({
                search: 'applied'
            }).every(function() {
                let data = this.data();
                allData.push({
                    id: data.id,
                    nama: data.nama,
                    nip: data.nip,
                    unit: data.unit ? data.unit.unit : ''
                });
            });
            var tes = table.rows().data();
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

        // Checkbox-all click handler
        $('#checkbox-all').on('click', function() {
            let isChecked = $(this).prop('checked');

            // Check/uncheck all visible checkboxes
            $('.employee-checkbox:visible').prop('checked', isChecked);

            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            table.rows({
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
            table.ajax.reload();
        });

        // Handle change event on employee_id select
        $('#employee_id').on('change', function() {
            let selectedEmployeeId = $(this).val();

            // Update the value of nama_pic based on selected employee
            if (selectedEmployeeId) {
                let selectedEmployeeName = $('#employee_id option:selected').text().trim();
                $('#nama_pic').val(selectedEmployeeName);
            } else {
                $('#nama_pic').val('');
            }
        });


        // Function to apply filter to show only selected employees
        function applySelectedOnlyFilter() {
            // Create a custom filter function
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    let employeeId = parseInt(table.row(dataIndex).data().id);
                    return selectedEmployees.includes(employeeId);
                }
            );

            // Apply the filter
            table.draw();

            // Remove the custom filter function
            $.fn.dataTable.ext.search.pop();
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
            console.log('Selected employees:', selectedEmployees);
            console.log('Selected employees:', JSON.stringify(selectedEmployees));
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
            $('#selected-count').text('Karyawan Terpilih: ' + selectedEmployees.length);
            $('#mengelola').val(selectedEmployees.length + ' Karyawan');
        }

        // Function to update the total employees count display
        function updateTotalCount() {
            let totalEmployees = table.page.info().recordsTotal;
            $('#total-count').text('Total Karyawan: ' + totalEmployees);
        }

        // Handle form submission
        $('form').on('submit', function(e) {
            // Check if the form is the PIC form (contains username field)
            if ($(this).find('#username').length) {
                // If using multiple employees, ensure the hidden field has data
                if (selectedEmployees.length > 0) {
                    $('#selected_employee_ids').val(JSON.stringify(selectedEmployees));
                } else {
                    // Alert if no employees selected
                    e.preventDefault();
                    alert('Please select at least one employee!');
                }
            }
        });
    });
</script>
