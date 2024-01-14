document.addEventListener("DOMContentLoaded", function () {
  // Get all elements with class 'delete-section'
  var deleteSections = document.querySelectorAll(".delete-section");
  var deleteSectionMessage = document.getElementById("deleteSectionMessage");
  var deleteButton = document.getElementById("deleteButton");
  var selectedSection = "";

  // Add click event listener to each delete section
  deleteSections.forEach(function (deleteSection) {
    deleteSection.addEventListener("click", function () {
      // Get the sibling input element
      var inputElement = deleteSection.nextElementSibling;

      // Get the value of the hidden input
      selectedSection = inputElement.value;

      deleteSectionMessage.innerHTML = "DELETE SECTION " + selectedSection;
    });
  });

  deleteButton.addEventListener("click", () => {
    deleteSection(selectedSection);
  });
});

function deleteSection(selectedSection) {
  var url = "../includes/delete_section.php";
  var formData = new FormData();
  formData.append("selectedSection", selectedSection);

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      // Redirect to homepage
      if (data["status"] == "success") {
        window.location.href = "admin_homepage.php";
        return false;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
