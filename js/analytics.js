document.addEventListener("DOMContentLoaded", function () {

  const xValues = ["4-1", "4-2", "4-3", "4-4", "4-5", "4-6"];

  const present = [73, 50, 60, 75, 83, 81, 72, 100];
  const late =    [20, 40, 30, 18, 10, 10, 20, 100];
  const absent =  [7, 10, 10, 7, 7, 9, 8, 100];

  const presentColors = ['#810000', '#810000', '#810000', '#810000', '#810000', '#810000'];
  const lateColors = ['#FFE000', '#FFE000', '#FFE000', '#FFE000', '#FFE000', '#FFE000'];
  const absentColors = ['#DBA61A', '#DBA61A', '#DBA61A', '#DBA61A', '#DBA61A', '#DBA61A'];

  const attendanceOverview = new Chart("attendanceOverview", {
    type: "bar",
    data: {
      labels: xValues,
      datasets: [
        {
          label: 'Present',
          backgroundColor: presentColors,
          data: present,
          barThickness: 10,
        },
        {
          label: 'Late',
          backgroundColor: lateColors,
          data: late,
          barThickness: 10,
        },
        {
          label: 'Absent',
          backgroundColor: absentColors,
          data: absent,
          barThickness: 10,
        },
      ]
    },
    options: {
      maintainAspectRatio: true,
      responsive: true,
      layout: {
        autoPadding: true
      },
    }
  });

  var yValues = [90, 70, 87, 66, 88, 94, 0, 100]

  const attendanceByClass = new Chart("attendanceByClass", {
    type: "horizontalBar",
    data: {
      labels: xValues,
      datasets: [{
        label: 'Attendance Percentage',
        backgroundColor: presentColors,
        data: yValues,
        barThickness: 10,
      }]
    },
    options: {
      maintainAspectRatio: true,
      responsive: true,
    }
  });
});