document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init Table Employee
        initTable();

        // Search Employee Need Update
        $("#search-employee-need-update").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableEmployeeNeedUpdate.search(value).draw();
        });

        // Search Employee Ftm
        $("#search-employee-ftm").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableEmployeeFtm.search(value).draw();
        });

        // Show modal error Employee Ftm
        isModalEmployeeFtmError = $(`#alert-modal-edit-employee-ftm`).val();
        if (isModalEmployeeFtmError > 0) {
            editEmployeeFtm(isModalEmployeeFtmError);
        }

        // Show modal error Employee Need Update
        isModalEmployeeNeedUpdateError = $(
            `#alert-modal-edit-employee-need-update`
        ).val();
        if (isModalEmployeeNeedUpdateError > 0) {
            editEmployeeNeedUpdate(isModalEmployeeNeedUpdateError);
        }

        // Alert Updated Employee Ftm
        isUpdatedEmployeeFtm = $(`#alert-success-ftm`).val();
        if (isUpdatedEmployeeFtm == 1) {
            alertUpdatedEmployee();
        }

        // Alert Updated Employee Need Update
        isUpdatedEmployeeNeedUpdate = $(`#alert-success-need-update`).val();
        if (isUpdatedEmployeeNeedUpdate == 1) {
            alertUpdatedEmployee();
        }
    });
});

let tableEmployeeNeedUpdate, tableEmployeeFtm;
function initTable() {
    // Init Table Employee
    tableEmployeeNeedUpdate = $(".table-employee-need-update").DataTable({
        processing: true,
        serverSide: true,
        ajax: base_url() + `employee/ajax/datatable/need-update`,
        dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "align-middle text-center",
            },
            {
                data: "nama",
                name: "nama",
                className: "align-middle text-left",
            },
            {
                data: "nip",
                name: "nip",
                className: "align-middle text-left",
            },
            {
                data: "_pin",
                name: "pin",
                className: "align-middle text-center",
            },
            {
                data: "divisi.divisi",
                name: "divisi.divisi",
                className: "align-middle text-right",
            },
            {
                data: "unit.unit",
                name: "unit.unit",
                className: "align-middle text-right",
            },
            {
                data: "action",
                name: "action",
                className: "align-middle text-right",
                orderable: false,
                searchable: false,
            },
        ],
        columnDefs: [],
        order: [[1, "asc"]],
    });

    // Init Table Employee FTM
    tableEmployeeFtm = $(".table-employee-ftm").DataTable({
        processing: true,
        serverSide: true,
        ajax: base_url() + `employee/ajax/datatable/ftm-all`,
        dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
        columns: [
            {
                data: "DT_RowIndex",
                orderable: false,
                searchable: false,
                className: "align-middle text-center",
            },
            {
                data: "alias",
                name: "alias",
                className: "align-middle text-left",
            },
            {
                data: "nik",
                name: "nik",
                className: "align-middle text-left",
            },
            {
                data: "pin",
                name: "pin",
                className: "align-middle text-left",
            },
            {
                data: "cabang.cab_name",
                name: "cabang.cab_name",
                className: "align-middle text-right",
            },
            {
                data: "departemen.dept_name",
                name: "departemen.dept_name",
                className: "align-middle text-right",
            },
            {
                data: "_is_sync",
                className: "align-middle text-center",
                orderable: false,
                searchable: false,
            },
            {
                data: "action",
                name: "action",
                className: "align-middle text-right",
                orderable: false,
                searchable: false,
            },
        ],
        order: [[1, "asc"]],
        columnDefs: [],
    });
}

function changeLengthNeedUpdate(length) {
    tableEmployeeNeedUpdate.page.len(length).draw();
    $(".length-info-need-update").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".length-list-need-update a").removeClass("active");
    $(`.di-need-update-${length}`).addClass("active");
}
function changeLengthFtm(length) {
    tableEmployeeFtm.page.len(length).draw();
    $(".length-info-ftm").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".length-list-ftm a").removeClass("active");
    $(`.di-ftm-${length}`).addClass("active");
}

let isModalEmployeeFtmError = false;
let isUpdatedEmployeeFtm = false;
function editEmployeeFtm(id) {
    $("#modal-edit-employee-ftm").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + `employee/ftm/${id}`,
        success: function (res) {
            $("#modal-edit-employee-ftm form").attr(
                "action",
                base_url() + `employee/ftm/edit/${id}`
            );
            $("#edit-id-employee-ftm").val(res.emp_id_auto);
            $("#edit-nik-employee-ftm").val(res.nik);
            $("#edit-pin-employee-ftm").val(res.pin);
            $("#edit-alias-employee-ftm").val(res.alias);
            $("#edit-cabang-employee-ftm")
                .val(res.cab_id_auto)
                .trigger("change");
            $("#edit-departemen-employee-ftm")
                .val(res.dept_id_auto)
                .trigger("change");
        },
    });
}

let isModalEmployeeNeedUpdateError = false;
let isUpdatedEmployeeNeedUpdate = false;
function editEmployeeNeedUpdate(id) {
    $("#modal-edit-employee-need-update").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + `employee/need-update/${id}`,
        success: function (res) {
            $("#modal-edit-employee-need-update form").attr(
                "action",
                base_url() + `employee/need-update/edit/${id}`
            );
            $("#edit-id-employee-need-update").val(res.id);
            $("#edit-nip-employee-need-update").val(res.nip);
            $("#edit-pin-employee-need-update").val(res.pin);
            $("#edit-nama-employee-need-update").val(res.nama);
            $("#edit-divisi-employee-need-update")
                .val(res.divisi.id)
                .trigger("change");
            $("#edit-unit-employee-need-update")
                .val(res.unit.id)
                .trigger("change");
        },
    });
}

function alertUpdatedEmployee() {
    iziToast.info({
        title: `Updated!`,
        message: `Employee has been updated.`,
        position: "bottomRight",
        timeout: 1680,
    });
}
