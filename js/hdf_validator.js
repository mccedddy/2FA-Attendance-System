document.addEventListener('DOMContentLoaded', function () {
  // Show / Hide Question 6
  var hiddenQuestions = document.querySelector('.hidden-question');
  hiddenQuestions.style.display = 'none';

  var radioQ3 = document.getElementsByName('q3');
  var radioQ4 = document.getElementsByName('q4');
  var radioQ5 = document.getElementsByName('q5');

  function handleRadioChange() {
    var showQuestions = radioQ3[0].checked || radioQ4[0].checked || radioQ5[1].checked || radioQ5[3].checked;
    hiddenQuestions.style.display = showQuestions ? 'block' : 'none';
  }

  for (var i = 0; i < radioQ3.length; i++) {
    radioQ3[i].addEventListener('change', handleRadioChange);
  }

  for (var i = 0; i < radioQ4.length; i++) {
    radioQ4[i].addEventListener('change', handleRadioChange);
  }

  for (var i = 0; i < radioQ5.length; i++) {
    radioQ5[i].addEventListener('change', handleRadioChange);
  }
});

// Form Validator
function validateForm() {
  // Validate radio buttons
  var checkedRadioCount = document.querySelectorAll('input[type="radio"]:checked').length;
  if (checkedRadioCount !== 4) {
    alert("Please answer all required questions.");
    return;
  }

  // Validate checkboxes
  var checkboxes = document.getElementsByName("q2[]");
  var checkboxValid = Array.prototype.slice.call(checkboxes).some(x => x.checked);
  if (!checkboxValid) {
    alert("Please check at least 1 checkbox.");
    return;
  }

  // Validate textboxes
  var textboxes = document.querySelectorAll('.textbox');
  var hiddenQuestions = document.querySelector('.hidden-question');

  if (hiddenQuestions.style.display === 'none') {
    var textboxes = document.querySelectorAll('.textbox:not([name="q6a"]):not([name="q6b"])');
    var q6aTextbox = document.querySelector('input[name="q6a"]');
    var q6bTextbox = document.querySelector('input[name="q6b"]');
    q6aTextbox.value = "";
    q6bTextbox.value = "";
  } 

  for (var i = 0; i < textboxes.length; i++) {
    var value = textboxes[i].value.trim();
    var type = textboxes[i].getAttribute('name');
    if (value === "") {
      alert("Please fill in all required textboxes.");
      return;
    }

    // Validate contact number
    if (type === "q7b" || type === "q8b") {
      if (!/^\d{10,13}$/.test(value)) {
        alert("Please enter a valid contact number.");
        return;
      }
    }

    // Validate email address
    if (type === "q7a" || type === "q8c") {
      if (!/^\S+@\S+\.\S+$/.test(value)) {
        alert("Please enter a valid email address.");
        return;
      }
    }
  }

  // If all validations pass, open the confirmation modal
  openConfirmationModal();
}


// Popup Controller
function openPerfectScoreModal() {
  var modal = document.getElementById("perfectScoreModal");
  modal.style.display = "block";
}

function closePerfectScoreModal() {
  var modal = document.getElementById("perfectScoreModal");
  modal.style.display = "none";
}

function openConfirmationModal() {
  var confirmationModal = document.getElementById("confirmationModal");
  confirmationModal.style.display = "block";
}

function closeConfirmationModal() {
  var confirmationModal = document.getElementById("confirmationModal");
  confirmationModal.style.display = "none";
}

function submitForm() {
  closeConfirmationModal();
  openPerfectScoreModal();
}