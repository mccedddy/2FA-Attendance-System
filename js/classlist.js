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
    editSelectedStudent();
  });

  deleteButton.addEventListener("click", () => {
    deleteSelectedStudents();
  });

  importButton.addEventListener("click", () => {
    importClasslist();
  });

  exportButton.addEventListener("click", () => {
    exportClasslist();
  });

  fileInput.addEventListener("change", () => {
    updateFileName();
  });
});

function editSelectedStudent() {
  var checkbox = document.querySelector(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  var editStudentModal = document.getElementById("editStudentModal");
  var editStudentTitle = document.getElementById("editStudentTitle");
  var originalStudentNumber = document.getElementById("originalStudentNumber");

  if (checkbox) {
    // Get student number
    var studentNumber = checkbox
      .closest("tr")
      .querySelector("td:nth-child(4)").textContent;

    // Setup edit modal
    editStudentTitle.textContent = "EDIT STUDENT " + studentNumber;
    editStudentModal.style.display = "block";
    originalStudentNumber.value = studentNumber;

    // Fetch data for the selected student
    $.ajax({
      url: "../includes/fetch_edit_student_data.php",
      method: "POST",
      data: { studentNumber: studentNumber },
      success: function (response) {
        // Parse the response JSON
        var studentData = JSON.parse(response);

        // Set the default values for the textboxes
        document.getElementById("editLastName").value = studentData.last_name;
        document.getElementById("editFirstName").value = studentData.first_name;
        document.getElementById("editStudentNumber").value =
          studentData.student_number;
        document.getElementById("editNfcUid").value = studentData.nfc_uid;
        document.getElementById("editEmail").value = studentData.email;
      },
      error: function (error) {
        console.error("Error fetching student data:", error);
      },
    });
  } else {
    console.log("No checkbox selected.");
  }
}

function deleteSelectedStudents() {
  // Get all checkboxes in the table
  var checkboxes = document.querySelectorAll(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  // Extract student numbers from checked checkboxes
  var studentNumbers = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.closest("tr").querySelector("td:nth-child(4)").textContent;
  });

  // Send the list of student numbers to the server
  if (studentNumbers.length > 0) {
    $.ajax({
      url: "../includes/delete_students.php",
      method: "POST",
      data: { studentNumbers: studentNumbers },
      success: function (response) {
        location.reload();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  } else {
    // Inform the user that no students are selected
    alert("No students selected for deletion.");
  }
}

function importClasslist() {
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

      // Send dataArray to the server using a POST request
      $.ajax({
        url: "../includes/import_classlist.php",
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

function exportClasslist() {
  console.log("Clicked export!");
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
