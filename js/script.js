$(document).ready(function () {
  console.log("LOADED!");
  $("#submit-button").click(function () {
    checked = $("input[type=checkbox]:checked").length;

    if (!checked) {
      alert("You must check at least one checkbox.");
      return false;
    }
  });
});
