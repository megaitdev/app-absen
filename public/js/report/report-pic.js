document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init Periode => (initDaterangePicker, initTable))
        initPeriode();
        picID = $("#pic_id").val();

        $("#search-employee").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableEmployee.search(value).draw();
        });

        $("#generate-report").on("click", function () {
            generateReport();
        });

        $("#print-report").on("click", function () {
            printReport();
        });
    });
});

let year, periode, picID;

function initPeriode() {
    $.ajax({
        url: base_url() + "report/ajax/get-periode",
        type: "GET",
        dataType: "json",
        success: function (res) {
            periode = res;
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
            $("#title-report-employee").html(
                `Report Karyawan | ${periode.name} : ${periode.start} ➡️ ${periode.end}`
            );

            $.ajax({
                url: base_url() + "report/ajax/set-periode",
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    periode: periode,
                },
                success: function (res) {
                    clearTable();
                    initTable();
                },
            });
        }
    );
}

let tableEmployee;
function initTable() {
    tableEmployee = $(".table-employee").DataTable({
        ajax: base_url() + `api/v1/report/datatable/report-pic`,
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

function changeLengthEmployee(length) {
    tableEmployee.page.len(length).draw();
    $(".length-info-employee").html(
        `<i class="fas fa-layer-group mr-2"></i>${length}`
    );
    $(".die").removeClass("active");
    $(`.die-${length}`).addClass("active");
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

function clearTable() {
    tableEmployee.destroy();
    tableEmployee = null;
}

function generateReport() {
    $("#modal-progress-generate-report").appendTo("body").modal("show");
    $.ajax({
        type: "get",
        url: base_url() + "api/v1/report/generate/report-pic/" + picID,
        success: function (res) {
            setTimeout(getProgressReport, 123);
            console.log("Generate report started:", res);
        },
        error: function (err) {
            console.error("Error starting generate report:", err);
        },
    });
}

function getProgressReport() {
    $.ajax({
        type: "get",
        url: base_url() + `api/v1/report/progress/generate-report-pic/${picID}`,
        success: function (res) {
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

function printReportV1() {
    Swal.fire({
        title: `Laporan untuk periode: ${periode.name}`,
        text: `Tanggal: ${periode.start} hingga ${periode.end}`,
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Cetak Laporan",
        cancelButtonText: "Tutup",
    }).then((result) => {
        if (result.isConfirmed) {
            const swalLoading = {
                title: "Printing Report...",
                html: "Tunggu hingga proses print selesai.",
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    $.ajax({
                        type: "GET",
                        url: base_url() + `api/v1/report/print/pic/${picID}`,
                        success: function (response) {
                            console.log("Response received:", response); // Log the response URL
                            if (response && response.trim() !== "") {
                                const link = document.createElement("a");
                                link.href = response;
                                link.setAttribute(
                                    "download",
                                    "Laporan PIC.xlsx"
                                );
                                link.style.display = "none";
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            } else {
                                Swal.fire(
                                    "Error",
                                    "File not found or server returned an empty response.",
                                    "error"
                                );
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error during AJAX request:", error);
                            Swal.fire(
                                "Error",
                                "An error occurred while generating the report.",
                                "error"
                            );
                        },
                        complete: function () {
                            Swal.close();
                        },
                    });
                },
            };
            Swal.fire(swalLoading);
        }
    });
}

function printReport() {
    Swal.fire({
        title: `Laporan untuk periode: ${periode.name}`,
        text: `Tanggal: ${periode.start} hingga ${periode.end}`,
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Cetak Laporan",
        cancelButtonText: "Tutup",
    }).then((result) => {
        if (result.isConfirmed) {
            const swalLoading = {
                title: "Printing Report...",
                html: "Tunggu hingga proses print selesai.",
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const url = base_url() + `api/v1/report/print/pic/${picID}`;
                    window.location.href = url;
                    Swal.close();

                    // window.open(url, "_blank");
                },
            };
            Swal.fire(swalLoading);
        }
    });
}
