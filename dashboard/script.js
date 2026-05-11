/* ── Sidebar toggle (mobile) ── */
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

/* ── Color palette ── */
const PRIMARY = "#96CA50";
const PRIMARY_DARK = "#78a83c";
const SECONDARY = "#3D4658";
const PALETTE = ["#96CA50", "#3D4658", "#F59E0B", "#3B82F6", "#EC4899"];

/* ── Produksi Chart ── */
const data7 = [1080, 1150, 1090, 1240, 1300, 1210, 1240];
const data30 = [
  980, 1020, 1100, 1050, 1090, 1130, 1080, 1150, 1090, 1240, 1300, 1210, 1240,
  1190, 1280, 1320, 1200, 1170, 1250, 1300, 1280, 1310, 1190, 1220, 1260, 1300,
  1280, 1240, 1310, 1240,
];
const labels7 = ["Sel", "Rab", "Kam", "Jum", "Sab", "Min", "Sen"].map(
  (d, i) => {
    const dt = new Date("2026-03-09");
    dt.setDate(dt.getDate() - (6 - i));
    return dt.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
    });
  },
);
const labels30 = Array.from(
  {
    length: 30,
  },
  (_, i) => {
    const dt = new Date("2026-03-09");
    dt.setDate(dt.getDate() - (29 - i));
    return dt.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
    });
  },
);

const prodCtx = document.getElementById("produksiChart").getContext("2d");
const gradient = prodCtx.createLinearGradient(0, 0, 0, 220);
gradient.addColorStop(0, "rgba(150,202,80,.25)");
gradient.addColorStop(1, "rgba(150,202,80,0)");

const prodChart = new Chart(prodCtx, {
  type: "line",
  data: {
    labels: labels7,
    datasets: [
      {
        label: "Produksi (L)",
        data: data7,
        borderColor: PRIMARY,
        backgroundColor: gradient,
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: PRIMARY,
        pointRadius: 4,
        pointHoverRadius: 6,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: (ctx) => ` ${ctx.parsed.y.toLocaleString("id-ID")} L`,
        },
      },
    },
    scales: {
      x: {
        grid: {
          display: false,
        },
        ticks: {
          font: {
            family: "Poppins",
            size: 11,
          },
          color: "#7c8493",
        },
      },
      y: {
        grid: {
          color: "#f0f2f5",
        },
        ticks: {
          font: {
            family: "Poppins",
            size: 11,
          },
          color: "#7c8493",
          callback: (v) => v.toLocaleString("id-ID") + " L",
        },
      },
    },
  },
});

function switchPeriod(btn, period) {
  document
    .querySelectorAll(".period-tab")
    .forEach((t) => t.classList.remove("active"));
  btn.classList.add("active");
  if (period === "7") {
    prodChart.data.labels = labels7;
    prodChart.data.datasets[0].data = data7;
  } else {
    prodChart.data.labels = labels30;
    prodChart.data.datasets[0].data = data30;
  }
  prodChart.update();
}

/* ── Ternak Composition Chart ── */
const ternakData = {
  labels: ["Sapi Perah", "Sapi Potong", "Kambing PE", "Domba Merino"],
  values: [112, 68, 45, 23],
};

const ternakCtx = document.getElementById("ternakChart").getContext("2d");
new Chart(ternakCtx, {
  type: "doughnut",
  data: {
    labels: ternakData.labels,
    datasets: [
      {
        data: ternakData.values,
        backgroundColor: PALETTE,
        borderWidth: 2,
        borderColor: "#fff",
        hoverOffset: 6,
      },
    ],
  },
  options: {
    responsive: true,
    cutout: "68%",
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: (ctx) => ` ${ctx.label}: ${ctx.parsed} ekor`,
        },
      },
    },
  },
});

/* Custom legend */
const total = ternakData.values.reduce((a, b) => a + b, 0);
document.getElementById("ternakLegend").innerHTML = ternakData.labels
  .map(
    (l, i) => `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f2f5">
          <div style="display:flex;align-items:center;gap:8px">
            <span style="width:10px;height:10px;border-radius:50%;background:${PALETTE[i]};display:inline-block;flex-shrink:0"></span>
            <span style="font-size:12px;color:#3D4658">${l}</span>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:12px;font-weight:600;color:#000005">${ternakData.values[i]}</span>
            <span style="font-size:11px;color:#7c8493">${Math.round((ternakData.values[i] / total) * 100)}%</span>
          </div>
        </div>`,
  )
  .join("");
