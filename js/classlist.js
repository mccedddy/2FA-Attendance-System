document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("classlistTable");
  var tbody = table.querySelector("tbody");
  var editButton = document.getElementById("editStudentBtn");
  var registerButton = document.getElementById("registerStudentBtn");
  var deleteButton = document.getElementById("deleteStudentsBtn");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");
  var fileInput = document.getElementById("fileInput");
  var video = document.getElementById("camera-stream");
  var captureButton = document.getElementById("capture");
  var processImagesButton = document.getElementById("processImages");
  var clearDataButton = document.getElementById("clearData");
  var target;

  // Initial setup
  sortTable();

  // Request access to the camera
  navigator.mediaDevices
    .getUserMedia({ video: true, audio: false })
    .then((stream) => {
      // Set the video source to the camera stream
      video.srcObject = stream;
      video.play();
    })
    .catch((error) => {
      console.error("Error accessing camera:", error);
    });

  // Event Listeners
  tbody.addEventListener("click", function (event) {
    target = event.target;
    if (
      target &&
      target.tagName === "BUTTON" &&
      target.id === "registerStudentBtn"
    ) {
      registerStudent(target);
    }
  });

  captureButton.addEventListener("click", (event) => {
    captureImage(event, video, target);
  });

  processImagesButton.addEventListener("click", (event) => {
    processImages(event);
  });

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

  clearDataButton.addEventListener("click", () => {
    clearDataFeatures();
  });
});

function uploadFeatures(event) {
  event.preventDefault();
  var url = "../includes/upload_features.php";

  fetch(url, {
    method: "POST",
  })
    .then((response) => response.text())
    .then((data) => {
      console.log("Response from server:", data);
      return JSON.parse(data);
    })
    .then((parsedData) => {
      console.log("DONE:", parsedData);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function processImages(event) {
  event.preventDefault();
  var idNumber = document.getElementById("idNumber").innerHTML;
  var url = "../includes/trigger_generate_features.php";
  var formData = new FormData();
  formData.append("idNumber", idNumber);

  console.log("Processing images...");
  showToastr("info", "Processing images.", "Please wait...");

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      console.log("Response from server:", data);
      return JSON.parse(data);
    })
    .then((parsedData) => {
      console.log("DONE:", parsedData);
      console.log("Generated Features:", parsedData);

      var uploadUrl = "../includes/upload_features.php";

      fetch(uploadUrl, {
        method: "POST",
      })
        .then((response) => response.text())
        .then((data) => {
          return JSON.parse(data);
        })
        .then((parsedData) => {

          //delete in captures
          fetch('../includes/delete_captures.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `idNumber=${encodeURIComponent(idNumber)}`
          })
          .then(response => {
            if (!response.ok) {
              return response.text().then(text => { throw new Error(text); });
            }
            return response.json();
          })
          .then(data => {
            if (data.status === 'success') {
              console.log(data.message); // Log the success message
              showToastr("success", "HATDOG", "Reloading page...");
            } else {
              console.error('Failed to delete folder or line: ' + data.message);
            }
          })
          .catch(error => {
            console.error('An error occurred: ' + error.message);
          });
          
          console.log("Uploaded Features:", parsedData);
          showToastr("success", "Uploaded features!", "Reloading page...");
          setTimeout(() => {
            location.reload();
          }, 3000);
          // location.reload();
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function registerStudent(button) {
  var registerTitle = document.getElementById("registerStudentTitle");
  var lastNameElem = document.getElementById("lastName");
  var firstNameElem = document.getElementById("firstName");
  var idNumberElem = document.getElementById("idNumber");
  var registerModal = document.getElementById("registerModal");
  var row = button.closest("tr");
  var idNumberCell = row.cells[3];
  var idNumber = idNumberCell.textContent.trim();
  var firstNameCell = row.cells[2];
  var firstName = firstNameCell.textContent.trim();
  var lastNameCell = row.cells[1];
  var lastName = lastNameCell.textContent.trim();

  registerTitle.innerHTML = "REGISTER STUDENT " + idNumber;
  lastNameElem.innerHTML = lastName;
  firstNameElem.innerHTML = firstName;
  idNumberElem.innerHTML = idNumber;
  registerModal.style.display = "block";
}

function captureImage(event, video, button) {
  event.preventDefault();
  var row = button.closest("tr");
  var idNumberCell = row.cells[3];
  var idNumber = idNumberCell.textContent.trim();
  const canvas = document.getElementById("canvas");
  const ctx = canvas.getContext("2d");

  // Adjust canvas size to be 248x248
  const canvasWidth = 248;
  const canvasHeight = 248;
  canvas.width = canvasWidth;
  canvas.height = canvasHeight;

  // Get the dimensions of the video
  const videoWidth = video.videoWidth;
  const videoHeight = video.videoHeight;

  // Calculate cropping dimensions
  const aspectRatio = videoWidth / videoHeight;
  let cropWidth, cropHeight;
  if (aspectRatio > 1) {
    // Video is wider than it is tall
    cropHeight = videoHeight;
    cropWidth = videoHeight; // Crop width to match height for 1:1 aspect ratio
  } else {
    // Video is taller than it is wide
    cropWidth = videoWidth;
    cropHeight = videoWidth; // Crop height to match width for 1:1 aspect ratio
  }

  const cropX = (videoWidth - cropWidth) / 2;
  const cropY = (videoHeight - cropHeight) / 2;

  // Draw the cropped image onto the canvas
  ctx.drawImage(
    video,
    cropX,
    cropY,
    cropWidth,
    cropHeight,
    0,
    0,
    canvasWidth,
    canvasHeight
  );
  // Get the image count from the server
  let xhrCount = new XMLHttpRequest();
  xhrCount.open("POST", "../includes/upload.php", true);
  xhrCount.setRequestHeader(
    "Content-Type",
    "application/x-www-form-urlencoded"
  );
  xhrCount.onload = function () {
    if (xhrCount.status === 200) {
      let response = JSON.parse(xhrCount.responseText);
      if (response.status === "success") {
        let imgFaceIndex = response.imgCount;
        let filename = "img_face_" + imgFaceIndex + ".png";

        // Convert the canvas content to a data URL
        let imageDataURL = canvas.toDataURL("image/png");

        // Send the image data to the server
        let xhrUpload = new XMLHttpRequest();
        xhrUpload.open("POST", "../includes/upload.php", true);
        xhrUpload.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhrUpload.send(
          "image=" +
            encodeURIComponent(imageDataURL) +
            "&filename=" +
            filename +
            "&idNumber=" +
            idNumber
        );

        // Display the captured image for 1 second, then clear the canvas
        setTimeout(() => {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
        }, 1000);
      } else {
        console.error("Error getting image count:", response.message);
      }
    } else {
      console.error("Error getting image count:", xhrCount.statusText);
    }
  };
  xhrCount.send("idNumber=" + idNumber);
}

function editSelectedStudent() {
  var title = document.getElementById("title").textContent;
  var checkbox = document.querySelector(
    '#classlistTable tbody input[type="checkbox"]:checked'
  );

  if (checkbox == null) {
    return;
  }

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
    showToastr("info", "No students selected for deletion.");
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
    showToastr("info", "No file selected");
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

function clearDataFeatures(){

  // Extract id number from modal
  var idNumber = document.getElementById("idNumber").textContent;
  var url = "../includes/delete_features.php";

  // Send the id number to the server
  if (idNumber.length > 0) {
    $.ajax({
      url: url,
      method: "POST",
      data: { idNumber: idNumber },
      success: function (response) {
        showToastr("success", "Cleared Data!", "Reloading page...");
        fetch('../includes/delete_features.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `idNumber=${encodeURIComponent(idNumber)}`
        })
        .then(response => {
          if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
          }
          return response.json();
        })
        .then(data => {
          if (data.status === 'success') {
            console.log('Features in db are deleted successfully!');
            showToastr("success", "Cleared Data!", "Reloading page...");
          } else {
            console.error('Failed to delete features in db ' + data.message);
            showToastr("info", "Failed to clear data");
          }
        })
        .catch(error => {
          console.error('An error occurred while deleting the in db ' + error.message);
          showToastr("error", "An error occurred");
        });
      },
      error: function (error) {
        showToastr("error", "An error occurred");
      },
    });
  } else {
    // Inform the user that is no id number
    //alert("Error Encountered");
  }
}
