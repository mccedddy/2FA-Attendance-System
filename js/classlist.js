document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("classlistTable");
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
  var title = document.getElementById("title").textContent;
  var checkbox = document.querySelector(
    '#classlistTable tbody input[type="checkbox"]:checked'
  );

  var editModal = document.getElementById("editModal");
  var editStudentTitle = document.getElementById("editStudentTitle");
  var originalIdNumber = document.getElementById("originalIdNumber");

  if (checkbox) {
    // Get student number
    var idNumber = checkbox
      .closest("tr")
      .querySelector("td:nth-child(4)").textContent;

    if (title != "PROFESSORS") {
      editStudentTitle.textContent = "EDIT STUDENT " + idNumber;
      var url = "../includes/fetch_edit_student_data.php";
    } else {
      editStudentTitle.textContent = "EDIT PROFESSOR " + idNumber;
      var url = "../includes/fetch_edit_professor_data.php";
    }

    // Setup edit modal
    editModal.style.display = "block";
    originalIdNumber.value = idNumber;

    // Fetch data for the selected student
    $.ajax({
      url: url,
      method: "POST",
      data: { idNumber: idNumber },
      success: function (response) {
        // Parse the response JSON
        var studentData = JSON.parse(response);

        // Set the default values for the textboxes
        document.getElementById("editLastName").value = studentData.last_name;
        document.getElementById("editFirstName").value = studentData.first_name;

        if (title != "PROFESSORS") {
          document.getElementById("editIdNumber").value = studentData.id_number;
          document.getElementById("editNfcUid").value = studentData.nfc_uid;
        } else {
          document.getElementById("editIdNumber").value = studentData.id_number;
        }

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
  var title = document.getElementById("title").textContent;
  // Get all checkboxes in the table
  var checkboxes = document.querySelectorAll(
    '#classlistTable tbody input[type="checkbox"]:checked'
  );

  // Extract student numbers from checked checkboxes
  var idNumbers = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.closest("tr").querySelector("td:nth-child(4)").textContent;
  });

  if (title != "PROFESSORS") {
    var url = "../includes/delete_students.php";
  } else {
    var url = "../includes/delete_professors.php";
  }

  // Send the list of student numbers to the server
  if (idNumbers.length > 0) {
    $.ajax({
      url: url,
      method: "POST",
      data: { idNumbers: idNumbers },
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

function exportClasslist() {
  var section = document.getElementById("title").textContent;
  var table = document.getElementById("classlistTable");

  if (section != "PROFESSORS") {
    table.setAttribute("data-cols-width", "15,20,20,10,15,35");
  } else {
    table.setAttribute("data-cols-width", "15,20,20,35");
  }

  var fileName = section + " Classlist.xlsx";
  TableToExcel.convert(document.getElementById("classlistTable"), {
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
  const table = document.getElementById("classlistTable");
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
