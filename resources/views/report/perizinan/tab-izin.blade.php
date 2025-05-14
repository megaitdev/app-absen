<form enctype="multipart/form-data" id="form-izin">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $employee->nama }}" disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="date">Tanggal</label>
            <input type="text" class="form-control" id="date-izin" name="date" value="" disabled>
        </div>
    </div>
    <div class="form-group">
        <label for="jenis_izin">Jenis Izin</label>
        <select class="form-control select2" id="jenis_izin" name="jenis_izin" required>
            <option value="">Pilih Jenis Izin</option>
            @foreach ($jenis_izin as $ji)
                <option value="{{ $ji->id }}">{{ $ji->izin }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-row" id="datepair">
        <div class="form-group col-md-6">
            <label for="start_time">Mulai</label>
            <div class="input-group">
                <input type="text" class="form-control time start" id="start_time" name="start_time" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                </div>
            </div>
            <label class="custom-switch mt-1 p-0">
                <input type="checkbox" name="custom-switch-checkbox" id="switch-izin" class="custom-switch-input">
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description">Full day</span>
            </label>
        </div>
        <div class="form-group col-md-6">
            <label for="end_time">Selesai</label>
            <div class="input-group">
                <input type="text" class="form-control time end" id="end_time" name="end_time" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                </div>
            </div>
            <small class="form-text text-muted">Waktu izin: <span id="time-difference"></span></small>
        </div>
    </div>
    <div class="form-group">
        <label for="file">Lampiran</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="lampiran_izin" name="lampiran_izin"
                accept="application/pdf,image/*" onchange="validateFile(this)">
            <label class="custom-file-label" for="lampiran_izin">Choose file
                (pdf/jpeg/png)</label>
        </div>
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea class="form-control h-100" id="keterangan" name="keterangan" rows="2"></textarea>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary float-right" onclick="submitIzin()"><i
                class="fas fa-check mr-1"></i>Izin</button>
    </div>
</form>

<script>
    function initTabIzin() {
        $("#form-izin")[0].reset();
        $("#jenis_izin").val('').trigger("change");
        $("#time-difference").text('');
        $("#date-izin").val(date);
        is_full_day = 0;
    }

    function submitIzin() {

        // Validasi required fields
        if (is_full_day == 1) {
            durasiIzin = report.shift.total_menit_kerja;
        }
        if (durasiIzin <= 0) {
            Swal.fire('Error!', 'Waktu izin tidak valid', 'error');
            return;
        }

        const formData = new FormData(document.getElementById('form-izin'));

        // Add required data
        formData.append('_token', CSRF_TOKEN);
        formData.append('employee_id', employee_id);
        formData.append('date', $('#date-izin').val());
        formData.append('jenis_izin', $('#jenis_izin').val());
        formData.append('start_time', $('#start_time').val());
        formData.append('end_time', $('#end_time').val());
        formData.append('is_full_day', is_full_day);
        formData.append('durasi', durasiIzin);
        formData.append('keterangan', $('#keterangan').val().trim());


        // Show loading state
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: base_url() + 'api/v1/report/perizinan/izin',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Izin berhasil disimpan',
                        icon: 'success'
                    }).then(() => {
                        $('#modal-perizinan').modal('hide');
                        tableReport.ajax.reload();
                        document.getElementById('form-izin').reset();
                    });
                } else {
                    Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Terjadi kesalahan', 'error');
            }
        });
    }

    function showIzin(izin_id) {
        $.ajax({
            type: "GET",
            url: base_url() + "api/v1/report/get-izin/" + izin_id,
            dataType: "json",
            success: function(res) {
                console.log(res);

                var nama = res.pic.nama;
                var dibuat = moment(res.created_at).format("YYYY-MM-DD HH:mm:ss");
                var url = base_url() + "lampiran/izin/"
                var lampiran = res.lampiran ?
                    `<a href="${url+res.lampiran}" target="_blank">Lihat Lampiran</a>` :
                    `Tidak ada lampiran`
                var jenisIzin = res.jenis_izin.izin.replace('Izin', '');

                var mulaiIzin = res.mulai_izin ?
                    `<span style="color: #2ecc71;">Mulai izin : ${res.mulai_izin}</span> ` :
                    "";
                var selesaiIzin = res.selesai_izin ?
                    `<span style="color: #e74c3c;"> Selesai izin : ${res.selesai_izin}</span>` :
                    "";
                Swal.fire({
                    title: "Informasi Izin",
                    html: `
                    <div class="card" style="width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                        <div class="card-body pb-0">
                            <p class="card-text">Tanggal : ${res.date}</p>
                            <p class="card-text">Izin : ${jenisIzin}</p>
                            <p class="card-text">Lampiran : ${lampiran}</p>
                            <div class="keterangan">
                                <p>${mulaiIzin}</p>
                                <p>${selesaiIzin}</p>
                            </div>
                        </div>
                        <div class="card-footer" style="background-color: #f9f9f9; border-top: 1px solid #ddd; font-size: 12px; padding: 10px;">
                            <p class="card-text">Dibuat oleh : ${nama} @ ${dibuat}</p>
                        </div>
                    </div>
                    <div class="action-button" style="text-align: center;">
                    <div class="btn btn-danger" style="margin-right: 10px;" onclick="hapusIzin(${izin_id})">
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

    function hapusIzin(izin_id) {
        Swal.fire({
            title: "Konfirmasi Hapus",
            text: "Apakah Anda yakin ingin menghapus izin ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fungsi ketika tombol hapus diklik
                var url =
                    base_url() + "api/v1/report/delete-izin/" + izin_id;
                $.ajax({
                    type: "GET",
                    url: url, // URL untuk menghapus izin
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Izin berhasil dihapus!',
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
                console.log("Hapus izin dibatalkan");
            }
        });
    }

    let is_full_day = 0,
        durasiIzin = 0,
        jenis_id;
    document.addEventListener("DOMContentLoaded", () => {
        $(document).ready(function() {

            $('#jenis_izin').change(function() {
                jenis_id = $(this).val();
                $('#start_time').prop('disabled', false);
                if ($(this).val() > 2) {
                    $('#datepair').find('label.custom-switch').show();
                } else {
                    $('#datepair').find('label.custom-switch').hide();
                }
                switch ($(this).val()) {
                    case "1":
                        $("#datepair .start").timepicker({
                            minTime: report.shift.jam_masuk,
                            maxTime: report.shift.jam_keluar,
                            timeFormat: "H:i",
                            step: 15,
                            disableTimeRanges: [
                                ["12pm", "12:45pm"]
                            ],
                        });

                        $("#datepair .end").timepicker({
                            timeFormat: "H:i",
                            setTime: report.shift.jam_keluar,
                        });

                        $('#end_time').prop('disabled', true).val(report.shift.jam_keluar);
                        break;
                    case '2':
                        $("#datepair .start").timepicker({
                            scrollDefault: "now",
                            minTime: report.shift.jam_masuk,
                            maxTime: report.shift.jam_keluar,
                            timeFormat: "H:i",
                            step: 15,
                            disableTimeRanges: [
                                ["12pm", "12:45pm"]
                            ],
                        });
                        $("#datepair .end").timepicker({
                            scrollDefault: "now",
                            minTime: report.shift.jam_masuk,
                            maxTime: report.shift.jam_keluar,
                            timeFormat: "H:i",
                            step: 15,
                            disableTimeRanges: [
                                ["12:01pm", "12:45pm"]
                            ],
                        });
                        $('#end_time').prop('disabled', false);
                        break;
                    default:
                        $("#datepair .start").timepicker({
                            scrollDefault: "now",
                            minTime: report.shift.jam_masuk,
                            maxTime: report.shift.jam_keluar,
                            timeFormat: "H:i",
                            step: 15,
                            disableTimeRanges: [
                                ["12pm", "12:45pm"]
                            ],
                        });
                        $("#datepair .end").timepicker({
                            // setTime: report.shift.jam_keluar,
                            scrollDefault: "now",
                            minTime: report.shift.jam_masuk,
                            maxTime: report.shift.jam_keluar,
                            timeFormat: "H:i",
                            step: 15,
                            disableTimeRanges: [
                                ["12:01pm", "12:45pm"]
                            ],
                        });
                        $('#end_time').prop('disabled', false);
                }
            });

            // Add event listener for changes on time start or time end
            $("#datepair .time").on("change", function() {
                startTime = moment($("#start_time").val(), "H:mm");
                if (jenis_id == "1") {
                    endTime = moment(report.shift.jam_keluar, "H:mm");
                } else {
                    endTime = moment($("#end_time").val(), "H:mm");
                }
                var restTime = moment(report.shift.jam_mulai_istirahat,
                    "H:mm");

                durasiIzin = Math.floor(endTime.diff(startTime, "minutes",
                    true));
                if (startTime < restTime && endTime > restTime) {
                    durasiIzin -= report.shift.total_menit_istirahat;
                }

                var selisih = konversiMenit(durasiIzin);
                $("#time-difference")
                    .text(selisih);
            });

            // Add event listener for the custom switch checkbox
            $("input[name='custom-switch-checkbox']").on("change", function() {
                if ($(this).is(":checked")) {
                    // If checked, disable the start_time and end_time input then set its value
                    $('#end_time').prop('disabled', true).val(report.shift.jam_keluar);
                    $('#start_time').prop('disabled', true).val(report.shift.jam_masuk);
                    startTime = moment(report.shift.jam_masuk, "H:mm");
                    endTime = moment(report.shift.jam_keluar, "H:mm");
                    is_full_day = 1;
                    var selisih = konversiMenit(report.shift.total_menit_kerja);
                    $("#time-difference")
                        .text(selisih);
                } else {
                    // If unchecked, enable the start_time and end_time input
                    $('#start_time').prop('disabled', false);
                    $('#end_time').prop('disabled', false);
                    is_full_day = 0;
                }
            });
        });
    });
</script>
