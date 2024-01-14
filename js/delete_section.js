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
      console.log(inputElement.value);

      // Get the value of the hidden input
      selectedSection = inputElement.value;

      // Log the section value to the console
      console.log(selectedSection);
      deleteSectionMessage.innerHTML = "DELETE SECTION " + selectedSection;
    });
  });

  deleteButton.addEventListener("click", () => {
    deleteSection();
  });
});

function deleteSection() {}
