document.addEventListener("DOMContentLoaded", function () {
  // Show / Hide Question 6
  var hiddenQuestions = document.querySelector(".hidden-question");
  hiddenQuestions.style.display = "none";

  var radioQ3 = document.getElementsByName("q3");
  var radioQ4 = document.getElementsByName("q4");
  var radioQ5 = document.getElementsByName("q5");

  function handleRadioChange() {
    var showQuestions =
      radioQ3[0].checked ||
      radioQ4[0].checked ||
      radioQ5[1].checked ||
      radioQ5[3].checked;
    hiddenQuestions.style.display = showQuestions ? "block" : "none";
  }

  for (var i = 0; i < radioQ3.length; i++) {
    radioQ3[i].addEventListener("change", handleRadioChange);
  }

  for (var i = 0; i < radioQ4.length; i++) {
    radioQ4[i].addEventListener("change", handleRadioChange);
  }

  for (var i = 0; i < radioQ5.length; i++) {
    radioQ5[i].addEventListener("change", handleRadioChange);
  }
});

// Form Validator
function validateForm() {
  // Validate radio buttons
  var checkedRadioCount = document.querySelectorAll(
    'input[type="radio"]:checked'
  ).length;
  if (checkedRadioCount !== 4) {
    alert("Please answer all required questions.");
    return;
  }

  // Validate checkboxes
  var checkboxes = document.getElementsByName("q2[]");
  var checkboxValid = Array.prototype.slice
    .call(checkboxes)
    .some((x) => x.checked);
  if (!checkboxValid) {
    alert("Please check at least 1 checkbox.");
    return;
  }

  // Validate textboxes
  var textboxes = document.querySelectorAll(".textbox");
  var hiddenQuestions = document.querySelector(".hidden-question");

  if (hiddenQuestions.style.display === "none") {
    var textboxes = document.querySelectorAll(
      '.textbox:not([name="q6a"]):not([name="q6b"])'
    );
    var q6aTextbox = document.querySelector('input[name="q6a"]');
    var q6bTextbox = document.querySelector('input[name="q6b"]');
    q6aTextbox.value = "";
    q6bTextbox.value = "";
  }

  for (var i = 0; i < textboxes.length; i++) {
    var value = textboxes[i].value.trim();
    var type = textboxes[i].getAttribute("name");
    if (value === "") {
      alert("Please fill in all required textboxes.");
      return;
    }

    // Validate contact number
    if (type === "q7b" || type === "q8b") {
      // Remove '+' if present and validate
      value = value.replace(/\+/g, ""); // Remove all '+' occurrences
      if (!/^\d{10,15}$/.test(value)) {
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
function openscoreModal() {
  var modal = document.getElementById("scoreModal");
  modal.style.display = "block";
}

function closescoreModal() {
  var modal = document.getElementById("scoreModal");
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

function submitForm(event) {
  event.preventDefault();

  // Question 1
  var q1 = document.getElementsByName("q1");
  var q1Value = "";
  for (var i = 0; i < q1.length; i++) {
    if (q1[i].checked) {
      q1Value = q1[i].value;
      break;
    }
  }

  // Question 2
  var q2Value = [];
  var q2Checkboxes = document.getElementsByName("q2[]");
  for (var i = 0; i < q2Checkboxes.length; i++) {
    if (q2Checkboxes[i].checked) {
      q2Value.push(q2Checkboxes[i].value);
    }
  }

  // Question 3
  var q3 = document.getElementsByName("q3");
  var q3Value = "";
  for (var i = 0; i < q3.length; i++) {
    if (q3[i].checked) {
      q3Value = q3[i].value;
      break;
    }
  }

  // Question 4
  var q4 = document.getElementsByName("q4");
  var q4Value = "";
  for (var i = 0; i < q4.length; i++) {
    if (q4[i].checked) {
      q4Value = q4[i].value;
      break;
    }
  }

  // Question 5
  var q5 = document.getElementsByName("q5");
  var q5Value = "";
  for (var i = 0; i < q5.length; i++) {
    if (q5[i].checked) {
      q5Value = q5[i].value;
      break;
    }
  }

  // Question 6
  var q6aValue = document.getElementsByName("q6a")[0].value;
  var q6bValue = document.getElementsByName("q6b")[0].value;

  // Question 7
  var q7aValue = document.getElementsByName("q7a")[0].value;
  var q7bValue = document.getElementsByName("q7b")[0].value;

  // Question 8
  var q8aValue = document.getElementsByName("q8a")[0].value;
  var q8bValue = document.getElementsByName("q8b")[0].value;
  var q8cValue = document.getElementsByName("q8c")[0].value;
  var q8dValue = document.getElementsByName("q8d")[0].value;

  console.log([q1Value, q2Value, q3Value, q4Value, q5Value, 
              q6aValue, q6bValue, 
              q7aValue, q7bValue, 
              q8aValue, q8cValue, q8aValue, q8dValue]);

  var formData = new FormData();
  formData.append("q1", q1Value);
  formData.append("q2", q2Value);
  formData.append("q3", q3Value);
  formData.append("q4", q4Value);
  formData.append("q5", q5Value);
  formData.append("q6a", q6aValue);
  formData.append("q6b", q6bValue);
  formData.append("q7a", q7aValue);
  formData.append("q7b", q7bValue);
  formData.append("q8a", q8aValue);
  formData.append("q8b", q8bValue);
  formData.append("q8c", q8cValue);
  formData.append("q8d", q8dValue);

  var url = "../includes/submit_hdf.php";

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      closeConfirmationModal();
      openscoreModal();
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
