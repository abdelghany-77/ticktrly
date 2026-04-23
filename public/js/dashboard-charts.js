document.addEventListener("DOMContentLoaded", function () {
  // chartData is set as a global variable in the blade template
  if (typeof window.chartData === "undefined") return;

  const chartData = window.chartData;

  // Register the datalabels plugin globally
  Chart.register(ChartDataLabels);

  // Shared defaults for dark theme
  Chart.defaults.color = "#94a3b8";
  Chart.defaults.borderColor = "rgba(51, 65, 85, 0.5)";
  Chart.defaults.font.family = "system-ui, -apple-system, sans-serif";

  // ── 1. Tickets by Category (Bar Chart) ──
  new Chart(document.getElementById("categoryChart"), {
    type: "bar",
    data: {
      labels: chartData.category.labels,
      datasets: [
        {
          label: "Tickets",
          data: chartData.category.data,
          backgroundColor: [
            "rgba(59, 130, 246, 0.7)",
            "rgba(168, 85, 247, 0.7)",
            "rgba(236, 72, 153, 0.7)",
            "rgba(20, 184, 166, 0.7)",
            "rgba(245, 158, 11, 0.7)",
            "rgba(99, 102, 241, 0.7)",
            "rgba(34, 197, 94, 0.7)",
            "rgba(239, 68, 68, 0.7)",
          ],
          borderColor: [
            "rgba(59, 130, 246, 1)",
            "rgba(168, 85, 247, 1)",
            "rgba(236, 72, 153, 1)",
            "rgba(20, 184, 166, 1)",
            "rgba(245, 158, 11, 1)",
            "rgba(99, 102, 241, 1)",
            "rgba(34, 197, 94, 1)",
            "rgba(239, 68, 68, 1)",
          ],
          borderWidth: 1,
          borderRadius: 6,
          borderSkipped: false,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        datalabels: {
          anchor: "end",
          align: "top",
          color: "#e2e8f0",
          font: { weight: "bold", size: 12 },
          formatter: (value) => (value > 0 ? value : ""),
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: "#94a3b8", font: { size: 11 } },
        },
        y: {
          beginAtZero: true,
          ticks: {
            color: "#94a3b8",
            stepSize: 1,
            font: { size: 11 },
          },
          grid: { color: "rgba(51, 65, 85, 0.3)" },
        },
      },
    },
  });

  // ── 2. Tickets by Status (Pie Chart) ──
  new Chart(document.getElementById("statusChart"), {
    type: "pie",
    data: {
      labels: chartData.status.labels,
      datasets: [
        {
          data: chartData.status.data,
          backgroundColor: [
            "rgba(245, 158, 11, 0.8)", // Open - amber
            "rgba(59, 130, 246, 0.8)", // In Progress - blue
            "rgba(16, 185, 129, 0.8)", // Resolved - emerald
            "rgba(100, 116, 139, 0.8)", // Closed - slate
          ],
          borderColor: [
            "rgba(245, 158, 11, 1)",
            "rgba(59, 130, 246, 1)",
            "rgba(16, 185, 129, 1)",
            "rgba(100, 116, 139, 1)",
          ],
          borderWidth: 2,
          hoverOffset: 8,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: "#cbd5e1",
            padding: 16,
            usePointStyle: true,
            pointStyleWidth: 10,
            font: { size: 12 },
          },
        },
        datalabels: {
          color: "#fff",
          font: { weight: "bold", size: 13 },
          formatter: (value, ctx) => {
            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
            if (total === 0 || value === 0) return "";
            const pct = Math.round((value / total) * 100);
            return value + " (" + pct + "%)";
          },
        },
      },
    },
  });

  // ── 3. AI Resolution Rate (Donut Chart) ──
  const aiTotal = chartData.ai.data.reduce((a, b) => a + b, 0);

  new Chart(document.getElementById("aiChart"), {
    type: "doughnut",
    data: {
      labels: chartData.ai.labels,
      datasets: [
        {
          data: chartData.ai.data,
          backgroundColor: [
            "rgba(139, 92, 246, 0.8)", // AI - violet
            "rgba(59, 130, 246, 0.8)", // Agent - blue
          ],
          borderColor: ["rgba(139, 92, 246, 1)", "rgba(59, 130, 246, 1)"],
          borderWidth: 2,
          hoverOffset: 8,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "55%",
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: "#cbd5e1",
            padding: 16,
            usePointStyle: true,
            pointStyleWidth: 10,
            font: { size: 12 },
          },
        },
        datalabels: {
          color: "#fff",
          font: { weight: "bold", size: 13 },
          formatter: (value, ctx) => {
            if (aiTotal === 0 || value === 0) return "";
            const pct = Math.round((value / aiTotal) * 100);
            return value + " (" + pct + "%)";
          },
        },
      },
    },
  });

  // ── 4. Tickets by Priority (Horizontal Bar Chart) ──
  new Chart(document.getElementById("priorityChart"), {
    type: "bar",
    data: {
      labels: chartData.priority.labels,
      datasets: [
        {
          label: "Tickets",
          data: chartData.priority.data,
          backgroundColor: [
            "rgba(239, 68, 68, 0.7)", // High - red
            "rgba(245, 158, 11, 0.7)", // Medium - amber
            "rgba(59, 130, 246, 0.7)", // Low - blue
          ],
          borderColor: [
            "rgba(239, 68, 68, 1)",
            "rgba(245, 158, 11, 1)",
            "rgba(59, 130, 246, 1)",
          ],
          borderWidth: 1,
          borderRadius: 6,
          borderSkipped: false,
        },
      ],
    },
    options: {
      indexAxis: "y",
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        datalabels: {
          anchor: "end",
          align: "right",
          color: "#e2e8f0",
          font: { weight: "bold", size: 12 },
          formatter: (value) => (value > 0 ? value : ""),
        },
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            color: "#94a3b8",
            stepSize: 1,
            font: { size: 11 },
          },
          grid: { color: "rgba(51, 65, 85, 0.3)" },
        },
        y: {
          grid: { display: false },
          ticks: { color: "#94a3b8", font: { size: 12, weight: "500" } },
        },
      },
    },
  });
});
