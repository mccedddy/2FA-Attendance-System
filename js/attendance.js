document.addEventListener("DOMContentLoaded", function () { 
  const table = document.getElementById("attendanceTable");
  const tbody = table.querySelector("tbody");
  const dateFilter = document.getElementById("date");
  const subjectFilter = document.getElementById("subjectFilter");

  fetchAttendance(dateFilter.value, subjectFilter.value);
  sortTable();

  dateFilter.addEventListener("change", () => {fetchAttendance(dateFilter.value, subjectFilter.value);});
  subjectFilter.addEventListener("change", () => {fetchAttendance(dateFilter.value, subjectFilter.value);});
});

function fetchAttendance(date, subject) {
  var url = "../includes/fetch_attendance.php";
  var formData = new FormData();
  formData.append("date", date);
  formData.append("subject", subject);
  console.log(date + " - " + subject);

  fetch(url, {
    method: "POST",
    body: formData,
  })
  .then((response) => response.json())
  .then((data) => {
    console.log(data);
    displayAttendanceData(data);
    // updateOriginalData(data);
  })
  .catch((error) => {
    console.error("Error:", error);
  });
}

function displayAttendanceData(data) {
    const table = document.getElementById("attendanceTable");
    const tbody = table.querySelector("tbody");
    tbody.innerHTML = "";

    data.forEach((rowData) => {
      let row = document.createElement("tr");
      // Create and append cells in the desired order
      let cells = [
        "student_name",
        "student_number",
        "status",
        "time",
        "date",
        "room",
        "subject_name",
        "professor_name",
        // "schedule_id"
      ].map((key) => {
        const cell = document.createElement("td");
        cell.innerText = rowData[key];
        return cell;
      });
      cells.forEach((cell) => {
        row.appendChild(cell);
      });
      tbody.appendChild(row);
    });
}


function sortTable() {
  const table = document.getElementById("attendanceTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.sort(compareDateTime);
  tbody.innerHTML = "";

  rows.forEach((row) => {
    tbody.appendChild(row);
  });
}

function compareDateTime(a, b) {
  const dateComparison =
    new Date(b.cells[4].innerText + " " + b.cells[3].innerText) -
    new Date(a.cells[4].innerText + " " + a.cells[3].innerText);
  return dateComparison;
}