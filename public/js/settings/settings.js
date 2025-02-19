document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init tabActive
        tabActive = $("li a.active").attr("href").substring(1);

        // Event on click tab
        $(".nav-pills a").on("click", function (e) {
            var tabActive = $(this).attr("href").substring(1); // Mendapatkan nilai href
            setSettingTabActive(tabActive); // Memanggil function setSettingTabActive
        });

        // Holidays Area
        holidaysArea();

        // Shift Area
        shiftArea();

        // Schedule Area
        scheduleArea();

        // Init table base on tabActive
        initTable();
    });
});

let tabActive;
function setSettingTabActive(tab) {
    var showTab = tab.charAt(0).toUpperCase() + tab.slice(1);
    tabActive = tab;

    $.ajax({
        type: "get",
        url: base_url() + `settings/tab/${tab}`,
        success: function (res) {
            initTable();
            iziToast.show({
                title: `${showTab}!`,
                message: `This tab show your ${tab} information`,
                position: "bottomRight",
                timeout: 1680,
            });
        },
    });
}

let tableHolidays, tableShift, tableSchedule;
function initTable() {
    switch (tabActive) {
        case "schedule":
            if (!tableSchedule) {
                tableSchedule = $(".table-schedule").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: base_url() + `settings/ajax/datatable/schedule`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    columns: [
                        {
                            data: "DT_RowIndex",
                            className: "align-middle text-center",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: "schedule",
                            name: "schedule",
                            className: "align-middle text-left",
                        },
                        {
                            data: "keterangan",
                            className: "align-middle text-left",
                        },
                        {
                            data: "_senin",
                            name: "senin",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_selasa",
                            name: "selasa",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_rabu",
                            name: "rabu",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_kamis",
                            name: "kamis",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_jumat",
                            name: "jumat",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_sabtu",
                            name: "sabtu",
                            className: "align-middle text-center",
                        },
                        {
                            data: "_minggu",
                            name: "minggu",
                            className: "align-middle text-center",
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
            }
            break;
        case "shift":
            if (!tableShift) {
                tableShift = $(".table-shift").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: base_url() + `settings/ajax/datatable/shift`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    columns: [
                        {
                            data: "DT_RowIndex",
                            className: "align-middle text-center",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: "name",
                            name: "name",
                            className: "align-middle text-left",
                        },
                        {
                            data: "keterangan",
                            className: "align-middle text-left",
                        },
                        {
                            data: "jam_masuk",
                            name: "jam_masuk",
                            className: "align-middle text-center",
                        },
                        {
                            data: "jam_mulai_istirahat",
                            name: "jam_mulai_istirahat",
                            className: "align-middle text-center",
                        },
                        {
                            data: "jam_selesai_istirahat",
                            name: "jam_selesai_istirahat",
                            className: "align-middle text-center",
                        },
                        {
                            data: "jam_keluar",
                            name: "jam_keluar",
                            className: "align-middle text-center",
                        },
                        {
                            data: "total_jam_istirahat",
                            name: "total_jam_istirahat",
                            className: "align-middle text-center",
                        },
                        {
                            data: "total_jam_kerja",
                            name: "total_jam_kerja",
                            className: "align-middle text-center",
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
            }
            break;
        case "holidays":
            if (!tableHolidays) {
                tableHolidays = $(".table-holidays").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax:
                        base_url() +
                        `settings/ajax/datatable/holidays/${yearHoliday}`,
                    // dom: `tip`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    columns: [
                        {
                            data: "DT_RowIndex",
                            className: "align-middle text-center",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: "_date",
                            name: "date",
                            className: "align-middle text-left",
                        },
                        {
                            data: "day",
                            className: "align-middle text-left",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: "note",
                            name: "note",
                            className: "align-middle text-left",
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
            }
            break;

        default:
            break;
    }
}

// Shift Area
let isSubmitedShift = false;
let isUpdatedShift = false;
let isModalErrorShift = false;

function shiftArea() {
    // Alert Submited
    isSubmitedShift = $(`#alert-success-shift`).val();
    if (isSubmitedShift == 1) {
        alertSubmitedShift();
    }

    // Alert Updated
    isUpdatedShift = $(`#alert-updated-shift`).val();
    if (isUpdatedShift == 1) {
        alertUpdatedShift();
    }

    // Show modal error
    isModalErrorShift = $(`#alert-modal-shift`).val();
    if (isModalErrorShift > 0) {
        editShift(isModalErrorShift);
    }

    // Search Shift
    $("#search-shift").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        tableShift.search(value).draw();
    });
}

function alertSubmitedShift() {
    iziToast.success({
        title: `Submited!`,
        message: `Shift has been created.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function alertUpdatedShift() {
    iziToast.info({
        title: `Updated!`,
        message: `Shift has been updated.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function deleteShift(id) {
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
                url: base_url() + `settings/shift/delete/${id}`,
                success: function (res) {
                    iziToast.success({
                        title: `Deleted!`,
                        message: `Shift has been deleted.`,
                        position: "bottomRight",
                        timeout: 1680,
                    });
                    reloadTableShift();
                },
            });
        }
    });
}

function reloadTableShift() {
    tableShift.ajax.url(base_url() + `settings/ajax/datatable/shift`).load();
}

function editShift(id) {
    $("#modal-edit-shift").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + `settings/shift/${id}`,
        success: function (res) {
            $("#edit-id-shift").val(res.id);
            $("#edit-name-shift").val(res.name);
            $("#edit-jam_masuk-shift").val(res.jam_masuk);
            $("#edit-jam_mulai_istirahat-shift").val(res.jam_mulai_istirahat);
            $("#edit-jam_selesai_istirahat-shift").val(
                res.jam_selesai_istirahat
            );
            $("#edit-jam_keluar-shift").val(res.jam_keluar);
            $("#edit-keterangan-shift").val(res.keterangan);
            $("#edit-is_sameday-shift").prop(
                "checked",
                res.is_sameday == 1 ? true : false
            );
            $("#edit-is_break-shift").prop(
                "checked",
                res.is_break == 1 ? true : false
            );
            if (res.is_break == 0) {
                $("#edit-jam_mulai_istirahat-shift").prop("disabled", true);
                $("#edit-jam_selesai_istirahat-shift").prop("disabled", true);
            } else {
                $("#edit-jam_mulai_istirahat-shift").prop("disabled", false);
                $("#edit-jam_selesai_istirahat-shift").prop("disabled", false);
            }
        },
    });
}

function handleIsBreak() {
    if (document.getElementById("edit-is_break-shift").checked) {
        document.getElementById(
            "edit-jam_mulai_istirahat-shift"
        ).disabled = false;
        document.getElementById(
            "edit-jam_selesai_istirahat-shift"
        ).disabled = false;
    } else {
        document.getElementById(
            "edit-jam_mulai_istirahat-shift"
        ).disabled = true;
        document.getElementById(
            "edit-jam_selesai_istirahat-shift"
        ).disabled = true;
    }
}

// Holidays Area
// ----------------

let yearHoliday = new Date().getFullYear();
let isSubmitedHoliday = false;
let isUpdatedHoliday = false;
let isModalErrorHoliday = false;

function holidaysArea() {
    // Event on click button yearHoliday
    $(".yearpicker").yearpicker({
        onChange: function (value) {
            yearHoliday = value;
            reloadTableHolidays();
            $(".yearpicker").html(
                `<i class="far fa-calendar mr-1"></i> ${value}`
            );
        },
        endYear: yearHoliday,
    });
    $(".yearpicker").html(
        `<i class="far fa-calendar mr-1"></i> ${yearHoliday}`
    );

    // Alert Submited
    isSubmitedHoliday = $(`#alert-success-holiday`).val();
    if (isSubmitedHoliday == 1) {
        alertSubmitedHoliday();
    }

    // Alert Updated
    isUpdatedHoliday = $(`#alert-updated-holiday`).val();
    if (isUpdatedHoliday == 1) {
        alertUpdatedHoliday();
    }

    // Show modal error
    isModalErrorHoliday = $(`#alert-modal-holiday`).val();
    if (isModalErrorHoliday > 0) {
        editHoliday(isModalErrorHoliday);
    }

    // Search Holidays
    $("#search-holidays").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        tableHolidays.search(value).draw();
    });
}
function reloadTableHolidays() {
    tableHolidays.ajax
        .url(base_url() + `settings/ajax/datatable/holidays/${yearHoliday}`)
        .load(null, false);
}

function deleteHoliday(id) {
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
                url: base_url() + `settings/holidays/delete/${id}`,
                success: function (res) {
                    // Swal.fire({
                    //     title: "Deleted!",
                    //     text: "Holiday has been deleted.",
                    //     icon: "success",
                    // });
                    iziToast.success({
                        title: `Deleted!`,
                        message: `Holiday has been deleted.`,
                        position: "bottomRight",
                        timeout: 1680,
                    });
                    reloadTableHolidays();
                },
            });
        }
    });
}

function alertSubmitedHoliday() {
    iziToast.success({
        title: `Submited!`,
        message: `Holiday has been created.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function alertUpdatedHoliday() {
    iziToast.info({
        title: `Updated!`,
        message: `Holiday has been updated.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function editHoliday(id) {
    $("#modal-edit-holiday").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + `settings/holidays/${id}`,
        success: function (res) {
            $("#edit-id-holiday").val(res.id);
            $("#edit-date-holiday").val(res.date);
            $("#edit-note-holiday").val(res.note);
        },
    });
}

// Schedule Area
// ----------------

let isSubmitedSchedule = false;
let isUpdatedSchedule = false;
let isModalErrorSchedule = false;

function scheduleArea() {
    // Init Shift
    initShift();

    // Alert Submited
    isSubmitedSchedule = $(`#alert-success-schedule`).val();
    if (isSubmitedSchedule == 1) {
        alertSubmitedSchedule();
    }

    // Alert Updated
    isUpdatedSchedule = $(`#alert-updated-schedule`).val();
    if (isUpdatedSchedule == 1) {
        alertUpdatedSchedule();
    }

    // Show modal error
    isModalErrorSchedule = $(`#alert-modal-schedule`).val();

    if (isModalErrorSchedule > 0) {
        editSchedule(isModalErrorSchedule);
    }

    // Search Schedule
    $("#search-schedule").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        tableSchedule.search(value).draw();
    });
}

function initShift() {
    $.ajax({
        type: "get",
        url: base_url() + `settings/shift/get/active`,
        success: function (res) {
            shift = $.map(res, function (obj) {
                return {
                    id: obj.id,
                    text: obj.name,
                };
            });
        },
    });
}
function alertSubmitedSchedule() {
    iziToast.success({
        title: `Submited!`,
        message: `Schedule has been created.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function alertUpdatedSchedule() {
    iziToast.info({
        title: `Updated!`,
        message: `Schedule has been updated.`,
        position: "bottomRight",
        timeout: 1680,
    });
}

function deleteSchedule(id) {
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
                url: base_url() + `settings/schedule/delete/${id}`,
                success: function (res) {
                    iziToast.success({
                        title: `Deleted!`,
                        message: `Schedule has been deleted.`,
                        position: "bottomRight",
                        timeout: 1680,
                    });
                    reloadTableSchedule();
                },
            });
        }
    });
}

function reloadTableSchedule() {
    tableSchedule.ajax
        .url(base_url() + `settings/ajax/datatable/schedule`)
        .load();
}

let shift;

function clearSelect2() {
    $("#senin").select2("destroy");
    $("#selasa").select2("destroy");
    $("#rabu").select2("destroy");
    $("#kamis").select2("destroy");
    $("#jumat").select2("destroy");
    $("#sabtu").select2("destroy");
    $("#minggu").select2("destroy");
}
function editSchedule(id) {
    $.ajax({
        type: "get",
        url: base_url() + `settings/schedule/${id}`,
        success: function (res) {
            $("#edit-id-schedule").val(res.id);
            $("#edit-schedule").val(res.schedule);
            $("#edit-keterangan-schedule").val(res.keterangan);

            $(".select2").select2({
                data: shift,
            });

            $("#senin").val(getShiftSelect2(res.senin)).trigger("change");
            $("#selasa").val(getShiftSelect2(res.selasa)).trigger("change");
            $("#rabu").val(getShiftSelect2(res.rabu)).trigger("change");
            $("#kamis").val(getShiftSelect2(res.kamis)).trigger("change");
            $("#jumat").val(getShiftSelect2(res.jumat)).trigger("change");
            $("#sabtu").val(getShiftSelect2(res.sabtu)).trigger("change");
            $("#minggu").val(getShiftSelect2(res.minggu)).trigger("change");

            $("#modal-edit-schedule").appendTo("body").modal("show");
        },
    });
}

function getShiftSelect2(shift_id) {
    const foundShift = shift.find((item) => item.id == shift_id);
    return foundShift ? foundShift.id : "libur";
}

function showDetailShift(id) {
    $.ajax({
        type: "get",
        url: base_url() + `settings/shift/${id}`,
        success: function (res) {
            $("#show-name-shift").val(res.name);
            $("#show-jam_masuk-shift").val(res.jam_masuk);
            $("#show-jam_mulai_istirahat-shift").val(res.jam_mulai_istirahat);
            $("#show-jam_selesai_istirahat-shift").val(
                res.jam_selesai_istirahat
            );
            $("#show-jam_keluar-shift").val(res.jam_keluar);
            $("#show-keterangan-shift").val(res.keterangan);
            $("#show-is_sameday-shift").prop(
                "checked",
                res.is_sameday == 1 ? true : false
            );

            $("#show-is_break-shift").prop(
                "checked",
                res.is_break == 1 ? true : false
            );
            $("#modal-show-shift").appendTo("body").modal("show");
        },
    });
}
