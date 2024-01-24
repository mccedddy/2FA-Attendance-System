document.addEventListener("DOMContentLoaded", function () {

  const xValues = ["4-1", "4-2", "4-3", "4-4", "4-5", "4-6"];
  const yValues = [50, 60, 65, 80, 85, 90, 100, 0];
  const barColors = ["#810000", "#810000", "#810000", "#810000", "#810000", "#810000"];

  const attendanceOverview = new Chart("attendanceOverview", {
    type: "bar",
    data: {
      labels: xValues,
      datasets: [{
        backgroundColor: barColors,
        data: yValues
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      layout: {
        autoPadding: true
      }
    }
  });

  const attendanceByClass = new Chart("attendanceByClass", {
    type: "horizontalBar",
    data: {
      labels: xValues,
      datasets: [{
        backgroundColor: barColors,
        data: yValues
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      layout: {
        padding: 10
      }
    }
  });
});