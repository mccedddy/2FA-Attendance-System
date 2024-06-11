document.addEventListener("DOMContentLoaded", function () {
  var ctx = document.getElementById("attendanceOverviewTable").getContext("2d");
  var labels = ["A", "B", "C", "D", "E", "F", "G", "H", "I"];
  var dataset = [
    [35, 33, 34, 35, 33, 25, 33, 34, 32],
    [5, 6, 4, 2, 5, 10, 2, 6, 4],
    [0, 1, 2, 3, 2, 5, 5, 0, 2],
  ];
  var attendanceOverview = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Present",
          data: dataset[0],
          backgroundColor: "#810000",
        },
        {
          label: "Late",
          data: dataset[1],
          backgroundColor: "#FFE000",
        },
        {
          label: "Absent",
          data: dataset[2],
          backgroundColor: "#DBA61A",
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
        x: {
          beginAtZero: true,
        },
      },
      barThickness: 8,
      categoryPercentage: 0.5,
      barPercentage: 0.8,
    },
  });
});
