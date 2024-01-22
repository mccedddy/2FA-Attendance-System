document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var editButton = document.getElementById("editStudentBtn");
  var deleteButton = document.getElementById("deleteStudentsBtn");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var fileInput = document.getElementById("fileInput");

  // Initial setup
  sortTable();

  // Event Listeners
  editButton.addEventListener("click", () => {
    editSelectedSubject();
  });

  deleteButton.addEventListener("click", () => {
    deleteSelectedSubjects();
  });

  importButton.addEventListener("click", () => {
    importSubjects();
  });

  exportButton.addEventListener("click", () => {
    exportSubjects();
  });

  fileInput.addEventListener("change", () => {
    updateFileName();
  });
});

function editSelectedSubject() {
  var checkbox = document.querySelector(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  var editSubjectModal = document.getElementById("editStudentModal");
  var editSubjectTitle = document.getElementById("editStudentTitle");
  var originalSubjectCode = document.getElementById("originalStudentNumber");

  if (checkbox) {
    // Get subject code
    var subjectCode = checkbox
      .closest("tr")
      .querySelector("td:nth-child(2)").textContent;

    editSubjectTitle.textContent = "EDIT SUBJECT " + subjectCode;
    var url = "../includes/fetch_edit_subject_data.php";

    // Setup edit modal
    editSubjectModal.style.display = "block";
    originalSubjectCode.value = subjectCode;

    // Fetch data for the selected student
    $.ajax({
      url: url,
      method: "POST",
      data: { subjectCode: subjectCode },
      success: function (response) {
        // Parse the response JSON
        var subjectData = JSON.parse(response);
        // console.log(response);

        // Set the default values for the textboxes
        document.getElementById("editSubjectCode").value =
          subjectData.subject_code;
        document.getElementById("editSubjectName").value =
          subjectData.subject_name;
      },
      error: function (error) {
        console.error("Error fetching student data:", error);
      },
    });
  } else {
    console.log("No checkbox selected.");
  }
}

function deleteSelectedSubjects() {
  // Get all checkboxes in the table
  var checkboxes = document.querySelectorAll(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  // Extract subject codes from checked checkboxes
  var subjectCodes = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.closest("tr").querySelector("td:nth-child(2)").textContent;
  });

  var url = "../includes/delete_subjects.php";

  // Send the list of subject codes to the server
  if (subjectCodes.length > 0) {
    $.ajax({
      url: url,
      method: "POST",
      data: { subjectCodes: subjectCodes },
      success: function (response) {
        location.reload();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  } else {
    // Inform the user that no subjects are selected
    alert("No subjects selected for deletion.");
  }
}

function importSubjects() {
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

      if (section != "PROFESSORS") {
        var url = "../includes/import_classlist.php";
      } else {
        var url = "../includes/import_professors.php";
      }

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
  }
}

function exportSubjects() {
  var table = document.getElementById("attendanceTable");

  table.setAttribute("data-cols-width", "15,40");

  var fileName = "Subjects.xlsx";
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

function sortTable() {
  const table = document.getElementById("attendanceTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  // Sort rows based on the content of the second column
  rows.sort(compareSecondColumn);

  // Clear and append sorted rows to the tbody
  tbody.innerHTML = "";
  rows.forEach((row) => {
    tbody.appendChild(row);
  });
}

function compareSecondColumn(row1, row2) {
  const cell1 = row1.querySelector("td:nth-child(2)");
  const cell2 = row2.querySelector("td:nth-child(2)");

  // Use localeCompare for alphabetical sorting
  return cell1.textContent.localeCompare(cell2.textContent);
}
