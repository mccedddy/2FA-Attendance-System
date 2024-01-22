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
