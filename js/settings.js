document.addEventListener("DOMContentLoaded", function () {
    var emailButton = document.getElementById("changeEmailBtn");
    var passButton = document.getElementById("changePassBtn");
  
    // Initial setup
    sortTable();
  
    // Event Listeners
    emailButton.addEventListener("click", () => {
        emailValidation();
    });
  
    passButton.addEventListener("click", () => {
        passwordValidation();
    });
  });

  function emailValidation(){
    var newEmail = document.getElementById("newEmail").value;
    var confirmEmail = document.getElementById("confirmEmail").value;

    if (newEmail !== confirmEmail) {
      // Prevent form submission
      alert("New email and confirm email do not match. Please try again.");
      return false; // Returning false prevents the form submission
    }

    // Allow form submission
    return true;
  }

  function passwordValidation(){
   
  };
  

  