// Configure Toastr options
toastr.options = {
  closeButton: false,
  debug: false,
  newestOnTop: false,
  progressBar: true,
  positionClass: "toast-top-right",
  preventDuplicates: false,
  onclick: null,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "5000",
  extendedTimeOut: "1000",
  showEasing: "swing",
  hideEasing: "linear",
  showMethod: "fadeIn",
  hideMethod: "fadeOut",
};

// Function to show Toastr notification
function showToastr(type, title = "", message = "") {
  if (type == "success") {
    toastr.success(message, title);
  } else if (type == "error") {
    toastr.error(message, title);
  } else if (type == "warning") {
    toastr.warning(message, title);
  } else {
    toastr.info(message, title);
  }
}
