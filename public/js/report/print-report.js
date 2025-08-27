document.addEventListener("DOMContentLoaded", () => {
  $(document).ready(function () {
    // Init Periode => (initDaterangePicker, initTable))
    initPeriode();
    picID = $("#pic_id").val();

    // Filters: Apply & Reset
    $(document).on("click", "#apply-report-filter", async function () {
      await applyReportFilter();
    });
    $(document).on("click", "#reset-report-filter", function () {
      resetReportFilter();
    });

    $(document).on("change", "#filterUnitSelect", async function () {
      selectedUnits = $(this).val().map(Number);
    });
  });
});

let year,
  periode,
  picID,
  selectedEmployees,
  selectedUnits,
  employees,
  units,
  infoPic,
  infoUnit,
  infoPangkat,
  infoPeriode,
  infoGaji,
  infoCetak,
  infoMengelola;

function initPeriode() {
  $.ajax({
    url: base_url() + "report/ajax/get-periode",
    type: "GET",
    dataType: "json",
    success: function (res) {
      periode = res;
      year = moment(periode.end).format("YYYY");

      initDaterangePicker();

      initReportTable();
    },
  });
}

async function initReportTable() {
  await $.ajax({
    url: base_url() + "api/v1/report/get-report-pic/" + picID,
    type: "GET",
    dataType: "json",
    success: function (res) {
      let report = res.data;
      console.log(report);
      selectedEmployees = report.employeesIDs;
      selectedUnits = Object.keys(report.unit).map(Number);
      employees = report.reports;
      units = report.unit;

      infoUnit = report.unit_names;
      infoPangkat = report.pangkat;
      infoPeriode =
        moment(periode.start).format("YYYY/MM/DD") +
        " - " +
        moment(periode.end).format("YYYY/MM/DD");
      infoGaji = `Bulan ${report.periode.name}`;
      infoPic = report.pic.nama;
      infoMengelola = selectedEmployees.length;

      loadEmployeesForSelect();
      loadUnitForSelect();
      loadReportTable();
      setInfoLabel();
    },
  });
}

function loadReportTable() {
  const $tbody = $("#report-tbody");
  $tbody.empty();

  // Render placeholder while loading to avoid layout shifts
  if (!selectedEmployees.length) {
    $tbody.append(
      `<tr id="table-empty">
              <td colspan="12" class="text-center ">
                  <div class="d-flex align-items-center justify-content-center p-3">
                      <span>Data Tidak Ditemukan...</span>
                  </div>
              </td>
          </tr>`
    );
    return;
  }

  employees.forEach((emp, index) => {
    console.log(emp);

    const tr = document.createElement("tr");
    tr.innerHTML = `<td>${index + 1}</td>
                    <td class="nik">${emp.nip || "-"}</td>
                    <td class="nama">${emp.nama}</td>
                    <td>${emp.stats.total_ut}</td>
                    <td>${emp.stats.total_um}</td>
                    <td>${emp.stats.total_uk}</td>
                    <td>${emp.stats.total_jam_potongan}</td>
                    <td>${emp.stats.total_utl}</td>
                    <td>${emp.stats.total_uml}</td>
                    <td>${emp.stats.total_umll}</td>
                    <td>${emp.stats.total_jam_akumulasi_lembur}</td>
                    `;

    $tbody.append(tr);
  });
}

function loadReportTable() {
  const $tbody = $("#report-tbody");
  $tbody.empty();

  if (!selectedEmployees || !selectedEmployees.length) {
    $tbody.append(
      `<tr id="table-empty">
              <td colspan="12" class="text-center ">
                  <div class="d-flex align-items-center justify-content-center p-3">
                      <span>Data Tidak Ditemukan...</span>
                  </div>
              </td>
          </tr>`
    );
    return;
  }

  let index = 1;
  employees.forEach((emp, i) => {
    const tr = document.createElement("tr");
    if (!selectedEmployees.includes(emp.employee_id)) {
      return;
    }
    tr.innerHTML = `<td>${index}</td>
                    <td class="nik">${emp.nip || "-"}</td>
                    <td class="nama">${emp.nama}</td>
                    <td>${emp.stats.total_ut}</td>
                    <td>${emp.stats.total_um}</td>
                    <td>${emp.stats.total_uk}</td>
                    <td>${emp.stats.total_jam_potongan}</td>
                    <td>${emp.stats.total_utl}</td>
                    <td>${emp.stats.total_uml}</td>
                    <td>${emp.stats.total_umll}</td>
                    <td>${emp.stats.total_jam_akumulasi_lembur}</td>`;
    index++;
    $tbody.append(tr);
  });
}

function updateDateTime() {
  const now = moment().utcOffset("+0700").format("Y/MM/DD HH:mm");
  $("#info-dicetak").html(now);
}

function setInfoLabel() {
  $("#info-pic").html(infoPic);
  $("#info-unit").html(infoUnit);
  $("#info-pangkat").html(infoPangkat);
  $("#info-periode").html(infoPeriode);
  $("#info-gaji").html(infoGaji);
  $("#info-mengelola").html(infoMengelola + " Karyawan");
  setInterval(updateDateTime, 1000);
  updateDateTime();
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
          console.log(res);
        },
      });
    }
  );
}

async function printReport() {
  // Tampilkan loading Swal saat menyiapkan file
  try {
    Swal.fire({
      title: "Menyiapkan PDF…",
      html: "Mohon tunggu, kami sedang membuat file laporan.",
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const rowsPerPage = 25;
    const CAPTURE_WIDTH = 1800; // lebar standar konsisten (basis desain 1600px)
    const originalContainer = document.getElementById("capture");
    if (!originalContainer) {
      Swal.close();
      return Swal.fire({
        icon: "warning",
        title: "Gagal",
        text: "Container laporan tidak ditemukan.",
      });
    }
    const reportTable = originalContainer.querySelector(".report-table");
    if (!reportTable) {
      Swal.close();
      return Swal.fire({
        icon: "warning",
        title: "Gagal",
        text: "Tabel laporan tidak ditemukan.",
      });
    }

    // Only include rows not hidden by filter
    const allRows = Array.from(reportTable.querySelectorAll("tbody tr")).filter(
      (r) =>
        r.querySelectorAll("td").length &&
        r.textContent.trim().length > 0 &&
        $(r).is(":visible")
    );
    if (!allRows.length) {
      Swal.close();
      return Swal.fire({
        icon: "info",
        title: "Tidak ada data",
        text: "Tidak ada baris yang dapat dicetak. Cek filter Anda.",
      });
    }

    const headerNode = originalContainer.querySelector(".header");
    const infoTablesNode = originalContainer.querySelector(".info-tables");
    const tableHead = reportTable.querySelector("thead");

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("landscape", "mm", "a4");
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
      const pageDiv = document.createElement("div");
      // Use the same scoped root class so namespaced CSS applies in html2canvas
      pageDiv.className = "report-container report-print";
      pageDiv.style.background = "#ffffff";
      // Padding bawah cukup kecil, footer akan menempel
      pageDiv.style.padding = "16px 20px 20px 20px";
      pageDiv.style.boxSizing = "border-box";
      pageDiv.style.width = CAPTURE_WIDTH + "px"; // pakai lebar tetap agar konsisten antar device
      pageDiv.style.position = "absolute";
      // Hitung tinggi piksel agar setelah skala lebar -> contentWidthMM, tingginya pas memenuhi tinggi konten PDF
      const targetPagePxHeight = Math.round(
        (contentHeightMM * CAPTURE_WIDTH) / contentWidthMM
      );
      pageDiv.style.height = targetPagePxHeight + "px";
      pageDiv.style.display = "flex";
      pageDiv.style.flexDirection = "column";
      pageDiv.style.overflow = "hidden";
      pageDiv.style.left = "-10000px"; // off-screen

      // Selalu tampilkan header penuh + info tables di setiap halaman
      if (headerNode) pageDiv.appendChild(headerNode.cloneNode(true));
      if (infoTablesNode) pageDiv.appendChild(infoTablesNode.cloneNode(true));

      // Table wrapper
      const table = document.createElement("table");
      // Avoid Bootstrap classes; rely on namespaced report styles
      table.className = "report-table";
      table.style.width = "100%";
      table.appendChild(tableHead.cloneNode(true));
      const tbody = document.createElement("tbody");
      for (let i = start; i < end; i++) {
        tbody.appendChild(allRows[i].cloneNode(true));
      }
      table.appendChild(tbody);
      const tableWrapper = document.createElement("div");
      tableWrapper.className = "report-table-wrap";
      tableWrapper.style.flex = "1";
      tableWrapper.style.display = "block";
      tableWrapper.appendChild(table);
      pageDiv.appendChild(tableWrapper);

      // Footer (HTML) dengan font Cascadia Mono (body sudah pakai, tapi eksplisitkan)
      const footer = document.createElement("div");
      footer.style.position = "absolute";
      footer.style.left = "0";
      footer.style.right = "0";
      footer.style.bottom = "6px"; // sedikit naik agar tidak terlalu menempel
      footer.style.textAlign = "center";
      footer.style.fontSize = "12px"; // sedikit lebih besar
      footer.style.lineHeight = "1.1";
      footer.style.fontFamily = "Cascadia Mono, Cascadia Code, monospace";
      footer.style.padding = "2px 0";
      footer.style.background = "transparent";
      footer.textContent = `Page ${
        pageIndex + 1
      } of ${totalPages}  |  Aplikasi Absen - MAK`;
      pageDiv.appendChild(footer);
      document.body.appendChild(pageDiv);

      await new Promise((r) => requestAnimationFrame(r));
      const canvas = await html2canvas(pageDiv, {
        scale: 2,
        useCORS: true,
        logging: false,
      });
      document.body.removeChild(pageDiv);
      return canvas;
    }

    for (let p = 0; p < totalPages; p++) {
      if (p > 0) pdf.addPage("a4", "landscape");
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
      const imgData = pageCanvas.toDataURL("image/png");
      pdf.addImage(
        imgData,
        "PNG",
        marginLeftRight,
        marginTop,
        contentWidthMM,
        imgHeightMM,
        undefined,
        "FAST"
      );
    }

    // Trigger download
    const filename = "laporan-absen.pdf";
    pdf.save(filename);

    // Tutup loading dan tampilkan sukses
    Swal.close();
    Swal.fire({
      icon: "success",
      title: "Berhasil diunduh",
      text: `File ${filename} berhasil diunduh.`,
      timer: 2000,
      showConfirmButton: false,
    });
  } catch (err) {
    console.error("Gagal membuat PDF:", err);
    Swal.close();
    Swal.fire({
      icon: "error",
      title: "Gagal mencetak",
      text: "Terjadi kesalahan saat membuat file PDF.",
    });
  }
}

// --------------------------
// Filters: Apply by Unit or Employee
// --------------------------

async function applyReportFilter() {
  selectedEmployees = filterEmployeeIdsByUnitIds(selectedUnits);
  infoUnit = selectedUnits.map((id) => units[id]).join(", ");
  infoMengelola = selectedEmployees.length;
  loadReportTable();
  setInfoLabel();
}

function filterEmployeeIdsByUnitIds(unitIds) {
  // Gunakan metode `filter()` dan `includes()` untuk memeriksa apakah unit_id
  // karyawan ada di dalam array unitIds yang diberikan.
  const filteredData = employees.filter((employee) =>
    unitIds.includes(employee.unit_id)
  );

  infoPangkat = "Pengatur";
  const hasOperator = filteredData.some((employee) => employee.pangkat === 1);
  if (hasOperator) {
    infoPangkat = "Operator-Pengatur";
  }

  // Kemudian gunakan `map()` untuk mengekstrak hanya employee_id
  const employeeIds = filteredData.map((employee) => employee.employee_id);

  return employeeIds;
}

function resetReportFilter() {
  $("#filterUnitSelect").val(null).trigger("change");
  selectedUnits = Object.keys(units).map(Number);
  selectedEmployees = filterEmployeeIdsByUnitIds(selectedUnits);
  loadReportTable();
  infoUnit = selectedUnits.map((id) => units[id]).join(", ");
  infoMengelola = selectedEmployees.length;
  setInfoLabel();
}

// --------------------------
// Filters
// --------------------------

function loadEmployeesForSelect() {
  const $sel = $("#filterEmployeeSelect");
  $sel.empty();
  employees.forEach((e) => {
    if (!selectedEmployees.includes(e.id)) {
      return;
    }
    const label = e.nip ? `${e.nama} (${e.nip})` : e.nama;
    $sel.append(`<option value="${e.id}">${label}</option>`);
  });
  if ($.fn.select2) {
    $sel.select2({
      width: "360px",
      placeholder: $sel.data("placeholder") || "Pilih Karyawan",
      allowClear: true,
    });
  }
}

function loadUnitForSelect() {
  const $sel = $("#filterUnitSelect");
  $sel.empty();
  Object.entries(units).forEach(([id, name]) => {
    $sel.append(`<option value="${id}">${name}</option>`);
  });
  if ($.fn.select2) {
    $sel.select2({
      width: "260px",
      placeholder: $sel.data("placeholder") || "Pilih Unit",
      allowClear: true,
    });
  }
}
