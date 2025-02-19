document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init tabActive
        tabActive = $("li a.active").attr("href").substring(1);

        // Event on click tab
        $(".nav-pills a").on("click", function (e) {
            var tabActive = $(this).attr("href").substring(1); // Mendapatkan nilai href
            setReportTab(tabActive); // Memanggil function setSettingTabActive
        });

        // Init Daterange
        initPeriode();

        // Search Employee
        $("#search-scan-log").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableScanLog.search(value).draw();
        });
        $("#search-unit").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableUnit.search(value).draw();
        });
        $("#search-employee").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableEmployee.search(value).draw();
        });
        $("#generate-report").on("click", function () {
            generateReport();
        });
    });
});

let year, periode;

function initPeriode() {
    $.ajax({
        url: base_url() + "report/ajax/get-periode",
        type: "GET",
        dataType: "json",
        success: function (data) {
            periode = data;
            year = moment(periode.end).format("YYYY");

            initDaterangePicker();

            initTable();
        },
    });
}

function initDaterangePicker() {
    $("#filter-periode").daterangepicker(
        {
            ranges: {
                Januari: [
                    moment().set({
                        year: moment().year() - 1,
                        month: 11,
                        date: 21,
                    }),
                    moment().set({ month: 0, date: 20 }),
                ],
                Februari: [
                    moment().set({ month: 0, date: 21 }),
                    moment().set({ month: 1, date: 20 }),
                ],
                Maret: [
                    moment().set({ month: 1, date: 21 }),
                    moment().set({ month: 2, date: 20 }),
                ],
                April: [
                    moment().set({ month: 2, date: 21 }),
                    moment().set({ month: 3, date: 20 }),
                ],
                Mei: [
                    moment().set({ month: 3, date: 21 }),
                    moment().set({ month: 4, date: 20 }),
                ],
                Juni: [
                    moment().set({ month: 4, date: 21 }),
                    moment().set({ month: 5, date: 20 }),
                ],
                Juli: [
                    moment().set({ month: 5, date: 21 }),
                    moment().set({ month: 6, date: 20 }),
                ],
                Agustus: [
                    moment().set({ month: 6, date: 21 }),
                    moment().set({ month: 7, date: 20 }),
                ],
                September: [
                    moment().set({ month: 7, date: 21 }),
                    moment().set({ month: 8, date: 20 }),
                ],
                Oktober: [
                    moment().set({ month: 8, date: 21 }),
                    moment().set({ month: 9, date: 20 }),
                ],
                November: [
                    moment().set({ month: 9, date: 21 }),
                    moment().set({ month: 10, date: 20 }),
                ],
                Desember: [
                    moment().set({ month: 10, date: 21 }),
                    moment().set({ month: 11, date: 20 }),
                ],
            },
            showWeekNumbers: true,
            linkedCalendars: false,
            alwaysShowCalendars: true,
            startDate: moment(periode.start),
            endDate: moment(periode.end),
            maxDate: moment().set({ month: 11 }).endOf("month"),
            opens: "left",
            drops: "auto",
        },
        function (start, end, label) {
            periode.start = start.format("YYYY-MM-DD");
            periode.end = end.format("YYYY-MM-DD");
            periode.name = label;
            $("#filter-periode").html(
                `<i class="fas fa-calendar-alt mr-1"></i> ${label}`
            );

            $.ajax({
                url: base_url() + "report/ajax/set-periode",
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    periode: periode,
                },
            });
            clearTable();
            initTable();
        }
    );
}

function clearTable() {
    if (tableScanLog) {
        tableScanLog.destroy();
        tableScanLog = null;
    }
    if (tableUnit) {
        tableUnit.destroy();
        tableUnit = null;
    }
    if (tableEmployee) {
        tableEmployee.destroy();
        tableEmployee = null;
    }
}

function generateReport() {
    $("#modal-progress-generate-report").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + "report/generate/employee",
        success: function (res) {
            console.log("Report generation started:", res);
        },
        error: function (err) {
            console.error("Error starting report generation:", err);
        },
    });
    setTimeout(getProgressReport, 123);
}

function getProgressReport() {
    $.ajax({
        type: "get",
        url: base_url() + `report/progress-generate`,
        success: function (res) {
            console.log(res);

            if (res.total == 0) {
                $(`.zero-state`).attr("hidden", false);
                $(`.state-progress`).attr("hidden", true);
            } else {
                $(`.zero-state`).attr("hidden", true);
                $(`.state-progress`).attr("hidden", false);
            }
            var $bar = $(".progress-generate");
            $bar.text(res.persentase + "%");
            $bar.width(7.5 * res.persentase);
            $(`.step`).text(res.steps);
            $(`.total`).text(res.total);
            if (res.persentase < 100) {
                setTimeout(() => {
                    getProgressReport();
                }, 1000);
                $(`.btn-block`).attr("hidden", true);
                $(`.title-progress`).text("Generate Report");
            } else {
                $(`.title-progress`).text("Success Generate Report");
                $(`.btn-block`).attr("hidden", false);
            }
        },
    });
}

let tabActive;
function setReportTab(tab) {
    var showTab = tab.charAt(0).toUpperCase() + tab.slice(1);
    tabActive = tab;

    $.ajax({
        type: "get",
        url: base_url() + `report/tab/${tab}`,
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

let tableScanLog, tableUnit, tableEmployee;
function initTable() {
    switch (tabActive) {
        case "scan-log":
            if (!tableScanLog) {
                tableScanLog = $(".table-report").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax:
                        base_url() +
                        `report/ajax/datatable/scan-log/${periode.start}/${periode.end}`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    columns: [
                        {
                            data: "DT_RowIndex",
                            orderable: false,
                            searchable: false,
                            className: "align-middle text-center",
                        },
                        {
                            data: "nama_karyawan",
                            name: "nama_karyawan",
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
                            data: "divisi",
                            name: "divisi",
                            className: "align-middle text-right",
                        },
                        {
                            data: "unit",
                            name: "unit",
                            className: "align-middle text-right",
                        },
                        {
                            data: "date",
                            name: "date",
                            className: "align-middle text-right",
                        },
                        {
                            data: "time",
                            name: "time",
                            className: "align-middle text-right",
                        },
                        {
                            data: "device_name",
                            name: "device_name",
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
                    order: [[6, "asc"]],
                });
            }
            break;
        case "unit":
            if (!tableUnit) {
                tableUnit = $(".table-unit").DataTable({
                    ajax: base_url() + `report/ajax/datatable/list-unit`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    columns: [
                        {
                            data: "DT_RowIndex",
                            orderable: false,
                            searchable: false,
                            className: "align-middle text-center",
                        },
                        {
                            data: "unit",
                            name: "unit",
                            className: "align-middle text-left",
                        },
                        {
                            data: "report_employees.jumlah_karyawan",
                            className: "align-middle text-left",
                        },
                        {
                            data: "keterangan",
                            name: "keterangan",
                            className: "align-middle text-left",
                        },

                        {
                            data: "action",
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
        case "employee":
            if (!tableEmployee) {
                tableEmployee = $(".table-employee").DataTable({
                    ajax: base_url() + `report/ajax/datatable/list-employee`,
                    dom: `t<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>`,
                    processing: true,
                    serverSide: true,
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
                            data: "pin",
                            name: "pin",
                            className: "align-middle text-left",
                        },
                        {
                            data: "divisi.divisi",
                            name: "divisi.divisi",
                            className: "align-middle text-left",
                        },
                        {
                            data: "unit.unit",
                            name: "unit.unit",
                            className: "align-middle text-left",
                        },
                        {
                            data: "action",
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

function generateReportEmployee(employee_id, nama) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to generate a report for " + nama + "!",
        icon: "warning",
        confirmButtonText: "Yes, generate!",
        showCancelButton: true,
        confirmButtonColor: "#6777ef",
        cancelButtonColor: "#fc544b",
    }).then((result) => {
        if (result.isConfirmed) {
            const swalLoading = {
                title: "Generating Report...",
                html: "Tunggu hingga proses generate selesai. Proses ini mungkin memakan waktu beberapa detik atau menit.",
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    // Make an AJAX call to the synchronize endpoint
                    $.ajax({
                        url:
                            base_url() +
                            "report/generate/single-employee/" +
                            employee_id,
                        method: "GET",
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Generate Report Berhasil",
                                text: response.message,
                                confirmButtonText: "OK",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload the page or update the table
                                    // location.reload();
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: "error",
                                title: "Generating Report Gagal",
                                text:
                                    "Terjadi kesalahan: " +
                                    (xhr.responseJSON
                                        ? xhr.responseJSON.message
                                        : error),
                                confirmButtonText: "OK",
                            });
                        },
                    });
                },
            };

            Swal.fire(swalLoading);
            // alert("test");
        }
    });
}

function changeLengthScanLog(length) {
    tableScanLog.page.len(length).draw();
    $(".length-info-scan-log").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".disl").removeClass("active");
    $(`.disl-${length}`).addClass("active");
}
function changeLengthUnit(length) {
    tableUnit.page.len(length).draw();
    $(".length-info-unit").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".diu").removeClass("active");
    $(`.diu-${length}`).addClass("active");
}
function changeLengthEmployee(length) {
    tableEmployee.page.len(length).draw();
    $(".length-info-employee").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".die").removeClass("active");
    $(`.die-${length}`).addClass("active");
}
