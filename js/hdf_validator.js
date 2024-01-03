document.addEventListener('DOMContentLoaded', function () {
  // Show / Hide Question 6
  var hiddenQuestions = document.querySelector('.hidden-question');
  hiddenQuestions.style.display = 'none';
  var radioQ5 = document.getElementsByName('q5');
  for (var i = 0; i < radioQ5.length; i++) {
    radioQ5[i].addEventListener('change', function () {
      if (this.value === 'yes-positive' || this.value === 'yes-negative' || this.value === 'yes-pending') {
        hiddenQuestions.style.display = 'block';
      } else if (this.value === 'no') {
        hiddenQuestions.style.display = 'none';
      }
    });
  }
});

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