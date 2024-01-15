document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var fileInput = document.getElementById("fileInput");

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

          window.location.href = "../pages/admin_section_page.php";
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
