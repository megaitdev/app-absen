<form id="form-verifikasi" enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $employee->nama }}" disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="date">Tanggal</label>
            <input type="text" class="form-control" id="date-verifikasi" name="date" value="" disabled>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="status">Status</label>
            <select class="form-control select2" id="status" name="status" required>
                <option value="">- Pilih Status-</option>
                <option value="hadir">Hadir</option>
                <option value="scan_masuk">Lupa Scan Masuk</option>
                <option value="scan_keluar">Lupa Scan Keluar</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="jam_verifikasi">Jam Masuk</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                        <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" value=""
                            required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="jam_verifikasi">Jam Keluar</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                        <input type="text" class="form-control" id="jam_keluar" name="jam_keluar" value=""
                            required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="file">Bukti</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="lampiran_verifikasi" name="lampiran_verifikasi"
                accept="application/pdf,image/*" onchange="validateFile(this)">
            <label class="custom-file-label" for="lampiran_verifikasi">Choose file (pdf/jpeg/png)</label>
        </div>
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea class="form-control h-100" id="keterangan" name="keterangan" rows="2"></textarea>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                class="fas fa-times mr-1"></i>Close</button>
        <button type="button" class="btn btn-dark float-right" onclick="submitVerifikasi()"><i
                class="fas fa-check mr-1"></i>Verifikasi</button>
    </div>
</form>

<script>
    function initTabVerifikasi() {
        $("#date-verifikasi").val(date);
        $("#jam_masuk").timepicker({
            scrollDefault: "now",
            showDuration: true,
            timeFormat: "H:i",
            step: 15,
            disableTimeRanges: [
                ["12pm", "12:45pm"]
            ],
        });
        $("#jam_keluar").timepicker({
            scrollDefault: "now",
            showDuration: true,
            timeFormat: "H:i",
            step: 15,
            disableTimeRanges: [
                ["12pm", "12:45pm"]
            ],
        });
        $("#jam_masuk").prop("disabled", false);
        $("#jam_keluar").prop("disabled", false);
        if (report.scan_masuk_murni == null && report.scan_keluar_murni == null) {
            $("#status").val("hadir").trigger("change");
            $("#jam_masuk").val(report.shift.jam_masuk);
            $("#jam_keluar").val(report.shift.jam_keluar);
        }
        if (report.scan_masuk_murni) {
            $("#status").val("scan_keluar").trigger("change");
            $("#jam_keluar").val(report.shift.jam_keluar);
            $("#jam_masuk").prop("disabled", true);
        }
        if (report.scan_keluar_murni) {
            $("#status").val("scan_masuk").trigger("change");
            $("#jam_masuk").val(report.shift.jam_masuk);
            $("#jam_keluar").prop("disabled", true);
        }
    }

    function submitVerifikasi() {
        // Validate required fields
        // if (!$('#lampiran_verifikasi').val()) {
        //     Swal.fire('Error!', 'Bukti verifikasi harus diupload', 'error');
        //     return;
        // }

        // if (!$('#keterangan').val().trim()) {
        //     Swal.fire('Error!', 'Keterangan harus diisi', 'error');
        //     return;
        // }

        const formData = new FormData(document.getElementById('form-verifikasi'));

        // Add required data
        formData.append('_token', CSRF_TOKEN);
        formData.append('employee_id', employee_id);
        formData.append('date', $('#date-verifikasi').val());
        formData.append('status', $('#status').val());
        formData.append('jam_masuk', $('#jam_masuk').val());
        formData.append('jam_keluar', $('#jam_keluar').val());
        formData.append('keterangan', $('#keterangan').val().trim());


        // alert($('#jam_masuk').val());
        // alert($('#jam_keluar').val());
        // Show loading state
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: base_url() + 'api/v1/report/perizinan/verifikasi',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Verifikasi berhasil disimpan',
                        icon: 'success'
                    }).then(() => {
                        $('#modal-perizinan').modal('hide');
                        tableReport.ajax.reload();
                        document.getElementById('form-verifikasi').reset();
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

    function showVerifikasi(verifikasi_id) {
        $.ajax({
            type: "GET",
            url: base_url() + "api/v1/report/get-verifikasi/" + verifikasi_id,
            dataType: "json",
            success: function(res) {
                var nama = res.pic.nama;
                var dibuat = moment(res.created_at).format("YYYY-MM-DD HH:mm:ss");
                var jenisHadir = "";
                var url = base_url() + "lampiran/verifikasi/"
                var lampiran = res.lampiran ?
                    `<a href="${url+res.lampiran}" target="_blank">Lihat Lampiran</a>` :
                    `Tidak ada lampiran`

                if (res.jenis === "hadir") {
                    jenisHadir = "Kehadiran";
                } else if (res.jenis === "scan_masuk") {
                    jenisHadir = "Scan Masuk";
                } else if (res.jenis === "scan_keluar") {
                    jenisHadir = "Scan Keluar";
                }
                var jamMasuk = res.jam_masuk ?
                    `<span style="color: #2ecc71;">Scan Masuk: ${res.jam_masuk}</span> ` :
                    "";
                var jamKeluar = res.jam_keluar ?
                    `<span style="color: #e74c3c;">| Scan Keluar: ${res.jam_keluar}</span>` :
                    "";
                Swal.fire({
                    title: "Informasi Verifikasi",
                    html: `
                    <div class="card" style="width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                    <div class="card-body pb-0">
                        <p class="card-text">Tanggal : ${res.date}</p>
                        <p class="card-text">Verifikasi : ${jenisHadir}</p>
                        <p class="card-text">Lampiran : ${lampiran}</p>
                        <div class="keterangan">
                        <p>
                            ${jamMasuk}
                            ${jamKeluar}
                        </p>
                        </div>
                    </div>
                    <div class="card-footer" style="background-color: #f9f9f9; border-top: 1px solid #ddd; font-size: 12px; padding: 10px;">
                        <p class="card-text">Dibuat oleh : ${nama} @ ${dibuat}</p>
                    </div>
                    </div>
                    <div class="action-button" style="text-align: center;">
                    <div class="btn btn-danger" style="margin-right: 10px;" onclick="hapusVerifikasi(${verifikasi_id})">
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

    function hapusVerifikasi(verifikasi_id) {
        Swal.fire({
            title: "Konfirmasi Hapus",
            text: "Apakah Anda yakin ingin menghapus verifikasi ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fungsi ketika tombol hapus diklik
                var url =
                    base_url() + "api/v1/report/delete-verifikasi/" + verifikasi_id;
                $.ajax({
                    type: "GET",
                    url: url, // URL untuk menghapus verifikasi
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Verifikasi berhasil dihapus!',
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
                console.log("Hapus verifikasi dibatalkan");
            }
        });
    }
</script>
