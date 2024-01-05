function toggleMobileNavbar() {
    var mobileNavbar = document.querySelector('.navbar');
    if (mobileNavbar.style.display === 'flex') {
      mobileNavbar.style.display = 'none';
    } else {
      mobileNavbar.style.display = 'flex';
      mobileNavbar.style.justifyContent = 'center';
    }
  }