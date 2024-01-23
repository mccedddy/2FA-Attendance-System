document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var editButton = document.getElementById("editStudentBtn");
  var deleteButton = document.getElementById("deleteStudentsBtn");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var fileInput = document.getElementById("fileInput");

  // Initial setup
  // sortTable();

  // Event Listeners
  editButton.addEventListener("click", () => {
    editSelectedSchedule();
  });

  deleteButton.addEventListener("click", () => {
    deleteSelectedSchedule();
  });

  importButton.addEventListener("click", () => {
    importSchedule();
  });

  exportButton.addEventListener("click", () => {
    exportSchedule();
  });

  fileInput.addEventListener("change", () => {
    updateFileName();
  });
});

function editSelectedSchedule() {
  var checkbox = document.querySelector(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  var scheduleId = document.getElementById("scheduleId");
  var editScheduleModal = document.getElementById("editStudentModal");

  if (checkbox) {
    // Get subject code
    var schedule = checkbox
      .closest("tr")
      .querySelector("td:nth-child(8)").textContent;

    var url = "../includes/fetch_edit_schedule_data.php";
    console.log(scheduleId.value);
    // Setup edit modal
    editScheduleModal.style.display = "block";
    scheduleId.value = schedule;

    console.log(scheduleId);
    console.log(scheduleId.value);

    // Fetch data for the selected student
    $.ajax({
      url: url,
      method: "POST",
      data: { schedule: schedule },
      success: function (response) {
        // Parse the response JSON
        var scheduleData = JSON.parse(response);
        console.log(response);

        // Set the default values for the textboxes
        document.getElementById("editSubject").value =
          scheduleData.subject_code;
        document.getElementById("editDay").value = scheduleData.day;
        document.getElementById("editStartTime").value =
          scheduleData.start_time;
        document.getElementById("editEndTime").value = scheduleData.end_time;
        document.getElementById("editProfessor").value = scheduleData.professor;
      },
      error: function (error) {
        console.error("Error fetching student data:", error);
      },
    });
  } else {
    console.log("No checkbox selected.");
  }
}

function deleteSelectedSchedule() {
  console.log("clicked delete!");
  // Get all checkboxes in the table
  var checkboxes = document.querySelectorAll(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  // Extract student numbers from checked checkboxes
  var schedules = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.closest("tr").querySelector("td:nth-child(8)").textContent;
  });

  console.log(schedules);

  var url = "../includes/delete_schedule.php";

  // Send the list of student numbers to the server
  if (schedules.length > 0) {
    $.ajax({
      url: url,
      method: "POST",
      data: { schedules: schedules },
      success: function (response) {
        location.reload();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  } else {
    // Inform the user that no students are selected
    alert("No schedules selected for deletion.");
  }
}

function importSchedule() {
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
      var dataArray = XLSX.utils.sheet_to_json(sheet, {
        header: 1,
        range: 1,
      });

      var url = "../includes/import_schedule.php";

      // Send dataArray to the server using a POST request
      $.ajax({
        url: url,
        method: "POST",
        data: { dataArray: JSON.stringify(dataArray) },
        success: function (response) {
          console.log(response);
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
  }
}

function exportSchedule() {
  var section = document.getElementById("title").textContent;
  var table = document.getElementById("attendanceTable");

  table.setAttribute("data-cols-width", "15,40,15,15,15,25,15");

  var fileName = section + ".xlsx";
  TableToExcel.convert(document.getElementById("attendanceTable"), {
    name: fileName,
    sheet: {
      name: "Sheet 1",
    },
  });
}

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
