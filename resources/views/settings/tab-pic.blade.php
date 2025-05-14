<div class="row">
    <div class="col-lg-12">
        <div class="card card-dark">
            <div class="card-header">
                <h4>List PIC</h4>
                <div class="card-header-action">
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('settings/pic/tambah') }}" class="btn btn-outline-dark pt-1 mx-1">
                            <i class="fa fa-plus mr-1"></i> Tambah
                        </a>
                        <div class="search-container mx-1">
                            <input type="text" class="search-input-dark" id="search-pic" placeholder="Search">
                            <button class="search-button">
                                <svg class="search-icon-dark" viewBox="0 0 24 24">
                                    <path
                                        d="M21.71 20.29l-5.01-5.01C17.54 13.68 18 11.91 18 10c0-4.41-3.59-8-8-8S2 5.59 2 10s3.59 8 8 8c1.91 0 3.68-0.46 5.28-1.3l5.01 5.01c0.39 0.39 1.02 0.39 1.41 0C22.1 21.32 22.1 20.68 21.71 20.29zM10 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6S13.31 16 10 16z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-light table-bordered table-pic">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>Nama PIC</th>
                                <th>Username</th>
                                <th>PIC</th>
                                <th>Mengelola</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Karyawan yang Dikelola --}}
<div class="modal fade" id="modal-karyawan-pic">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-dark">Karyawan yang Dikelola</h4>
                <i class="fas fa-users mr-1 float-end text-dark" style="font-size: 1.5em"></i>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nama-pic">Nama PIC</label>
                    <input type="text" class="form-control" id="show-nama-pic" disabled>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-light table-bordered table-karyawan-pic">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>NIP</th>
                                <th>Nama Karyawan</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody id="karyawan-list-body">
                            <!-- Data karyawan akan dimuat di sini -->
                        </tbody>
                    </table>
                </div>
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-light float-right" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit PIC --}}
<div class="modal fade" id="modal-edit-pic">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="text-dark">Edit PIC</h4>
                <i class="fas fa-user-edit mr-1 float-end text-dark" style="font-size: 1.5em"></i>
            </div>
            <div class="modal-body">
                <form id="form-edit-pic" method="post">
                    @csrf
                    <input type="hidden" id="edit_pic_id" name="id">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_employee_id">Karyawan</label>
                                <select class="form-control select2" id="edit_employee_id" name="employee_id">
                                    <option value="">Pilih Karyawan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_nama_pic">Nama PIC</label>
                                <input type="text" class="form-control" id="edit_nama_pic" name="nama_pic">
                            </div>
                            <div class="form-group">
                                <label for="edit_nomor_wa">Nomor WA</label>
                                <input type="text" class="form-control" id="edit_nomor_wa" name="nomor_wa">
                            </div>
                            <div class="form-group">
                                <label for="edit_username">Username</label>
                                <input type="text" class="form-control" id="edit_username" name="username">
                            </div>
                            <div class="form-group">
                                <label for="edit_password">Password</label>
                                <input type="password" class="form-control" id="edit_password" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                            </div>
                            <div class="form-group">
                                <label for="edit_pic">PIC</label>
                                <input type="text" class="form-control" id="edit_pic" name="pic">
                            </div>
                            <div class="form-group">
                                <label for="edit_mengelola">Mengelola</label>
                                <input type="text" class="form-control" id="edit_mengelola" name="mengelola"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_unit_filter">Filter Berdasarkan Unit</label>
                                <select class="form-control select2" id="edit_unit_filter" name="edit_unit_filter[]"
                                    multiple>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="button" id="edit_filter_button" class="btn btn-dark">Filter</button>
                                <button type="button" id="edit_reset_filter"
                                    class="btn btn-secondary">Reset</button>
                                <button type="button" id="edit_select_all" class="btn btn-info float-right">Select
                                    All</button>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit_show_selected_only">
                                    <label class="custom-control-label" for="edit_show_selected_only">Show Selected
                                        Only</label>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <div id="edit_selected_count">Karyawan Terpilih: 0</div>
                                <div id="edit_total_count">Total Karyawan: 0</div>
                            </div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped" id="edit_employee_table">
                                    <thead>
                                        <tr>
                                            <th width="10%">
                                                <div class="custom-checkbox custom-control">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="edit_checkbox_all">
                                                    <label for="edit_checkbox_all"
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
                            <input type="hidden" id="edit_selected_employee_ids" name="selected_employee_ids">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark float-right">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="session">
        @if (session('success-pic'))
            <input type="text" id="alert-success-pic" value="1" hidden></input>
        @endif
        @if (session('updated-pic'))
            <input type="text" id="alert-updated-pic" value="1" hidden></input>
        @endif
        @if ($errors->any(['pic']))
            <input type="text" id="alert-modal-pic" value="{{ old('shcedule_id') }}" hidden></input>
        @endif
    </div>
</div>


<script>
    // Pic Area
    let isSubmitedPic = false;
    let isUpdatedPic = false;
    let isModalErrorPic = false;

    function picArea() {
        // Alert Submited
        isSubmitedPic = $(`#alert-success-pic`).val();
        if (isSubmitedPic == 1) {
            alertSubmitedPic();
        }

        // Alert Updated
        isUpdatedPic = $(`#alert-updated-pic`).val();
        if (isUpdatedPic == 1) {
            alertUpdatedPic();
        }

        // Show modal error
        isModalErrorPic = $(`#alert-modal-pic`).val();
        if (isModalErrorPic > 0) {
            editPic(isModalErrorPic);
        }

        // Search Pic
        $("#search-pic").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            tablePic.search(value).draw();
        });

        // Delete PIC button handler
        $(document).on('click', '.btn-delete-pic', function() {
            let id = $(this).data('id');
            if (confirm('Apakah Anda yakin ingin menghapus PIC ini?')) {
                $.ajax({
                    url: base_url() + "settings/pic/delete/" + id,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('.table-pic').DataTable().ajax.reload();
                            alert('PIC berhasil dihapus!');
                        } else {
                            alert('Gagal menghapus PIC!');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Gagal menghapus PIC!');
                    }
                });
            }
        });

        // Handle edit form submission
        $('#form-edit-pic').on('submit', function(e) {
            e.preventDefault();

            // Get the form action URL
            let url = base_url() + "settings/pic/edit";

            // Get form data
            let formData = $(this).serialize();

            // Submit form via AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(res) {
                    // Close modal
                    $('#modal-edit-pic').modal('hide');

                    // Reload PIC table
                    $('.table-pic').DataTable().ajax.reload();

                    alertUpdatedPic();

                    // Show success alert
                },
                error: function(xhr) {
                    console.error('Error:', xhr);

                    // Handle validation errors
                    if (xhr.status === 422) {
                        let errors = xhr.resJSON.errors;
                        let errorMessage = 'Terjadi kesalahan:\n';

                        // Combine all error messages
                        for (let field in errors) {
                            errorMessage += errors[field][0] + '\n';
                        }

                        alert(errorMessage);
                    } else {
                        alert('Gagal memperbarui PIC!');
                    }
                }
            });
        });

        // Filter button click handler
        $('#edit_filter_button').on('click', function() {
            if (editEmployeeTable) {
                editEmployeeTable.ajax.reload();
            }
        });

        // Reset filter button click handler
        $('#edit_reset_filter').on('click', function() {
            $('#edit_unit_filter').val(null).trigger('change');
            if (editEmployeeTable) {
                editEmployeeTable.ajax.reload();
            }
        });

        // Select all button click handler
        $('#edit_select_all').on('click', function() {
            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            editEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });

            // Check all visible checkboxes
            $('.edit-employee-checkbox:visible').prop('checked', true);

            // Add all visible employee IDs to editSelectedEmployees array (if not already included)
            visibleEmployeeIds.forEach(function(id) {
                if (!editSelectedEmployees.includes(id)) {
                    editSelectedEmployees.push(id);
                }
            });

            // Update hidden input field
            updateEditSelectedEmployeesInput();

            // Update select all checkbox
            updateEditSelectAllCheckbox();

            // Update selected count
            updateEditSelectedCount();
        });

        // Checkbox-all click handler
        $('#edit_checkbox_all').on('click', function() {
            let isChecked = $(this).prop('checked');

            // Check/uncheck all visible checkboxes
            $('.edit-employee-checkbox:visible').prop('checked', isChecked);

            // Get all employee IDs currently visible in the table
            let visibleEmployeeIds = [];

            editEmployeeTable.rows({
                search: 'applied'
            }).every(function() {
                visibleEmployeeIds.push(this.data().id);
            });

            if (isChecked) {
                // Add all visible employee IDs to editSelectedEmployees array (if not already included)
                visibleEmployeeIds.forEach(function(id) {
                    if (!editSelectedEmployees.includes(id)) {
                        editSelectedEmployees.push(id);
                    }
                });
            } else {
                // Remove all visible employee IDs from editSelectedEmployees array
                editSelectedEmployees = editSelectedEmployees.filter(function(id) {
                    return !visibleEmployeeIds.includes(id);
                });
            }

            // Update hidden input field
            updateEditSelectedEmployeesInput();

            // Update selected count
            updateEditSelectedCount();
        });

        // Show selected only toggle handler
        $('#edit_show_selected_only').on('change', function() {
            editShowSelected = $(this).prop('checked');
            if (editEmployeeTable) {
                editEmployeeTable.ajax.reload();
            }
        });

        // Handle change event on employee_id select
        $('#edit_employee_id').on('change', function() {
            let selectedEmployeeId = $(this).val();

            // Update the value of nama_pic based on selected employee
            if (selectedEmployeeId) {
                let selectedEmployeeName = $('#edit_employee_id option:selected').text()
                    .trim();
                $('#edit_nama_pic').val(selectedEmployeeName);
            }
        });
    }

    function alertSubmitedPic() {
        iziToast.success({
            title: `Submited!`,
            message: `Pic has been created.`,
            position: "bottomRight",
            timeout: 1680,
        });
    }

    function alertUpdatedPic() {
        iziToast.info({
            title: `Updated!`,
            message: `Pic has been updated.`,
            position: "bottomRight",
            timeout: 1680,
        });
    }

    function deletePic(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#6777ef",
            cancelButtonColor: "#fc544b",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "get",
                    url: base_url() + `settings/pic/delete/${id}`,
                    success: function(res) {
                        iziToast.success({
                            title: `Deleted!`,
                            message: `Pic has been deleted.`,
                            position: "bottomRight",
                            timeout: 1680,
                        });
                        reloadTablePic();
                    },
                });
            }
        });
    }

    function reloadTablePic() {
        tablePic.ajax.url(base_url() + `settings/ajax/datatable/pic`).load();
    }

    function showEmployees(id) {
        $.ajax({
            url: base_url() + "settings/pic/ajax/show/" + id,
            type: "GET",
            success: function(res) {
                // Set nama PIC
                $('#show-nama-pic').val(res.pic.nama);

                // Clear existing table rows
                $('#karyawan-list-body').empty();

                // Add new rows for each employee
                res.employees.forEach(function(employee, index) {
                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${employee.nip}</td>
                            <td>${employee.nama}</td>
                            <td>${employee.unit ? employee.unit.unit : '-'}</td>
                        </tr>
                    `;
                    $('#karyawan-list-body').append(row);
                });

                // Show the modal
                $("#modal-karyawan-pic").appendTo("body").modal("show");
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Failed to load employee data');
            }
        });
    }

    function editPic(id) {
        // Reset form
        $('#form-edit-pic')[0].reset();
        $('#edit_pic_id').val(id);

        // Clear selected employees array
        editSelectedEmployees = [];

        // Set form action
        $('#form-edit-pic').attr('action', base_url() + 'settings/pic/update/' + id);

        // Load PIC data
        $.ajax({
            url: base_url() + "settings/pic/ajax/edit/" + id,
            type: "GET",
            success: function(res) {
                $('#edit_employee_id').empty();
                res.all_employees.forEach(function(emp) {
                    let selected = (emp.id == res.pic.employee_id) ? 'selected' : '';
                    $('#edit_employee_id').append(
                        `<option value="${emp.id}" ${selected}>${emp.nama}</option>`);
                });

                // Initialize select2
                $('#edit_employee_id').select2({
                    dropdownParent: $('#modal-edit-pic')
                });

                // Populate form data
                $('#edit_nama_pic').val(res.pic.nama);
                $('#edit_nomor_wa').val(res.pic.nomor_wa);
                $('#edit_username').val(res.pic.username);
                $('#edit_pic').val(res.pic.pic);
                $('#edit_mengelola').val(res.pic.mengelola);

                // Populate unit filter
                $('#edit_unit_filter').empty();
                res.units.forEach(function(unit) {
                    $('#edit_unit_filter').append(
                        `<option value="${unit.id}">${unit.unit}</option>`);
                });

                // Initialize select2 for unit filter
                $('#edit_unit_filter').select2({
                    dropdownParent: $('#modal-edit-pic')
                });

                // Load managed employees into selectedEmployees array
                editSelectedEmployees = res.managed_employees.map(function(emp) {
                    return emp;
                });

                // Update selected employees input
                $('#edit_selected_employee_ids').val(JSON.stringify(editSelectedEmployees));

                // Update selected count
                updateEditSelectedCount();

                // Initialize employee table
                if (editEmployeeTable) {
                    editEmployeeTable.destroy();
                }

                initializeEditEmployeeTable();

                // Show the modal
                $("#modal-edit-pic").appendTo("body").modal("show");
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Failed to load PIC data');
            }
        });
    }

    // Global variables for edit modal
    let editSelectedEmployees = [];
    let editShowSelected = false;
    let editEmployeeTable;

    function initializeEditEmployeeTable() {
        editEmployeeTable = $('#edit_employee_table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: base_url() + "settings/ajax/datatable/employees-pic",
                type: "GET",
                data: function(d) {
                    d.units = $('#edit_unit_filter').val();
                    d.employees = editSelectedEmployees;
                    d.show = editShowSelected;
                    return d;
                }
            },
            pageLength: 5,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            columns: [{
                    data: null,
                    orderable: false,
                    render: function(data) {
                        let isChecked = editSelectedEmployees.includes(data.id) ?
                            'checked' : '';
                        return `<div class="custom-checkbox custom-control">
                                <input type="checkbox" data-checkboxes="editgroup" class="custom-control-input edit-employee-checkbox" id="edit-checkbox-${data.id}" value="${data.id}" ${isChecked}>
                                <label for="edit-checkbox-${data.id}" class="custom-control-label">&nbsp;</label>
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
                bindEditCheckboxEvents();

                // Update select all checkbox
                updateEditSelectAllCheckbox();

                // Update total count
                updateEditTotalCount();
            }
        });
    }



    // Function to bind individual checkbox events for edit modal
    function bindEditCheckboxEvents() {
        $('.edit-employee-checkbox').on('click', function() {
            let employeeId = parseInt($(this).val());
            let isChecked = $(this).prop('checked');

            if (isChecked) {
                // Add employee ID to editSelectedEmployees array if not already included
                if (!editSelectedEmployees.includes(employeeId)) {
                    editSelectedEmployees.push(employeeId);
                }
            } else {
                // Remove employee ID from editSelectedEmployees array
                editSelectedEmployees = editSelectedEmployees.filter(function(id) {
                    return id !== employeeId;
                });
            }

            // Update hidden input field
            updateEditSelectedEmployeesInput();

            // Update select all checkbox
            updateEditSelectAllCheckbox();

            // Update selected count
            updateEditSelectedCount();

            // If "show selected only" is active, reapply the filter
            if ($('#edit_show_selected_only').prop('checked')) {
                editEmployeeTable.ajax.reload();
            }
        });
    }

    // Function to update the hidden input field with selected employee IDs
    function updateEditSelectedEmployeesInput() {
        editSelectedEmployees.sort((a, b) => a - b);
        $('#edit_selected_employee_ids').val(JSON.stringify(editSelectedEmployees));
    }

    // Function to update the "select all" checkbox based on visible checkboxes state
    function updateEditSelectAllCheckbox() {
        let allChecked = true;
        $('.edit-employee-checkbox:visible').each(function() {
            if (!$(this).prop('checked')) {
                allChecked = false;
                return false; // Break loop
            }
        });

        $('#edit_checkbox_all').prop('checked', allChecked);
    }

    // Function to update the selected employees count display
    function updateEditSelectedCount() {
        $('#edit_selected_count').text('Karyawan Terpilih: ' + editSelectedEmployees.length);
        $('#edit_mengelola').val(editSelectedEmployees.length + ' Karyawan');
    }

    // Function to update the total employees count display
    function updateEditTotalCount() {
        if (editEmployeeTable) {
            let totalEmployees = editEmployeeTable.page.info().recordsTotal;
            $('#edit_total_count').text('Total Karyawan: ' + totalEmployees);
        }
    }
</script>
