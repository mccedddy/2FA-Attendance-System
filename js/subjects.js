document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var editButton = document.getElementById("editStudentBtn");
  var deleteButton = document.getElementById("deleteStudentsBtn");

  // Initial setup
  sortTable();

  // Event Listeners
  editButton.addEventListener("click", () => {
    editSelectedSubject();
  });

  deleteButton.addEventListener("click", () => {
    deleteSelectedSubjects();
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
