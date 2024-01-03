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
  closeConfirmationModal(); // Close the confirmation modal
  openPerfectScoreModal(); // Show the congratulations modal
}