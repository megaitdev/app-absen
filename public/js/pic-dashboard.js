function loadRecentActivities() {
  showLoading("#recent-activities-container");

  $.ajax({
    url: base_url() + "api/pic/dashboard/recent-activities",
    method: "GET",
    success: function (response) {
      if (response.success) {
        renderRecentActivities(response.data);
      } else {
        showError("#recent-activities-container", "Gagal memuat aktivitas");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading recent activities:", error);
      showError(
        "#recent-activities-container",
        "Terjadi kesalahan saat memuat data"
      );
    },
  });
}

function renderRecentActivities(activities) {
  let html = "";

  if (activities.length === 0) {
    html =
      '<li class="text-center text-muted py-3">Tidak ada aktivitas terbaru</li>';
  } else {
    activities.forEach(function (activity) {
      html += `
                <li class="media">
                    <img class="mr-3 rounded-circle" width="50"
                         src="${base_url()}img/avatar/${activity.avatar}"
                         alt="avatar"
                         onerror="this.src='${base_url()}img/avatar/default-avatar.png'">
                    <div class="media-body">
                        <div class="float-right text-small text-muted">
                            ${activity.waktu}
                        </div>
                        <div class="media-title">${activity.nama}</div>
                        <span class="text-small text-muted">
                            ${activity.aktivitas}
                            <div class="bullet"></div>
                            ${activity.unit}
                            ${
                              activity.status
                                ? `<span class="badge badge-${getStatusColor(
                                    activity.status
                                  )} ml-1">${activity.status}</span>`
                                : ""
                            }
                        </span>
                    </div>
                </li>
            `;
    });
  }

  $("#recent-activities-list").html(html);
  hideLoading("#recent-activities-container");
}

function getStatusColor(status) {
  switch (status) {
    case "approved":
      return "success";
    case "rejected":
      return "danger";
    case "pending":
      return "warning";
    default:
      return "secondary";
  }
}

function showLoading(container) {
  $(container).find(".card-body").html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data...</p>
        </div>
    `);
}

function hideLoading(container) {
  // Loading will be replaced by actual content
}

function showError(container, message) {
  $(container).find(".card-body").html(`
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
            <p class="text-muted">${message}</p>
            <button class="btn btn-sm btn-primary" onclick="loadRecentActivities()">
                <i class="fas fa-redo mr-1"></i>Coba Lagi
            </button>
        </div>
    `);
}

// Load dashboard statistics
function loadDashboardStats() {
  $.ajax({
    url: base_url() + "api/pic/dashboard/stats",
    method: "GET",
    success: function (response) {
      if (response.success) {
        renderDashboardStats(response.data);
      } else {
        console.error("Failed to load dashboard stats");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading dashboard stats:", error);
    },
  });
}

function renderDashboardStats(stats) {
  // Update statistic cards
  $(".card-statistic-1").each(function () {
    const cardTitle = $(this).find(".card-header h4").text().trim();

    switch (cardTitle) {
      case "Total Karyawan":
        $(this).find(".card-body").text(stats.totalKaryawan);
        break;
      case "Kehadiran":
        $(this).find(".card-body").text(stats.persentaseKehadiran);
        break;
      case "Lembur":
        $(this).find(".card-body").text(stats.persentaseLembur);
        break;
      case "Ketidakhadiran":
        $(this).find(".card-body").text(stats.persentaseKetidakhadiran);
        break;
    }
  });

  // Update absence chart legend data
  updateAbsenceChartLegend(stats);

  // Update absence chart
  updateAbsenceChart(stats);
}

function updateAbsenceChartLegend(stats) {
  // Update the legend percentages in the absence chart section
  const legendItems = [
    { selector: ".bullet-success", value: stats.persentaseCuti },
    { selector: ".bullet-primary", value: stats.persentaseIzin },
    { selector: ".bullet-warning", value: stats.persentaseSakit },
    { selector: ".bullet-info", value: stats.persentaseVerifikasi },
  ];

  legendItems.forEach(function (item) {
    const element = $(item.selector).closest(".d-flex").find("span").last();
    if (element.length) {
      element.text(item.value);
    }
  });
}

function updateAbsenceChart(stats) {
  // Prepare chart data
  const chartData = [
    parseInt(stats.persentaseCuti) || 0,
    parseInt(stats.persentaseIzin) || 0,
    parseInt(stats.persentaseSakit) || 0,
    parseInt(stats.persentaseVerifikasi) || 0,
  ];

  // Store data globally
  window.absenceChartData = chartData;

  // Update absence chart with real data if chart exists
  if (
    typeof window.absenceChart !== "undefined" &&
    window.absenceChart !== null &&
    window.absenceChart.data &&
    window.absenceChart.data.datasets &&
    window.absenceChart.data.datasets[0]
  ) {
    window.absenceChart.data.datasets[0].data = chartData;
    window.absenceChart.update();
    console.log("Absence chart updated successfully");
  } else {
    console.log(
      "Absence chart not ready, data stored for later initialization"
    );
  }
}

// Load unit attendance chart
function loadUnitAttendanceChart() {
  $.ajax({
    url: base_url() + "api/pic/dashboard/unit-attendance-chart",
    method: "GET",
    success: function (response) {
      if (response.success) {
        renderUnitAttendanceChart(response.data);
      } else {
        console.error("Failed to load unit attendance chart");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading unit attendance chart:", error);
    },
  });
}

function renderUnitAttendanceChart(chartData) {
  // Store chart data globally for chart initialization
  window.unitChartData = chartData;

  // If chart already exists, update it
  if (
    typeof window.unitAttendanceChart !== "undefined" &&
    window.unitAttendanceChart !== null &&
    window.unitAttendanceChart.data &&
    window.unitAttendanceChart.data.datasets &&
    window.unitAttendanceChart.data.datasets[0]
  ) {
    window.unitAttendanceChart.data.labels = chartData.labels;
    window.unitAttendanceChart.data.datasets[0].data = chartData.data;
    window.unitAttendanceChart.update();
    console.log("Unit attendance chart updated successfully");
  } else {
    // Chart not ready yet, store data for later initialization
    console.log(
      "Unit attendance chart not ready, data stored for later initialization"
    );
  }
}

// Load unit performance table
function loadUnitPerformance() {
  $.ajax({
    url: base_url() + "api/pic/dashboard/unit-performance",
    method: "GET",
    success: function (response) {
      if (response.success) {
        renderUnitPerformance(response.data);
      } else {
        console.error("Failed to load unit performance");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error loading unit performance:", error);
    },
  });
}

function renderUnitPerformance(units) {
  let html = "";

  if (units.length === 0) {
    html =
      '<tr><td colspan="8" class="text-center text-muted">Tidak ada data unit</td></tr>';
  } else {
    units.forEach(function (unit) {
      const statusClass = getUnitStatusClass(unit.status);

      html += `
                <tr>
                    <td>${unit.nama}</td>
                    <td>${unit.total}</td>
                    <td>
                        <div class="progress mb-1" data-height="4" data-toggle="tooltip" title="${unit.kehadiran}">
                            <div class="progress-bar bg-success" style="width: ${unit.kehadiran_raw}%"></div>
                        </div>
                        ${unit.kehadiran}
                    </td>
                    <td>${unit.cuti}</td>
                    <td>${unit.izin}</td>
                    <td>${unit.sakit}</td>
                    <td>${unit.lembur}</td>
                    <td>
                        <div class="badge badge-${statusClass}">${unit.status}</div>
                    </td>
                </tr>
            `;
    });
  }

  $("#unit-performance-table tbody").html(html);

  // Reinitialize tooltips
  $('[data-toggle="tooltip"]').tooltip();
}

function getUnitStatusClass(status) {
  switch (status) {
    case "Baik":
      return "success";
    case "Perlu Perhatian":
      return "warning";
    case "Bermasalah":
      return "danger";
    default:
      return "secondary";
  }
}

// Function to initialize chart with stored data
function initializeChartsWithData() {
  // Initialize unit attendance chart with stored data
  if (
    window.unitChartData &&
    typeof window.unitAttendanceChart !== "undefined" &&
    window.unitAttendanceChart !== null &&
    window.unitAttendanceChart.data &&
    window.unitAttendanceChart.data.datasets &&
    window.unitAttendanceChart.data.datasets[0]
  ) {
    window.unitAttendanceChart.data.labels = window.unitChartData.labels;
    window.unitAttendanceChart.data.datasets[0].data =
      window.unitChartData.data;
    window.unitAttendanceChart.update();
    console.log("Unit attendance chart updated with stored data");
  }

  // Initialize absence chart with stored data
  if (
    window.absenceChartData &&
    typeof window.absenceChart !== "undefined" &&
    window.absenceChart !== null &&
    window.absenceChart.data &&
    window.absenceChart.data.datasets &&
    window.absenceChart.data.datasets[0]
  ) {
    window.absenceChart.data.datasets[0].data = window.absenceChartData;
    window.absenceChart.update();
    console.log("Absence chart updated with stored data");
  }
}

// Function to check if charts are ready and load data
function checkChartsAndLoadData() {
  let attempts = 0;
  const maxAttempts = 20; // Maximum 10 seconds (20 * 500ms)

  const checkInterval = setInterval(function () {
    attempts++;

    // Check if charts are ready
    const unitChartReady =
      typeof window.unitAttendanceChart !== "undefined" &&
      window.unitAttendanceChart !== null &&
      window.unitAttendanceChart.data;

    const absenceChartReady =
      typeof window.absenceChart !== "undefined" &&
      window.absenceChart !== null &&
      window.absenceChart.data;

    if (unitChartReady && absenceChartReady) {
      console.log("Charts are ready, loading data...");
      clearInterval(checkInterval);
      loadUnitAttendanceChart();
    } else if (attempts >= maxAttempts) {
      console.log(
        "Charts not ready after maximum attempts, loading data anyway..."
      );
      clearInterval(checkInterval);
      loadUnitAttendanceChart();
    }
  }, 500);
}

// Initialize dashboard when page loads
$(document).ready(function () {
  if (typeof loadRecentActivities === "function") {
    loadDashboardStats();
    loadUnitPerformance();
    loadRecentActivities();

    // Check charts readiness and load data
    checkChartsAndLoadData();
  }
});

// Initialize charts with data when charts are ready (called from view)
window.initializeChartsWithData = initializeChartsWithData;
