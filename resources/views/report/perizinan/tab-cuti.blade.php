<form id="form-cuti" enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $employee->nama }}" disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="date">Tanggal</label>
            <input type="text" class="form-control" id="date-cuti" name="date" value="" disabled>
        </div>
    </div>
    <div class="form-group">
        <label for="jenis_cuti">Jenis Cuti</label>
        <select class="form-control select2" id="jenis_cuti" name="jenis_cuti" required>
            @foreach ($jenis_cuti as $jc)
                <option value="{{ $jc->id }}">{{ $jc->cuti }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="file">File</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="lampiran_cuti" name="lampiran_cuti"
                accept="application/pdf,image/*" onchange="validateFile(this)">
            <label class="custom-file-label" for="lampiran_cuti">Choose
                file (pdf/jpeg/png)</label>
        </div>
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea class="form-control h-100" id="keterangan" name="keterangan" rows="2"></textarea>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                class="fas fa-times mr-1"></i>Close</button>
        <button type="button" class="btn btn-primary float-right" onclick="submitCuti()"><i
                class="fas fa-check mr-1"></i>Cuti</button>
    </div>
</form>


<script>
    function initTabCuti() {
        document.getElementById("form-cuti").reset();
        $("#jenis_cuti").val(1).trigger("change");
        $("#date-cuti").val(date);
    }

    function submitCuti() {
        // Validasi lampiran cuti
        if (!$('#lampiran_cuti').val()) {
            Swal.fire('Error!', 'Bukti cuti harus diupload', 'error');
            return;
        }

        // Ambil data dari form
        var formData = new FormData(document.getElementById('form-cuti'));

        // Tambahkan data ke formData
        formData.append('employee_id', employee_id);
        formData.append('date', $('#date-verifikasi').val());

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // // Kirim data menggunakan AJAX
        $.ajax({
            url: base_url() + 'api/v1/report/perizinan/cuti',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Cuti berhasil disimpan',
                        icon: 'success'
                    }).then(() => {
                        $('#modal-perizinan').modal('hide');
                        tableReport.ajax.reload();
                        initStatistikKehadiran();
                        document.getElementById('form-cuti').reset();
                        $('#jenis_cuti').val(1).trigger('change');
                        document.getElementById('lampiran_cuti').value = '';
                        document.querySelector('.custom-file-label').innerHTML =
                            'Choose file (pdf/jpeg/png)';
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

    function showCuti(cuti_id) {
        $.ajax({
            type: 'GET',
            url: base_url() + "api/v1/report/get-cuti/" + cuti_id,
            dataType: 'json',
            success: function(res) {
                var dibuat = moment(res.created_at).format("YYYY-MM-DD HH:mm:ss");
                var url = base_url() + "lampiran/cuti/"
                var lampiran = res.lampiran ?
                    `<a href="${url+res.lampiran}" target="_blank">Lihat Lampiran</a>` :
                    `Tidak ada lampiran`

                Swal.fire({
                    title: "Informasi Cuti",
                    html: `
                    <div class="card" style="width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                        <div class="card-body pb-3">
                            <p class="card-text">Tanggal : ${res.date}</p>
                            <p class="card-text">Cuti : ${res.jenis_cuti.cuti}</p>
                            <p class="card-text">Lampiran : ${lampiran}</p>
                        </div>
                        <div class="card-footer" style="background-color: #f9f9f9; border-top: 1px solid #ddd; font-size: 12px; padding: 10px;">
                            <p class="card-text">Dibuat oleh: ${res.pic.nama} @ ${dibuat}</p>
                        </div>
                        </div>
                        <div class="action-button" style="text-align: center;">
                        <div class="btn btn-danger" style="margin-right: 10px;" onclick="hapusCuti(${res.id})">
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
                        document.getElementById('form-cuti').reset();
                        $('#jenis_cuti').val(1).trigger('change');
                    }
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat informasi cuti',
                    icon: 'error',
                });
            }
        });
    }

    function hapusCuti(cuti_id) {
        Swal.fire({
            title: "Konfirmasi Hapus",
            text: "Apakah Anda yakin ingin menghapus cuti ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                // Fungsi ketika tombol hapus diklik
                var url =
                    base_url() + "api/v1/report/delete-cuti/" + cuti_id;
                $.ajax({
                    type: "GET",
                    url: url, // URL untuk menghapus cuti
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Cuti berhasil dihapus!',
                        });
                        // Reload tabel
                        tableReport.ajax.reload();
                        initStatistikKehadiran();
                        document.getElementById('form-cuti').reset();
                        $('#jenis_cuti').val(1).trigger('change');
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error!', response?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            } else {
                // Fungsi ketika tombol batal diklik
                console.log("Hapus cuti dibatalkan");

            }
        });
    }
</script>
