document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var dateFilter = document.getElementById("date");
  var subjectFilter = document.getElementById("subject");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var addAttendanceButton = document.getElementById("addButton");
  var deleteAttendanceButton = document.getElementById("deleteAttendance");
  var fileInput = document.getElementById("fileInput");

  fetchAttendance(dateFilter.value, subjectFilter.value);

  dateFilter.addEventListener("change", () => {
    fetchAttendance(dateFilter.value, subjectFilter.value);
  });
  subjectFilter.addEventListener("change", () => {
    fetchAttendance(dateFilter.value, subjectFilter.value);
  });
  importButton.addEventListener("click", () => {
    importAttendance();
  });
  exportButton.addEventListener("click", () => {
    exportAttendance();
  });
  addAttendanceButton.addEventListener("click", (event) => {
    addAttendance(event);
  });
  deleteAttendanceButton.addEventListener("click", () => {
    deleteAttendance();
  });
  fileInput.addEventListener("change", () => {
    updateFileName();
  });
});

function updateFileName() {
  var fileInput = document.getElementById("fileInput");
  var fileNameSpan = document.getElementById("fileName");
  var fileInputLabel = document.getElementById("fileInputLabel");

  if (fileInput.files.length > 0) {
    fileNameSpan.textContent = fileInput.files[0].name;
  } else {
    fileNameSpan.textContent = "No file chosen";
  }
}

function fetchAttendance(date, subject) {
  var url = "../includes/fetch_attendance.php";
  var formData = new FormData();
  formData.append("date", date);
  formData.append("subject", subject);

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      displayAttendanceData(data);
      fetchAbsent(date, subject);
      fetchUnverified(date, subject);
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

    // Create a cell with a checkbox
    let checkboxCell = document.createElement("td");
    checkboxCell.setAttribute("data-exclude", "true");
    checkboxCell.innerHTML =
      '<input type="checkbox" name="selectedStudents[]">';
    row.appendChild(checkboxCell);

    // Create and append cells in the desired order
    let cells = [
      "student_name",
      "id_number",
      "status",
      "time",
      "date",
      "room",
      "subject_name",
      "professor_name",
      "id",
    ].map((key) => {
      const cell = document.createElement("td");
      cell.innerText = rowData[key];
      if (key === "id") {
        cell.classList.add("hidden-cell");
      }
      return cell;
    });
    cells.forEach((cell) => {
      row.appendChild(cell);
    });
    tbody.appendChild(row);
  });
}

function importAttendance() {
  var section = document.getElementById("title").textContent;
  var fileInput = document.getElementById("fileInput");
  var file = fileInput.files[0];

  if (file) {
    var reader = new FileReader();

    reader.onload = function (e) {
      var fileContent = e.target.result;

      // Use xlsx library to read the file content
      var workbook = XLSX.read(fileContent, { type: "binary" });
      var sheetName = workbook.SheetNames[0];
      var sheet = workbook.Sheets[sheetName];

      // Convert sheet data to an array of objects starting from the 2nd row
      var dataArray = XLSX.utils.sheet_to_json(sheet, { header: 1, range: 1 });

      var url = "../includes/import_attendance.php";
      console.log(dataArray);

      // Send dataArray to the server using a POST request
      $.ajax({
        url: url,
        method: "POST",
        data: { dataArray: JSON.stringify(dataArray) },
        success: function (response) {
          location.reload();
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    };

    reader.readAsBinaryString(file);
  } else {
    console.error("No file selected.");
    showToastr("error", "No file selected");
  }
}

function exportAttendance() {
  console.log("clicked export");
  var section = document.getElementById("title").textContent;
  var table = document.getElementById("attendanceTable");
  var date = document.getElementById("date");

  table.setAttribute("data-cols-width", "30,20,10,10,15,10,40,30");

  var fileName = section + " - " + date.value + ".xlsx";
  TableToExcel.convert(document.getElementById("attendanceTable"), {
    name: fileName,
    sheet: {
      name: "Sheet 1",
    },
  });
}

function addAttendance(event) {
  event.preventDefault();
  var student = document.getElementById("attendanceStudent").value;
  var status = document.getElementById("attendanceStatus").value;
  var time = document.getElementById("attendanceTime").value;
  var date = document.getElementById("attendanceDate").value;
  var room = document.getElementById("attendanceRoom").value;
  var subject = document.getElementById("attendanceSubject").value;
  var professor = document.getElementById("attendanceProfessor").value;
  var section = document.getElementById("attendanceSection").value;

  console.log([student, status, time, date, room, subject, professor, section]);

  var formData = new FormData();
  formData.append("student", student);
  formData.append("status", status);
  formData.append("time", time);
  formData.append("date", date);
  formData.append("room", room);
  formData.append("subject", subject);
  formData.append("professor", professor);
  formData.append("section", section);

  console.log(formData);

  var url = "../includes/database_operations.php";
  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      location.reload();
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function fetchAbsent(date, subject) {
  var url = "../includes/fetch_absent.php";
  var formData = new FormData();
  formData.append("date", date);
  formData.append("subject", subject);

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      displayAbsentData(data);
      // updateOriginalData(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function displayAbsentData(data) {
  const table = document.getElementById("absentTable");
  const tbody = table.querySelector("tbody");
  tbody.innerHTML = "";

  data.forEach((rowData) => {
    let row = document.createElement("tr");

    // Create a cell with a checkbox
    let checkboxCell = document.createElement("td");
    checkboxCell.setAttribute("data-exclude", "true");
    checkboxCell.innerHTML =
      '<input type="checkbox" name="selectedStudents[]">';
    row.appendChild(checkboxCell);

    // Create and append cells in the desired order
    let cells = [
      "student_name",
      "id_number",
      "status",
      "date",
      "subject_name",
      "professor_name",
      "id",
    ].map((key) => {
      const cell = document.createElement("td");
      cell.innerText = rowData[key];
      if (key === "id") {
        cell.classList.add("hidden-cell");
      }
      return cell;
    });
    cells.forEach((cell) => {
      row.appendChild(cell);
    });
    tbody.appendChild(row);
  });
}

function fetchUnverified(date, subject) {
  var url = "../includes/fetch_unverified.php";
  var formData = new FormData();
  formData.append("date", date);
  formData.append("subject", subject);

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      displayUnverifiedData(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function displayUnverifiedData(data) {
  const table = document.getElementById("unverifiedTable");
  const tbody = table.querySelector("tbody");
  tbody.innerHTML = "";

  data.forEach((rowData) => {
    let row = document.createElement("tr");

    // Create a cell with a checkbox
    let checkboxCell = document.createElement("td");
    checkboxCell.setAttribute("data-exclude", "true");
    checkboxCell.innerHTML =
      '<input type="checkbox" name="selectedStudents[]">';
    row.appendChild(checkboxCell);

    // Create and append cells in the desired order
    let cells = [
      "student_name",
      "id_number",
      "status",
      "time",
      "date",
      "room",
      "subject_name",
      "professor_name",
      "id",
    ].map((key) => {
      const cell = document.createElement("td");
      cell.innerText = rowData[key];
      if (key === "id") {
        cell.classList.add("hidden-cell");
      }
      return cell;
    });
    cells.forEach((cell) => {
      row.appendChild(cell);
    });
    tbody.appendChild(row);
  });
}

function deleteAttendance() {
  // Helper function to extract student numbers from a table
  function extractIds(tableId, columnIndex) {
    var checkboxes = document.querySelectorAll(
      `#${tableId} tbody input[type="checkbox"]:checked`
    );
    return Array.from(checkboxes).map(function (checkbox) {
      return checkbox
        .closest("tr")
        .querySelector(`td:nth-child(${columnIndex})`).textContent;
    });
  }

  var presentIds = extractIds("attendanceTable", 10);
  var absentIds = extractIds("absentTable", 8);
  var unverifiedIds = extractIds("unverifiedTable", 10);

  var attendanceIds = presentIds.concat(absentIds, unverifiedIds);

  console.log("ID Numbers to delete:", attendanceIds);

  var url = "../includes/delete_attendance.php";

  // Send the list of student numbers to the server
  if (attendanceIds.length > 0) {
    $.ajax({
      url: url,
      method: "POST",
      data: { attendanceIds: attendanceIds },
      success: function (response) {
        console.log(response);
        location.reload();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  } else {
    // Inform the user that no students are selected
    showToastr("info", "No students selected for deletion.");
  }
}
