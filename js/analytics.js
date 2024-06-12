document.addEventListener("DOMContentLoaded", function () {
  var sectionFilter = document.getElementById("sectionFilter");
  var subjectFilter = document.getElementById("subjectFilter");
  var startDate = document.getElementById("startDate");
  var endDate = document.getElementById("endDate");

  // Event listeners
  sectionFilter.addEventListener("change", function () {
    filter(
      sectionFilter.value,
      subjectFilter.value,
      startDate.value,
      endDate.value
    );
  });

  subjectFilter.addEventListener("change", function () {
    filter(
      sectionFilter.value,
      subjectFilter.value,
      startDate.value,
      endDate.value
    );
  });

  startDate.addEventListener("change", function () {
    filter(
      sectionFilter.value,
      subjectFilter.value,
      startDate.value,
      endDate.value
    );
  });

  endDate.addEventListener("change", function () {
    filter(
      sectionFilter.value,
      subjectFilter.value,
      startDate.value,
      endDate.value
    );
  });

  // Initialize
  filter(
    sectionFilter.value,
    subjectFilter.value,
    startDate.value,
    endDate.value
  );
});

function filter(section, subject, startDate, endDate) {
  console.log("Filter:", section, "-", subject, "-", startDate, "-", endDate);
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
      // Calculate total count
      let totalCount = data.length;

      // Calculate total verified count
      let totalVerifiedCount = 0;
      data.forEach((record) => {
        if (record.verified === "1") {
          totalVerifiedCount++;
        }
      });

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

      let latePercentage = (lateCount / totalVerifiedCount) * 100;
      if (lateCount == 0 && totalVerifiedCount == 0) {
        latePercentage = 0;
      }

      for (var i = 0; i < latePercentageElements.length; i++) {
        latePercentageElements[i].innerHTML = latePercentage.toFixed(2) + "%";
      }

      // Process the data to get attendance counts
      let attendanceCounts = {};
      data.forEach((record) => {
        let key = `${record.date}`; // Use only the date as the key
        if (!attendanceCounts[key]) {
          attendanceCounts[key] = { Present: 0, Late: 0, Absent: 0 };
        }
        if (record.verified === "1") {
          if (record.status === "Present") {
            attendanceCounts[key].Present++;
          } else if (record.status === "Late") {
            attendanceCounts[key].Late++;
          }
        }
      });

      // Prepare data for chart
      let labels = [];
      let presentData = [];
      let lateData = [];
      let absentData = [];

      for (let key in attendanceCounts) {
        labels.push(key);
        presentData.push(attendanceCounts[key].Present);
        lateData.push(attendanceCounts[key].Late);
        // Calculate absent count
        let total = attendanceCounts[key].Present + attendanceCounts[key].Late;
        let totalStudents = data.filter((record) => record.date === key).length;
        absentData.push(totalStudents - total);
      }

      // Sort the labels and corresponding data
      let sortedIndices = labels
        .map((label, index) => ({ label, index }))
        .sort((a, b) => new Date(a.label) - new Date(b.label))
        .map((item) => item.index);

      labels = sortedIndices.map((index) => labels[index]);
      presentData = sortedIndices.map((index) => presentData[index]);
      lateData = sortedIndices.map((index) => lateData[index]);
      absentData = sortedIndices.map((index) => absentData[index]);

      // Limit the number of displayed data points
      const maxPoints = 10;
      if (labels.length > maxPoints) {
        labels = labels.slice(-maxPoints);
        presentData = presentData.slice(-maxPoints);
        lateData = lateData.slice(-maxPoints);
        absentData = absentData.slice(-maxPoints);
      }

      // Update the chart
      updateAttendanceOverviewChart(labels, presentData, lateData, absentData);
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
      for (var i = 0; i < totalStudentsElements.length; i++) {
        totalStudentsElements[i].innerHTML = data;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

let attendanceOverviewChart;

function updateAttendanceOverviewChart(
  labels,
  presentData,
  lateData,
  absentData
) {
  var ctx = document.getElementById("attendanceOverviewTable").getContext("2d");

  if (attendanceOverviewChart) {
    attendanceOverviewChart.destroy();
  }

  attendanceOverviewChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Present",
          data: presentData,
          backgroundColor: "#810000",
        },
        {
          label: "Late",
          data: lateData,
          backgroundColor: "#FFE000",
        },
        {
          label: "Absent",
          data: absentData,
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
