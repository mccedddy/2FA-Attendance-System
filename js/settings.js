/* const newEmail = document.getElementById(newEmail);
const confirmEmail = document.getElementById(confirmEmail);
const emailForm = document.getElementById(emailForm);
const errorElement = document.getElementById(emailError);

emailForm.addEventListener('submit', (e) => {
  e.preventDefault();
})




/* document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('emailForm');

  form.addEventListener('submit', function(event) {
      // Clear previous errors
      clearErrors();

      // Perform validation
      const newEmail= document.getElementById('newEmail').value;
      const currentEmail = document.getElementById('currentEmail').value;

      let valid = true;

      if (!validateUsername(email)) {
          showError('usernameError', 'Username is required and must be at least 3 characters long.');
          valid = false;
      }
  });

  submitButton.disabled = !valid;

  function validateUsername(email) {
      return newEmail == currentEmail;
  }

  function showError(elementId, message) {
      document.getElementById(elementId).textContent = message;
  }

  function clearErrors() {
      document.getElementById('emailError').textContent = '';
  }
});


/* document.addEventListener("DOMContentLoaded", function () {
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
  

   */ */ */