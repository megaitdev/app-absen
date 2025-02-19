document.addEventListener("DOMContentLoaded", () => {
    $(document).ready(function () {
        // Init Table Employee
        initTable();

        // Search Employee
        $("#search-employee").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            tableEmployee.search(value).draw();
        });

        // Get Info Employee
        getInfoEmployee();
    });
});

let tableEmployee;
function initTable() {
    tableEmployee = $(".table-employee").DataTable({
        processing: true,
        serverSide: true,
        ajax: base_url() + `employee/ajax/datatable/all`,
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
                className: "align-middle text-left",
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
}

function changeLength(length) {
    tableEmployee.page.len(length).draw();
    $(".length-info").html(`<i class="fas fa-layer-group mr-2"></i>${length}`);
    $(".dropdown-item").removeClass("active");
    $(`.di-${length}`).addClass("active");
}

function getInfoEmployee() {
    $.ajax({
        url: base_url() + "employee/info",
        method: "GET",
        success: function (res) {
            $("#sinkron-count").text(res.sinkron).hide().fadeIn(765);
            $("#belum-sinkron-count")
                .text(res.belum_sinkron)
                .hide()
                .fadeIn(765);
            $("#need-update-count").text(res.need_update).hide().fadeIn(765);
            $(".loading-info").hide(123); // Menggunakan fadeOut dengan durasi 1000ms (1 detik)
        },
    });
}

function syncData() {
    const swalOptions = {
        title: "Sinkronisasi...",
        html: "Tunggu hingga proses sinkronisasi selesai. Proses ini mungkin memakan waktu beberapa detik atau menit.",
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Make an AJAX call to the synchronize endpoint
            $.ajax({
                url: base_url() + "employee/synchronize",
                method: "GET",
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Sinkronisasi Berhasil",
                        text: response.message,
                        confirmButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload the page or update the table
                            location.reload();
                        }
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Sinkronisasi Gagal",
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

    Swal.fire(swalOptions);
}
