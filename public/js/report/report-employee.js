document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        employee_id = $("#employee_id").text();
        // Init Periode, Range & Table
        initPeriode();

        // Toggle Button Summary
        const button = document.getElementById("toggle-summary");
        button.addEventListener("click", function () {
            // Toggle the rotate class on the icon
            const icon = this.querySelector("i");
            icon.classList.toggle("fa-chevron-up");
            icon.classList.toggle("fa-chevron-down");
        });
    });
});

let year, periode, holidays;

function initPeriode() {
    $.ajax({
        url: base_url() + "report/ajax/get-periode",
        type: "GET",
        dataType: "json",
        success: function (res) {
            periode = res;
            console.log(res);

            year = moment(periode.end).format("YYYY");

            initDaterangePicker();

            initHolidaysThenTable();

            initStatistikKehadiran();
        },
    });
}

function initStatistikKehadiran() {
    $.ajax({
        url: base_url() + `api/v1/report/get-absen-stats/${employee_id}`,
        type: "GET",
        dataType: "json",
        success: function (res) {
            let stat = res.data;
            console.log(stat);

            $("#attendance-percentage")
                .children("span:nth-child(2)")
                .text(`${stat.persentase_kehadiran}%`);
            $("#attendance-day")
                .children("span:nth-child(2)")
                .text(`${stat.hadir} dari ${stat.total_hari} hari`);
            $("#attendance-total-hour")
                .children("span:nth-child(2)")
                .text(`${stat.total_jam_hadir} jam`);

            $("#regular-allowance-meal")
                .children("span:nth-child(2)")
                .text(`${stat.total_um}x`);
            $("#regular-allowance-transport")
                .children("span:nth-child(2)")
                .text(`${stat.total_ut}x`);
            $("#regular-allowance-diligence")
                .children("span:nth-child(2)")
                .text(`${stat.total_uk}x`);

            $("#overtime-allowance-meal")
                .children("span:nth-child(2)")
                .text(`${stat.total_uml}x`);
            $("#overtime-allowance-transport")
                .children("span:nth-child(2)")
                .text(`${stat.total_utl}x`);
            $("#overtime-allowance-meal-overtime")
                .children("span:nth-child(2)")
                .text(`${stat.total_umll}x`);
        },
    });
}

function initHolidaysThenTable() {
    $.ajax({
        url:
            base_url() +
            `report/ajax/get-holidays/${periode.start}/${periode.end}`,
        type: "GET",
        dataType: "json",
        success: function (data) {
            holidays = data;
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
            opens: "right",
            drops: "auto",
        },
        function (start, end, label) {
            periode.start = start.format("YYYY-MM-DD");
            periode.end = end.format("YYYY-MM-DD");
            periode.name = label;
            $("#filter-periode").html(
                `<i class="fas fa-calendar-alt mr-1"></i> ${label}`
            );
            $("#attendance-title").html(
                `Ringkasan Kehadiran | ${periode.name} : ${periode.start}  ${periode.end}`
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
                    initHolidaysThenTable();
                },
            });
        }
    );
}

let employee_id, tableReport;
function initTable() {
    tableReport = $(".table-report").DataTable({
        // processing: true,
        // serverSide: true,
        fixedHeader: true,
        ajax:
            base_url() + `report/ajax/datatable/report-employee/${employee_id}`,
        dom: `t`,
        columns: [
            // 0. Date
            {
                data: "tanggal",
                name: "tanggal",
                className: "align-middle text-center",
                // Kolom tanggal, menampilkan tanggal kehadiran karyawan
            },

            // 1. Shift
            {
                data: "shift",
                name: "shift",
                className: "align-middle text-center p-0",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom shift
                    if (row.is_lembur_libur == 1) {
                        return `<div class="btn btn-sm btn-success m-0" onclick="showLembur(${row.lembur_id})"><i class="fas fa-clock mr-1"></i> Lembur Libur </div>`;
                    }
                    if (holidays[row.date]) {
                        return `<div class="btn btn-sm btn-danger" onclick="showHoliday('[${
                            row.date
                        }] ➡️ ${
                            holidays[row.date].note
                        }')"><i class="fas fa-calendar-alt mr-1"></i> Holiday</div>`;
                    }
                    if (data == null) {
                        return `<div class="btn btn-sm btn-danger m-0" onclick="showHoliday('[${row.date}] ➡️ Libur Rutin')"><i class="fas fa-calendar-times mr-1"></i> Libur Rutin </div>`;
                    }
                    if (row.is_cuti == 1) {
                        return `<div class="btn btn-sm btn-success m-0" onclick="showCuti(${row.cuti_id})"><i class="fas fa-calendar-times mr-1"></i> Cuti </div>`;
                    }

                    if (row.is_izin == 1) {
                        return `<div class="btn btn-sm btn-success m-0" onclick="showIzin(${row.izin_id})"><i class="fas fa-calendar-times mr-1"></i> Izin </div>`;
                    }

                    return data;
                },
                // Kolom shift, menampilkan informasi tentang shift kerja karyawan
            },

            // 2. Jam Masuk
            {
                data: "jam_masuk",
                name: "jam_masuk",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    if (row.is_lembur_libur == 1) {
                        return "-";
                    }
                    return data;
                },
                // Kolom jam masuk, menampilkan jam masuk karyawan
            },

            // 3. Scan Masuk
            {
                data: "scan_masuk",
                name: "scan_masuk",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom scan masuk
                    if (data == null && row.shift != null) {
                        return `<i class="fas fa-times-circle text-danger"></i>`;
                    }
                    if (
                        row.verifikasi == "scan_masuk" ||
                        row.verifikasi == "hadir"
                    ) {
                        return `<div class="btn btn-sm btn-success" onclick="showVerifikasi('${row.verifikasi_id}')"> ${data}</div>`;
                    }
                    return data;
                },
                // Kolom scan masuk, menampilkan informasi tentang scan masuk karyawan
            },

            // 4. Jam Keluar
            {
                data: "jam_keluar",
                name: "jam_keluar",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    if (row.is_lembur_libur == 1) {
                        return "-";
                    }
                    return data;
                },
                // Kolom jam keluar, menampilkan jam keluar karyawan
            },

            // 5. Scan Keluar
            {
                data: "scan_keluar",
                name: "scan_keluar",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom scan keluar
                    if (data == null && row.shift != null) {
                        return `<i class="fas fa-times-circle text-danger"></i>`;
                    }
                    if (
                        row.verifikasi == "scan_keluar" ||
                        row.verifikasi == "hadir"
                    ) {
                        return `<div class="btn btn-sm btn-success" onclick="showVerifikasi('${row.verifikasi_id}')"> ${data}</div>`;
                    }
                    return data;
                },
                // Kolom scan keluar, menampilkan informasi tentang scan keluar karyawan
            },

            // 6. Durasi Murni
            {
                data: "durasi_murni",
                name: "durasi_murni",
                className: "align-middle text-center",
                // Kolom durasi murni, menampilkan durasi kerja karyawan
            },

            // 7. Durasi Efektif
            {
                data: "durasi_efektif",
                name: "durasi_efektif",
                className: "align-middle text-center",
                // Kolom durasi efektif, menampilkan durasi kerja efektif karyawan
            },

            // 8. Jam Hilang Murni
            {
                data: "jam_hilang_murni",
                name: "jam_hilang_murni",
                className: "align-middle text-center",
                // Kolom jam hilang murni, menampilkan jam hilang kerja karyawan
            },

            // 9. Jam Hilang Efektif
            {
                data: "jam_hilang_efektif",
                name: "jam_hilang_efektif",
                className: "align-middle text-center",
                // Kolom jam hilang efektif, menampilkan jam hilang kerja efektif karyawan
            },

            // 10. Lembur Murni
            {
                data: "lembur_murni",
                name: "lembur_murni",
                className: "align-middle text-center",
                // Kolom lembur murni, menampilkan lembur kerja karyawan
            },

            // 11. Lembur Efektif
            {
                data: "lembur_efektif",
                name: "lembur_efektif",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom lembur murni
                    if (row.is_lembur == 1 || row.is_lembur_libur == 1) {
                        return `<div class="btn btn-sm btn-success" onclick="showLembur('${row.lembur_id}')"><i class="fas fa-check-circle"></i> ${data}</div>`;
                    }
                    if (row.durasi_lembur >= 60 && row.shift == null) {
                        return `<div class="btn btn-sm btn-warning" onclick="generatePerizinan('${row.date}', ${row.report_id})"><i class="fas fa-question-circle"></i> ${data}</div>`;
                    }
                    if (row.durasi_lembur >= 60) {
                        return `<div class="btn btn-sm btn-warning" onclick="confirmLembur(${row.report_id})"><i class="fas fa-question-circle"></i> ${data}</div>`;
                    }
                    return data;
                },
                // Kolom lembur efektif, menampilkan lembur kerja efektif karyawan
            },

            // 12. Potongan
            {
                data: "potongan",
                name: "potongan",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom potongan
                    if (row.is_izin == 1) {
                        return `<div class="btn btn-sm btn-success" onclick="showIzin(${row.izin_id})"><i class="fas fa-minus-circle"></i> ${data}</div>`;
                    }

                    return data;
                },
                // Kolom potongan, menampilkan informasi tentang potongan karyawan
            },

            // 13. Tunjangan
            {
                data: "tunjangan",
                name: "tunjangan",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom tunjangan
                    var result = `<div onclick="showTunjangan(${row.ut}, ${row.um}, ${row.uk}, ${row.utl}, ${row.uml}, ${row.umll})" class="btn btn-sm btn-dark text-center">`;

                    if (row.ut == 1) {
                        result += `<i class="fas fa-gas-pump text-success px-1" style="font-size: 10px;"></i>`;
                    }
                    if (row.um == 1) {
                        result += `<i class="fas fa-utensils text-success px-1" style="font-size: 10px;"></i>`;
                    }
                    if (row.uk == 1) {
                        result += `<i class="fas fa-briefcase text-success px-1" style="font-size: 10px;"></i>`;
                    }
                    if (row.utl == 1) {
                        result += `<i class="fas fa-gas-pump text-warning px-1" style="font-size: 10px;"></i>`;
                    }
                    if (row.uml == 1) {
                        result += `<i class="fas fa-utensils text-warning px-1" style="font-size: 10px;"></i>`;
                    }
                    if (row.umll == 1) {
                        result += `<i class="fas fa-coffee text-warning px-1" style="font-size: 10px;"></i>`;
                    }
                    result += `</div>`;
                    if (row.shift == null) {
                        result = data;
                    }
                    let totalTunjangan = 0;
                    totalTunjangan += row.ut ? row.ut : 0;
                    totalTunjangan += row.um ? row.um : 0;
                    totalTunjangan += row.uk ? row.uk : 0;
                    totalTunjangan += row.utl ? row.utl : 0;
                    totalTunjangan += row.uml ? row.uml : 0;
                    totalTunjangan += row.umll ? row.umll : 0;
                    if (totalTunjangan == 0) {
                        result = `<div onclick="showTunjangan(${row.ut}, ${row.um}, ${row.uk}, ${row.utl}, ${row.uml}, ${row.umll})" class="btn btn-sm btn-dark text-center"><i class="fas fa-times-circle text-danger px-1" style="font-size: 10px;"></i></div>`;
                    }

                    // if (
                    //     (row.scan_masuk != null || row.scan_keluar != null) &&
                    //     row.shift != null
                    // ) {
                    //     result = `<i class="fas fa-motorcycle text-success mr-1" style="font-size: 10px;"></i>
                    //                 <i class="fas fa-coffee text-success mr-1" style="font-size: 10px;"></i>
                    //                 <i class="fas fa-briefcase text-success mr-1" style="font-size: 10px;"></i>`;
                    // }
                    return result;
                },
                // Kolom tunjangan, menampilkan informasi tentang tunjangan karyawan
            },

            // 14. Perizinan
            {
                data: "perizinan",
                name: "perizinan",
                className: "align-middle text-center",
                render: function (data, type, row) {
                    // Fungsi render untuk mengubah tampilan data dalam kolom perizinan
                    return data;
                },
                // Kolom perizinan, menampilkan informasi tentang perizinan karyawan
            },
        ],
        pageLength: -1,
        columnDefs: [
            {
                targets: 0,
                width: "158px",
            },
            {
                targets: 1,
                width: "120px",
            },
        ],
        rowCallback: function (row, data) {
            if (data.is_cuti == 1) {
                $(row).css("background-color", "#ffe6e6");
                for (var i = 2; i <= 12; i++) {
                    $(row)
                        .find("td:eq(" + i + ")")
                        .html("");
                }
            } else if (data.is_izin == 1 && data.is_full_day == 1) {
                $(row).css("background-color", "#ffe6e6");
                for (var i = 2; i <= 12; i++) {
                    $(row)
                        .find("td:eq(" + i + ")")
                        .html("");
                }
            } else {
                if (data.shift == null) {
                    $(row).css("background-color", "#ffe6e6");
                    if (data.scan_masuk != null) {
                        $(row).find("td:eq(3)").css({
                            "background-color": "#6f42c1",
                            color: "#ffffff",
                        });
                    }
                    if (data.scan_keluar != null) {
                        $(row).find("td:eq(5)").css({
                            "background-color": "#6f42c1",
                            color: "#ffffff",
                        });
                    }
                } else if (
                    data.scan_masuk == null ||
                    data.scan_keluar == null
                ) {
                    $(row).css("background-color", "#fff9c4");
                }
            }
        },
        order: [],
    });
}

function showHoliday(note) {
    Swal.fire({
        title: "Holiday Note",
        text: note,
        icon: "info",
    });
}

let report, date;
function generatePerizinan(param_date, report_id) {
    date = param_date;

    const check_holiday =
        holidays &&
        Object.values(holidays).some((holiday) =>
            moment(date).isSame(holiday.date, "day")
        );

    if (report_id != 0 && check_holiday == false) {
        $.ajax({
            url: base_url() + `api/v1/report/get-report/${report_id}`,
            type: "GET",
            success: function (res) {
                report = res;

                $("#default-tab").hide();
                $("#cuti-tab").hide();
                $("#lembur-tab").hide();
                $("#verifikasi-tab").hide();
                $("#izin-tab").hide();
                if (report.shift_id == null) {
                    $("#lembur-tab").show();
                    $("#lembur-tab").tab("show");
                    initTabLemburLibur(); // function at tab-lembur
                } else if (report.is_cuti == 1) {
                    $("#verifikasi-tab").hide();
                    $("#default-tab").show();
                    $("#default-tab").tab("show");
                } else if (
                    report.is_lembur == 1 ||
                    report.is_lembur_libur == 1
                ) {
                    $("#lembur-tab").hide();
                    $("#izin-tab").show();
                    $("#izin-tab").tab("show");
                } else if (
                    report.scan_masuk_murni == null ||
                    report.scan_keluar_murni == null
                ) {
                    $("#izin-tab").show();
                    $("#cuti-tab").show();
                    $("#verifikasi-tab").show();
                    $("#verifikasi-tab").tab("show");
                    initTabVerifikasi(); // function at tab-verifikasi
                } else {
                    $("#izin-tab").show();
                    $("#izin-tab").tab("show");
                    $("#lembur-tab").show();
                    initTabLembur(); // function at tab-lembur
                    initTabIzin(); // function at tab-izin
                }
            },
        });
    } else {
        initTabLemburLibur();
        $("#lembur-tab").show();
        $("#default-tab").hide();
        $("#cuti-tab").hide();
        $("#izin-tab").hide();
        $("#verifikasi-tab").hide();
        $("#lembur-tab").tab("show");
    }

    initTabCuti(); // function at tab-cuti

    $(".custom-file-label").html("Choose file (pdf/jpeg/png)");
    $("#modal-perizinan").appendTo("body").modal("show");
}

function konversiMenit(menit) {
    /**
     * Mengubah total menit menjadi format jam dan menit.
     *
     * @param {number} menit - Jumlah total menit yang akan dikonversi
     * @returns {string} Format jam dan menit
     */
    if (menit < 0) {
        return "Jumlah menit tidak valid";
    }

    const jam = Math.floor(menit / 60);
    const sisaMenit = menit % 60;

    if (jam === 0) {
        return `${menit} menit`;
    } else if (sisaMenit === 0) {
        return `${jam} jam`;
    } else {
        return `${jam} jam ${sisaMenit} menit`;
    }
}

function clearTable() {
    if (tableReport) {
        tableReport.destroy();
        tableReport = null;
    }
}

function validateFile(input) {
    const file = input.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB
    const allowedTypes = ["application/pdf", "image/jpeg", "image/png"];

    // Remove any existing error message
    $(input).siblings(".text-danger").remove();

    // Update file label
    const fileName = file ? file.name : "Choose file (pdf/jpeg/png)";
    $(input).next(".custom-file-label").text(fileName);

    if (file) {
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            $(input)
                .parent()
                .append(
                    '<span class="text-danger small d-block mt-1">File harus berupa PDF, JPEG, atau PNG</span>'
                );
            input.value = "";
            $(input)
                .next(".custom-file-label")
                .text("Choose file (pdf/jpeg/png)");
            return false;
        }

        // Check file size
        if (file.size > maxSize) {
            $(input)
                .parent()
                .append(
                    '<span class="text-danger small d-block mt-1">Ukuran file tidak boleh lebih dari 2MB</span>'
                );
            input.value = "";
            $(input)
                .next(".custom-file-label")
                .text("Choose file (pdf/jpeg/png)");
            return false;
        }
    }
    return true;
}

function showTunjangan(ut, um, uk, utl, uml, umll) {
    let tunjangan = "";
    if (ut == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-success m-2'>Uang Transport</span> ";
    if (um == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-success m-2'>Uang Makan</span> ";
    if (uk == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-success m-2'>Uang Kerajinan</span> ";
    if (uml == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-warning m-2'>Uang Makan Lembur</span> ";
    if (utl == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-warning m-2'>Uang Transport Lembur</span> ";
    if (umll == 1)
        tunjangan +=
            "<span class='badge badge-pill badge-warning m-2'>Uang Makan Lembur Libur</span> ";
    if (tunjangan != "") {
        Swal.fire({
            icon: "info",
            title: "Tunjangan",
            html: `<div class="d-flex flex-wrap">${tunjangan}</div>`,
        });
    } else {
        Swal.fire({
            icon: "info",
            title: "Tunjangan",
            text: "Tidak ada tunjangan yang tersedia",
        });
    }
}
