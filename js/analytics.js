document.addEventListener("DOMContentLoaded", function () {
  var sectionFilter = document.getElementById("sectionFilter");
  var subjectFilter = document.getElementById("subjectFilter");

  // Event listeners
  sectionFilter.addEventListener("change", function () {
    filter(sectionFilter.value, subjectFilter.value);
  });

  subjectFilter.addEventListener("change", function () {
    filter(sectionFilter.value, subjectFilter.value);
  });

  // Initialize
  loadAttendanceOverview();
  filter(sectionFilter.value, subjectFilter.value);
});

function filter(section, subject, startDate, endDate) {
  console.log("Filter:", section, "-", subject);
  fetchStudentCount(section, subject);
  fetchAttendance(section, subject, startDate, endDate);
}

function fetchAttendance(section, subject, startDate, endDate) {
  var latePercentageElements =
    document.getElementsByClassName("latePercentage");
  var attendancePercentageElements = document.getElementsByClassName(
    "attendancePercentage"
  );
  var formData = new FormData();
  formData.append("section", section);
  formData.append("subject", subject);
  formData.append("startDate", startDate);
  formData.append("endDate", endDate);
  formData.append("fetchAttendance", true);

  var url = "../includes/database_operations.php";
  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);

      // Calculate total count
      let totalCount = data.length;
      console.log("totalCount", totalCount);

      // Calculate total verified count
      let totalVerifiedCount = 0;
      data.forEach((record) => {
        if (record.verified === "1") {
          totalVerifiedCount++;
        }
      });
      console.log("totalVerifiedCount", totalVerifiedCount);

      // Calculate attendance count [(present+late)/total]
      let attendanceCount = 0;
      data.forEach((record) => {
        if (
          (record.status === "Present" || record.status === "Late") &&
          record.verified === "1"
        ) {
          attendanceCount++;
        }
      });
      console.log("attendanceCount", attendanceCount);

      let attendancePercentage = (attendanceCount / totalCount) * 100;
      if (attendanceCount == 0 && totalCount == 0) {
        attendancePercentage = 0;
      }

      for (var i = 0; i < attendancePercentageElements.length; i++) {
        attendancePercentageElements[i].innerHTML =
          attendancePercentage.toFixed(2) + "%";
      }

      // Calculate late count [late/verified(present+late)]
      let lateCount = 0;
      data.forEach((record) => {
        if (record.status === "Late" && record.verified === "1") {
          lateCount++;
        }
      });
      console.log("lateCount", lateCount);

      let latePercentage = (lateCount / totalVerifiedCount) * 100;
      if (lateCount == 0 && totalVerifiedCount == 0) {
        latePercentage = 0;
      }

      for (var i = 0; i < latePercentageElements.length; i++) {
        latePercentageElements[i].innerHTML = latePercentage.toFixed(2) + "%";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function fetchStudentCount(section, subject) {
  var totalStudentsElements = document.getElementsByClassName("totalStudents");

  var formData = new FormData();
  formData.append("section", section);
  formData.append("subject", subject);
  formData.append("fetchStudentCount", true);

  var url = "../includes/database_operations.php";
  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Student Count:", data);
      for (var i = 0; i < totalStudentsElements.length; i++) {
        totalStudentsElements[i].innerHTML = data;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function loadAttendanceOverview() {
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
}
