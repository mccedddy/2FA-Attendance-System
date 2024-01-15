document.addEventListener("DOMContentLoaded", function () {
  var table = document.getElementById("attendanceTable");
  var tbody = table.querySelector("tbody");
  var importButton = document.getElementById("import");
  var exportButton = document.getElementById("export");

  importButton.addEventListener("click", () => {
    importClasslist();
  });

  exportButton.addEventListener("click", () => {
    exportClasslist();
  });
});

function importClasslist() {
  console.log("Clicked import!");
}

function exportClasslist() {
  console.log("Clicked export!");
}
