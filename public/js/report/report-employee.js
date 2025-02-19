document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        employee_id = $("#employee_id").text();
        // Init Periode, Range & Table
        initPeriode();
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
                success: function (data) {
                    clearTable();
                    initTable();
                },
            });
        }
    );
}

let employee_id, tableReport;
function initTable() {
    tableReport = $(".table-report").DataTable({
        processing: true,
        serverSide: true,
        ajax:
            base_url() + `report/ajax/datatable/report-employee/${employee_id}`,
        dom: `t`,
        columns: [
            {
                data: "tanggal",
                name: "tanggal",
                className: "align-middle text-center",
            },
            {
                data: "shift",
                name: "shift",
                className: "align-middle text-left",
            },
            {
                data: "jam_masuk",
                name: "jam_masuk",
                className: "align-middle text-left",
            },
            {
                data: "scan_masuk",
                name: "scan_masuk",
                className: "align-middle text-left",
            },
            {
                data: "jam_keluar",
                name: "jam_keluar",
                className: "align-middle text-left",
            },
            {
                data: "scan_keluar",
                name: "scan_keluar",
                className: "align-middle text-left",
            },
            {
                data: "durasi_murni",
                name: "durasi_murni",
                className: "align-middle text-left",
            },
            {
                data: "durasi_efektif",
                name: "durasi_efektif",
                className: "align-middle text-left",
            },
            {
                data: "jam_hilang_murni",
                name: "jam_hilang_murni",
                className: "align-middle text-left",
            },
            {
                data: "jam_hilang_efektif",
                name: "jam_hilang_efektif",
                className: "align-middle text-left",
            },
            {
                data: "lembur_murni",
                name: "lembur_murni",
                className: "align-middle text-left",
            },
            {
                data: "lembur_efektif",
                name: "lembur_efektif",
                className: "align-middle text-left",
            },
            {
                data: "verifikasi",
                name: "verifikasi",
                className: "align-middle text-right",
            },
            {
                data: "perizinan",
                name: "perizinan",
                className: "align-middle text-right",
            },
        ],
        pageLength: -1,
        columnDefs: [
            {
                targets: 0,
                width: "188px",
            },
            {
                targets: 1,
                width: "100px",
            },
        ],
        order: [],
    });
}

function clearTable() {
    if (tableReport) {
        tableReport.destroy();
        tableReport = null;
    }
}
