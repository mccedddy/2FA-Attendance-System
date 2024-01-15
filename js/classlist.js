document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var deleteButton = document.getElementById("deleteStudentsBtn");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var fileInput = document.getElementById("fileInput");

  sortTable();

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

function deleteSelectedStudents() {
  // Get all checkboxes in the table
  var checkboxes = document.querySelectorAll(
    '#attendanceTable tbody input[type="checkbox"]:checked'
  );

  // Extract student numbers from checked checkboxes
  var studentNumbers = Array.from(checkboxes).map(function (checkbox) {
    return checkbox.closest("tr").querySelector("td:nth-child(4)").textContent; // Assuming student number is in the 4th column
  });

  console.log("Student numbers to be deleted: " + studentNumbers);

  // Send the list of student numbers to the server
  if (studentNumbers.length > 0) {
    // Use AJAX to send data to the PHP script
    $.ajax({
      url: "../includes/delete_students.php",
      method: "POST",
      data: { studentNumbers: studentNumbers },
      success: function (response) {
        // Handle success, e.g., refresh the table
        console.log(response);
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
  console.log("Clicked import!");
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

      console.log(dataArray);

      // Send dataArray to the server using a POST request
      $.ajax({
        url: "../includes/import_classlist.php",
        method: "POST",
        data: { dataArray: JSON.stringify(dataArray) },
        success: function (response) {
          console.log(response);
          console.log(response["status"]);
          console.log(response["message"]);

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
