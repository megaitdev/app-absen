<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Absen Karyawan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/cascadia-code" rel="stylesheet"> <!-- Cascadia Mono/Code -->
        <style>
            body {
                font-family: 'Cascadia Mono', 'Cascadia Code', monospace;
                background-color: #f0f0f0;
                padding: 20px;
            }

            .report-container {
                background-color: white;
                padding: 30px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            .header {
                text-align: center;
            }

            .header h1 {
                font-size: 24px;
                font-weight: 900;
                font-family: 'Arial Black', Arial, sans-serif;
                color: #333;
            }

            .header h2 {
                font-size: 18px;
                font-weight: 700;
                font-family: 'Arial Black', Arial, sans-serif;
                color: #666;
            }

            .info-section {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .info-left,
            .info-right {
                font-size: 14px;
            }

            .info-left p {
                margin: 2px 0;
                text-align: left;
            }

            .info-right p {
                margin: 2px 0;
                text-align: right;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 12px;
                /* gunakan gap antar kolom */
            }

            .info-label {
                font-weight: bold;
                display: inline-block;
                width: 80px;
                /* Lebar untuk label kiri */
            }

            .info-label-right {
                font-weight: bold;
                display: inline-block;
                text-align: left;
                /* label kini rata kiri agar sejajar vertikal */
                width: 100px;
                /* Lebar untuk label kanan */
            }

            .info-value-right {
                display: inline-block;
                text-align: left;
                /* nilai tetap rata kiri */
                padding-left: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            /* Override: tebal 2px untuk tabel laporan */
            .report-table {
                border: 1px solid #444 !important;
            }

            .report-table th,
            .report-table td {
                border: 1px solid #444 !important;
            }

            .report-table thead th {
                background-color: #e7e7e7 !important;
                /* abu-abu muda */
            }

            /* NIK & NAMA rata kiri */
            .report-table td.nik,
            .report-table td.nama {
                text-align: left !important;
            }

            /* Fix artefak garis di html2canvas untuk header dengan rowspan */
            .report-table thead tr:nth-child(2) th {
                /* baris kedua header */
                border-top: none !important;
                /* hilangkan border yang menabrak sel rowspan */
            }

            .report-table thead th[rowspan] {
                position: relative;
            }

            /* Hilangkan garis atas baris kedua dengan transparan tanpa menutup teks */
            .report-table thead tr:nth-child(2) th {
                border-top: 1px solid transparent !important;
            }

            .table-header-group th {
                text-align: center;
                vertical-align: middle;
            }

            .table-header-group .col-title {
                background-color: #e9ecef;
            }

            /* Tabel info header (dipisah kiri & kanan) */
            .info-tables {
                display: flex;
                justify-content: space-between;
                gap: 60%;
                margin-bottom: 20px;
            }

            .info-table {
                font-size: 14px;
                border-collapse: collapse;
            }

            .info-table td {
                padding: 2px 6px;
                vertical-align: middle;
                border: none;
                /* hilangkan border untuk info */
                text-align: left;
            }

            .info-table td.label {
                font-weight: bold;
                width: 70px;
                /* diperkecil dari 90px */
                white-space: nowrap;
            }

            .info-table-right td.label {
                width: 90px;
                /* diperkecil dari 110px */
            }
        </style>
    </head>

    <body>

        <div id="capture" class="report-container" style="min-height: 1240px;">
            <div class="header">
                <h1>LAPORAN ABSEN KARYAWAN</h1>
                <h2>PIC UIT</h2>
            </div>

            <div class="info-tables">
                <table class="info-table info-table-left">
                    <tbody>
                        <tr>
                            <td class="label">UNIT</td>
                            <td>IT</td>
                        </tr>
                        <tr>
                            <td class="label">PANGKAT</td>
                            <td>Pengatur</td>
                        </tr>
                        <tr>
                            <td class="label">PERIODE</td>
                            <td>21/07/2025 – 20/08/2025</td>
                        </tr>
                        <tr>
                            <td class="label">GAJI</td>
                            <td>Agustus 2025</td>
                        </tr>
                    </tbody>
                </table>
                <table class="info-table info-table-right">
                    <tbody>
                        <tr>
                            <td class="label">PIC</td>
                            <td>Abiema Febrian Nugraha</td>
                        </tr>
                        <tr>
                            <td class="label">DICETAK</td>
                            <td>21/08/2025 13:17</td>
                        </tr>
                        <tr>
                            <td class="label">MENGELOLA</td>
                            <td>3 Karyawan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered report-table">
                    <thead class="table-header-group">
                        <tr>
                            <th rowspan="2" style="width:30px">NO</th>
                            <th rowspan="2" style="width:95px">NIK</th>
                            <th rowspan="2" style="width:170px">NAMA</th>
                            <th colspan="3" class="col-title">REGULER</th>
                            <th rowspan="2" style="width:110px">Pot. Absen (Jam)</th>
                            <th colspan="4" class="col-title">LEMBUR</th>
                        </tr>
                        <tr>
                            <th style="width:30px">UT</th>
                            <th style="width:30px">UM</th>
                            <th style="width:30px">UK</th>
                            <th style="width:30px">UTL</th>
                            <th style="width:30px">UML</th>
                            <th style="width:30px">UMLL</th>
                            <th style="width:30px">JAM LEMBUR EFEKTIF AKUMULASI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Nur Haryanto</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Adhy Syahputra</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Abiema Febrian Nugraha</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Sutan Syahrir</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Mohammad Hatta</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Ki Hajar Dewantara</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Soekarno</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Bung Tomo</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Gatot Soebroto</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Jenderal Sudirman</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>11</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Soepomo</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>12</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Tan Malaka</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>13</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">I Gusti Ngurah Rai</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>14</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Cut Nyak Dien</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>15</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Teuku Umar</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>16</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Pattimura</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>17</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Pangeran Diponegoro</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>18</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Tuanku Imam Bonjol</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>19</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Sisingamangaraja XII</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>20</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Wage Rudolf Supratman</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>21</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Dewi Sartika</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>22</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">R.A. Kartini</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>23</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Hasyim Asy'ari</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>24</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Ahmad Dahlan</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>25</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Imam Bonjol</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>26</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Pangeran Antasari</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>27</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Si Singamangaraja</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>28</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Kapitan Pattimura</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>29</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Teuku Umar</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>30</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Cut Nyak Meutia</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>31</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Sultan Hasanuddin</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>32</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Hang Tuah</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>33</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Gajah Mada</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>34</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Arya Wiraraja</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>35</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Ken Arok</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>36</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Ken Dedes</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>37</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Soekma Djaja</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>38</td>
                            <td class="nik">MAK 1022714</td>
                            <td class="nama">Trimurti</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>39</td>
                            <td class="nik">MAK 1022713</td>
                            <td class="nama">Maria Walanda Maramis</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>40</td>
                            <td class="nik">MAK 0102156</td>
                            <td class="nama">Rasuna Said</td>
                            <td>22</td>
                            <td>22</td>
                            <td>22</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- <div
                style="font-size: 11px; margin-top: 10px; text-align: right; position: absolute; bottom: 10px; right: 10px;">
                Halaman 1 dari x | Aplikasi Absen - MAK
            </div> --}}
        </div>


        </div>

        <button class="mt-5 btn btn-primary" onclick="printReport()">Cetak Laporan</button>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('library/html2canvas/html2canvas.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" referrerpolicy="no-referrer"></script>
        <script>
            async function printReport() {
                const rowsPerPage = 20;
                const CAPTURE_WIDTH = 1800; // lebar standar konsisten (basis desain 1600px)
                const originalContainer = document.getElementById('capture');
                const reportTable = originalContainer.querySelector('.report-table');
                if (!reportTable) return;

                const allRows = Array.from(reportTable.querySelectorAll('tbody tr'))
                    .filter(r => r.querySelectorAll('td').length && r.textContent.trim().length > 0);

                const headerNode = originalContainer.querySelector('.header');
                const infoTablesNode = originalContainer.querySelector('.info-tables');
                const tableHead = reportTable.querySelector('thead');

                const {
                    jsPDF
                } = window.jspdf;
                const pdf = new jsPDF('landscape', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const marginLeftRight = 6; // mm samping
                const marginTop = 6; // mm atas
                const marginBottom = 0; // mm bawah (diturunkan agar footer lebih ke bawah)
                const contentWidthMM = pageWidth - marginLeftRight * 2;
                const contentHeightMM = pageHeight - marginTop - marginBottom;
                pdf.setFontSize(10);

                const totalPages = Math.ceil(allRows.length / rowsPerPage);

                // Helper to build a single page DOM for capture
                async function renderPage(pageIndex) {
                    const start = pageIndex * rowsPerPage;
                    const end = Math.min(start + rowsPerPage, allRows.length);
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'report-container';
                    pageDiv.style.background = '#ffffff';
                    // Padding bawah cukup kecil, footer akan menempel
                    pageDiv.style.padding = '16px 20px 20px 20px';
                    pageDiv.style.boxSizing = 'border-box';
                    pageDiv.style.width = CAPTURE_WIDTH + 'px'; // pakai lebar tetap agar konsisten antar device
                    pageDiv.style.position = 'absolute';
                    // Hitung tinggi piksel agar setelah skala lebar -> contentWidthMM, tingginya pas memenuhi tinggi konten PDF
                    const targetPagePxHeight = Math.round((contentHeightMM * CAPTURE_WIDTH) / contentWidthMM);
                    pageDiv.style.height = targetPagePxHeight + 'px';
                    pageDiv.style.display = 'flex';
                    pageDiv.style.flexDirection = 'column';
                    pageDiv.style.overflow = 'hidden';
                    pageDiv.style.left = '-10000px'; // off-screen

                    // Selalu tampilkan header penuh + info tables di setiap halaman
                    if (headerNode) pageDiv.appendChild(headerNode.cloneNode(true));
                    if (infoTablesNode) pageDiv.appendChild(infoTablesNode.cloneNode(true));

                    // Table wrapper
                    const table = document.createElement('table');
                    table.className = 'table table-bordered report-table';
                    table.style.width = '100%';
                    table.appendChild(tableHead.cloneNode(true));
                    const tbody = document.createElement('tbody');
                    for (let i = start; i < end; i++) {
                        tbody.appendChild(allRows[i].cloneNode(true));
                    }
                    table.appendChild(tbody);
                    const tableWrapper = document.createElement('div');
                    tableWrapper.style.flex = '1';
                    tableWrapper.style.display = 'block';
                    tableWrapper.appendChild(table);
                    pageDiv.appendChild(tableWrapper);

                    // Footer (HTML) dengan font Cascadia Mono (body sudah pakai, tapi eksplisitkan)
                    const footer = document.createElement('div');
                    footer.style.position = 'absolute';
                    footer.style.left = '0';
                    footer.style.right = '0';
                    footer.style.bottom = '6px'; // sedikit naik agar tidak terlalu menempel
                    footer.style.textAlign = 'center';
                    footer.style.fontSize = '12px'; // sedikit lebih besar
                    footer.style.lineHeight = '1.1';
                    footer.style.fontFamily = 'Cascadia Mono, Cascadia Code, monospace';
                    footer.style.padding = '2px 0';
                    footer.style.background = 'transparent';
                    footer.textContent = `Page ${pageIndex + 1} of ${totalPages}  |  Aplikasi Absen - MAK`;
                    pageDiv.appendChild(footer);
                    document.body.appendChild(pageDiv);

                    await new Promise(r => requestAnimationFrame(r));
                    const canvas = await html2canvas(pageDiv, {
                        scale: 2,
                        useCORS: true,
                        logging: false
                    });
                    document.body.removeChild(pageDiv);
                    return canvas;
                }

                for (let p = 0; p < totalPages; p++) {
                    if (p > 0) pdf.addPage('a4', 'landscape');
                    const pageCanvas = await renderPage(p);
                    const contentWidthMM = pageWidth - marginLeftRight * 2;
                    const scale = contentWidthMM / pageCanvas.width;
                    let imgHeightMM = pageCanvas.height * scale;
                    const maxHeight = contentHeightMM; // sudah perhitungkan margin bawah 0
                    if (imgHeightMM > maxHeight) {
                        // shrink uniformly if needed
                        const shrink = maxHeight / imgHeightMM;
                        imgHeightMM = maxHeight;
                        // adjust scale accordingly
                        // scale already used only for width; width will also shrink
                    }
                    const imgData = pageCanvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', marginLeftRight, marginTop, contentWidthMM, imgHeightMM, undefined,
                        'FAST');
                }

                pdf.save('laporan-absen.pdf');
            }
        </script>
    </body>

</html>
