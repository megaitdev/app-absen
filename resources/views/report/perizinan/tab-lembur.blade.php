<form id="form-lembur" enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $employee->nama }}" disabled>
        </div>
        <div class="form-group col-md-3">
            <label for="date">Tanggal Mulai</label>
            <input type="text" class="form-control" id="date-lembur" name="date" value="" disabled>
        </div>
        <div class="form-group col-md-3 m-0">
            <label for="date">Tanggal Selesai</label>
            <input type="text" class="form-control" id="date-lembur-end" name="date-end" value="" disabled>
            <label class="custom-switch mt-1 p-0">
                <input type="checkbox"class="custom-switch-input" id="switch-lembur">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description">> Jam 12 Malam</span>
            </label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="start_lembur">Mulai Jam</label>
            <input type="text" class="form-control time-lembur" id="start_lembur" name="start_lembur" required
                pattern="[0-9:]+">
        </div>
        <div class="form-group col-md-6">
            <label for="end_lembur">Selesai Jam</label>
            <input type="text" class="form-control time-lembur" id="end_lembur" name="end_lembur" required
                pattern="[0-9:]+">
            <small class="form-text text-muted">Durasi lembur : <span id="durasi-lembur"></span></small>
        </div>
    </div>
    <div class="form-group">
        <label for="file">File</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="lampiran_lembur" name="lampiran_lembur"
                accept="application/pdf,image/*" onchange="validateFile(this)">
            <label class="custom-file-label" for="lampiran_lembur">Choose file
                (pdf/jpeg/png)</label>
        </div>
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea class="form-control h-100" id="keterangan" name="keterangan" rows="2"></textarea>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                class="fas fa-times mr-1"></i>Close</button>
        <button type="button" class="btn btn-primary float-right" onclick="submitLembur()"><i
                class="fas fa-check mr-1"></i>Lembur</button>
    </div>
</form>

<div class="modal fade" id="overtimeModal" tabindex="-1" role="dialog" aria-labelledby="overtimeModal"
    aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title mb-2" id="overtimeModalLabel">Konfirmasi Lembur</h5>
            </div>
            <div class="modal-body">
                <form id="overtime-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nama_overtime">Nama</label>
                            <input type="text" class="form-control" id="nama_overtime" name="nama"
                                value="{{ $employee->nama }}" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="date_overtime">Tanggal Mulai</label>
                            <input type="text" class="form-control" id="date-lembur-overtime" name="date"
                                value="" disabled>
                        </div>
                        <div class="form-group col-md-3 m-0">
                            <label for="date_overtime">Tanggal Selesai</label>
                            <input type="text" class="form-control" id="date-lembur-end-overtime" name="date-end"
                                value="" disabled>
                            <label class="custom-switch mt-1 p-0">
                                <input type="checkbox"class="custom-switch-input" id="switch-lembur-overtime"
                                    disabled>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">> Jam 12 Malam</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_overtime">Mulai Jam</label>
                            <input type="text" class="form-control time-lembur" id="start_overtime"
                                name="start_overtime" required pattern="[0-9:]+" disabled>
                            <small class="form-text text-muted">Durasi lembur murni : <span
                                    id="durasi-overtime-murni"></span></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_overtime">Selesai Jam</label>
                            <input type="text" class="form-control time-lembur" id="end_overtime"
                                name="end_overtime" required pattern="[0-9:]+" disabled>
                            <small class="form-text text-muted">Durasi lembur efektif : <span
                                    id="durasi-overtime"></span></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="file">Lampiran</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="lampiran-overtime"
                                name="lampiran_lembur" accept="application/pdf,image/*"
                                onchange="validateFile(this)">
                            <label class="custom-file-label" id="lampiran-overtime-label"
                                for="lampiran-overtime">Choose file
                                (pdf/jpeg/png)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="overtimeDescription" class="form-label">Keterangan Lembur</label>
                        <textarea class="form-control h-100" id="keterangan-overtime" rows="3" required></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="fas fa-times mr-1"></i>Close</button>
                <button type="button" class="btn btn-primary" onclick="submitConfirmLembur()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
    function initTabLembur() {
        isLemburLibur = false;
        defaultStartLembur = moment(report.shift.jam_keluar, "HH:mm").add(15, 'minutes');
        datePlusOne = moment(date, "YYYY-MM-DD").add(1, 'days').format(
            "YYYY-MM-DD");
        startLembur = moment(date + " " + defaultStartLembur.format("HH:mm"), "YYYY-MM-DD HH:mm");
        endLembur = moment(date + " " + defaultStartLembur.format("HH:mm"), "YYYY-MM-DD HH:mm").add(60,
            'minutes');
        restTime = moment(date + " 18:00", "YYYY-MM-DD HH:mm");
        durasiLembur = Math.floor(endLembur.diff(startLembur, "minutes", true));
        $("#durasi-lembur")
            .text(konversiMenit(durasiLembur));
        $('#start_lembur').val(defaultStartLembur.format('HH:mm'))
        $('#end_lembur').val(endLembur.format('HH:mm'))
        $("#start_lembur").timepicker({
            scrollDefault: "now",
            timeFormat: "H:i",
            minTime: defaultStartLembur.format('HH:mm'),
            maxTime: defaultStartLembur.add(215, 'minutes').format('HH:mm'),
            step: 15,
            disableTextInput: true,
            disableTimeRanges: [
                ["18pm", "18:45pm"]
            ],
        });
        $("#end_lembur").timepicker({
            scrollDefault: "now",
            timeFormat: "H:i",
            step: 15,
            disableTextInput: true,
            disableTimeRanges: [
                ["18:01pm", "18:46pm"]
            ],
        });
        $("#date-lembur").val(date);

    }

    function initTabLemburLibur() {
        isLemburLibur = true;
        $("#date-lembur").val(date);


        $("#start_lembur").timepicker({
            scrollDefault: "now",
            timeFormat: "H:i",
            step: 15,
            disableTextInput: true,
            disableTimeRanges: [
                ["18pm", "18:45pm"],
                ["12pm", "12:45pm"]
            ],
        });
        $("#end_lembur").timepicker({
            scrollDefault: "now",
            timeFormat: "H:i",
            step: 15,
            disableTextInput: true,
            disableTimeRanges: [
                ["18:01pm", "18:46pm"],
                ["12:01pm", "12:45pm"]
            ],
        });

        $.ajax({
            url: base_url() + 'api/v1/report/report-by-date/' + date + '/' + {{ $employee->id }},
            type: 'GET',
            success: function(res) {
                if (res && Object.keys(res).length !== 0) {
                    var timeStartLembur = moment(res.scan_masuk_murni, "YYYY-MM-DD HH:mm").format("HH:mm");
                    var timeEndLembur = moment(res.scan_keluar_murni, "YYYY-MM-DD HH:mm").format("HH:mm");
                    startLembur = moment(res.scan_masuk_murni, "YYYY-MM-DD HH:mm");
                    endLembur = moment(res.scan_keluar_murni, "YYYY-MM-DD HH:mm");

                    $("#start_lembur").val(timeStartLembur);
                    $("#end_lembur").val(timeEndLembur);
                    handlingDurasiLembur();
                }
            }
        });
    }

    let startLembur, endLembur, isLemburLibur;
    let defaultStartLembur, datePlusOne, durasiLembur, isSameday, restTime, restDuration;
    document.addEventListener("DOMContentLoaded", () => {
        $(document).ready(function() {
            isSameday = 1;
            isLemburLibur = false;
            restDuration = 45;
            $("#switch-lembur").on("change", function() {
                if ($(this).is(":checked")) {
                    isSameday = 0;
                    $('#date-lembur-end').val(datePlusOne);
                    endLembur = moment(datePlusOne + " " + $("#end_lembur").val(),
                        "YYYY-MM-DD HH:mm");
                    handlingDurasiLembur()
                } else {
                    endLembur = moment(date + " " + $("#end_lembur").val(),
                        "YYYY-MM-DD HH:mm");
                    handlingDurasiLembur()
                    isSameday = 1;
                    $('#date-lembur-end').val('');
                }
            });
            $(".time-lembur").on("change", function() {
                startLembur = moment(date + " " + $("#start_lembur").val(),
                    "YYYY-MM-DD HH:mm");
                endLembur = moment(date + " " + $("#end_lembur").val(),
                    "YYYY-MM-DD HH:mm");
                if (!isSameday) {
                    endLembur = moment(datePlusOne + " " + $("#end_lembur").val(),
                        "YYYY-MM-DD HH:mm");
                }
                handlingDurasiLembur()
            });
        });
    });

    function handlingDurasiLembur() {
        if (isLemburLibur) {
            durasiLembur = Math.floor(endLembur.diff(startLembur, "minutes", true));
            restTimeSiang = moment(date + " 12:00", "YYYY-MM-DD HH:mm");
            restTimeMalam = moment(date + " 18:00", "YYYY-MM-DD HH:mm");
            if (startLembur < restTimeSiang && endLembur > restTimeSiang) {
                durasiLembur -= restDuration;
            } else if (startLembur < restTimeMalam && endLembur > restTimeMalam) {
                durasiLembur -= restDuration;
            }
            $("#durasi-lembur")
                .text(konversiMenit(durasiLembur));
            if (durasiLembur < 0) {
                durasiLembur = 0;
                $("#durasi-lembur")
                    .text(konversiMenit(durasiLembur));
            }
        } else {
            durasiLembur = Math.floor(endLembur.diff(startLembur, "minutes", true));
            console.log(restTime);

            if (startLembur < restTime && endLembur > restTime) {
                durasiLembur -= restDuration;
            }
            $("#durasi-lembur")
                .text(konversiMenit(durasiLembur));

        }
    }

    function submitLembur() {
        // Validate required fields
        // if (durasiLembur < 0) {
        //     Swal.fire('Error!', 'Waktu lembur tidak valid', 'error');
        //     return;
        // }
        // if (!$('#lampiran_lembur').val()) {
        //     Swal.fire('Error!', 'Bukti lembur harus diupload', 'error');
        //     return;
        // }

        console.log(isLemburLibur);

        if (!$('#start_lembur').val()) {
            Swal.fire('Error!', 'Waktu mulai lembur harus diisi', 'error');
            return;
        }
        if (!$('#end_lembur').val()) {
            Swal.fire('Error!', 'Waktu selesai lembur harus diisi', 'error');
            return;
        }



        const formData = new FormData(document.getElementById('form-lembur'));

        // Add required data
        formData.append('_token', CSRF_TOKEN);
        formData.append('employee_id', employee_id);
        formData.append('date', $('#date-lembur').val());
        formData.append('end_date', $('#date-lembur-end').val());
        formData.append('lembur', 'terusan');
        if (isLemburLibur) {
            formData.append('lembur', 'libur');
        }
        formData.append('start_lembur', $('#start_lembur').val());
        formData.append('end_lembur', $('#end_lembur').val());
        formData.append('keterangan', $('#keterangan').val().trim());

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var url = base_url() + 'api/v1/report/perizinan/lembur';
        if (isLemburLibur) {
            url = base_url() + 'api/v1/report/perizinan/lembur-libur';
        }
        console.log(url);


        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res);
                if (res.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Lembur berhasil disimpan',
                        icon: 'success'
                    }).then(() => {
                        $('#modal-perizinan').modal('hide');
                        $("#jam_keluar").val('');
                        if (!isLemburLibur) {
                            report.shift.jam_keluar = $('#end_lembur').val();
                        }
                        tableReport.ajax.reload();
                        document.getElementById('form-lembur').reset();
                    });
                } else {
                    Swal.fire('Error!', res.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr) {
                const res = xhr.resJSON;
                Swal.fire('Error!', res?.message || 'Terjadi kesalahan', 'error');
            }
        });
    }

    function showLembur(lembur_id) {
        $.ajax({
            type: "GET",
            url: base_url() + "api/v1/report/get-lembur/" + lembur_id,
            dataType: "json",
            success: function(res) {
                console.log(res);

                var nama = res.pic.nama;
                var dibuat = moment(res.created_at).format("YYYY-MM-DD HH:mm:ss");
                var url = base_url() + "lampiran/lembur/"
                var lampiran = res.lampiran ?
                    `<a href="${url+res.lampiran}" target="_blank">Lihat Lampiran</a>` :
                    `Tidak ada lampiran`
                var jenisLembur = firstUp(res.lembur);

                var mulaiLembur = res.mulai_lembur ?
                    `<span style="color: #2ecc71;">Mulai lembur : ${res.mulai_lembur}</span> ` :
                    "";
                var selesaiLembur = res.selesai_lembur ?
                    `<span style="color: #e74c3c;"> Selesai lembur : ${res.selesai_lembur}</span>` :
                    "";
                Swal.fire({
                    title: "Informasi Lembur",
                    html: `
                    <div class="card" style="width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                        <div class="card-body pb-0">
                            <p class="card-text">Tanggal : ${res.date}</p>
                            <p class="card-text">Lembur : ${jenisLembur}</p>
                            <p class="card-text">Lampiran : ${lampiran}</p>
                            <div class="keterangan">
                                <p>${mulaiLembur}</p>
                                <p>${selesaiLembur}</p>
                            </div>
                        </div>
                        <div class="card-footer" style="background-color: #f9f9f9; border-top: 1px solid #ddd; font-size: 12px; padding: 10px;">
                            <p class="card-text">Dibuat oleh : ${nama} @ ${dibuat}</p>
                        </div>
                    </div>
                    <div class="action-button" style="text-align: center;">
                    <div class="btn btn-danger" style="margin-right: 10px;" onclick="hapusLembur(${lembur_id})">
                        <i class="fa fa-trash"></i> Hapus
                    </div>
                    <div class="btn btn-primary" style="margin-left: 10px;" onclick="Swal.close()">
                        <i class="fas fa-x"></i> Tutup
                    </div>
                    </div>
                `,
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                }).then((result) => {
                    if (result.isDismissed) {
                        // Fungsi ketika dialog ditutup
                        // console.log("Dialog ditutup");
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            },
        });
    }

    function hapusLembur(lembur_id) {
        Swal.fire({
            title: "Konfirmasi Hapus",
            text: "Apakah Anda yakin ingin menghapus lembur ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fungsi ketika tombol hapus diklik
                var url =
                    base_url() + "api/v1/report/delete-lembur/" + lembur_id;
                $.ajax({
                    type: "GET",
                    url: url, // URL untuk menghapus lembur
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Lembur berhasil dihapus!',
                        });
                        // Reload tabel
                        tableReport.ajax.reload();
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            } else {
                // Fungsi ketika tombol batal diklik
                console.log("Hapus lembur dibatalkan");
            }
        });
    }

    function confirmLembur(report_id) {
        $.ajax({
            url: base_url() + `api/v1/report/get-report/${report_id}`,
            type: "GET",
            success: function(res) {
                report = res;
                defaultStartLembur = moment(report.shift.jam_keluar, "HH:mm").add(15, 'minutes');
                datePlusOne = moment(report.date, "YYYY-MM-DD").add(1, 'days').format(
                    "YYYY-MM-DD");
                startLembur = moment(report.date + " " + defaultStartLembur.format("HH:mm"),
                    "YYYY-MM-DD HH:mm");
                endLembur = moment(report.scan_keluar_murni, "YYYY-MM-DD HH:mm");
                restTime = moment(report.date + " 18:45", "YYYY-MM-DD HH:mm");
                durasiLemburMurni = endLembur.diff(startLembur, "minutes", true);
                durasiLemburEfektif = durasiLemburMurni;

                if (startLembur < restTime && endLembur > restTime) {
                    durasiLemburEfektif -= restDuration;
                }
                durasiLemburEfektif = Math.floor(durasiLemburEfektif / 30) * 30;

                var endDate = null;
                if (endLembur.isAfter(moment(report.date).endOf('day'))) {
                    endDate = endLembur.format("YYYY-MM-DD");
                }

                $("#date-lembur-overtime").val(report.date)
                if (endDate) {
                    $("#date-lembur-end-overtime").val(endDate)
                    $("#switch-lembur-overtime").prop('checked', true);
                }

                $("#start_overtime").val(startLembur.format("HH:mm"));
                $("#end_overtime").val(endLembur.format("HH:mm"));
                $("#durasi-overtime")
                    .text(konversiMenit(durasiLemburEfektif));
                $("#durasi-overtime-murni")
                    .text(konversiMenit(durasiLemburMurni));
                $(".custom-file-label").text("Choose file (pdf/jpeg/png)");

                $("#overtimeModal").appendTo("body").modal("show");
            },
        });

    }

    function submitConfirmLembur() {
        const formData = new FormData(document.getElementById('overtime-form'));

        // Add required data
        formData.append('_token', CSRF_TOKEN);
        formData.append('employee_id', employee_id);
        formData.append('date', $('#date-lembur-overtime').val());
        formData.append('end_date', $('#date-lembur-end-overtime').val());
        formData.append('lembur', 'terusan');
        formData.append('start_lembur', $('#start_overtime').val());
        formData.append('end_lembur', $('#end_overtime').val());
        formData.append('keterangan', $('#keterangan-overtime').val().trim());

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: base_url() + 'api/v1/report/perizinan/confirm-lembur',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Lembur berhasil disimpan',
                        icon: 'success'
                    }).then(() => {
                        $('#overtimeModal').modal('hide');
                        tableReport.ajax.reload();
                        document.getElementById('overtime-form').reset();
                    });
                } else {
                    Swal.fire('Error!', res.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr) {
                const res = xhr.resJSON;
                Swal.fire('Error!', res?.message || 'Terjadi kesalahan', 'error');
            }
        });
    }
</script>
