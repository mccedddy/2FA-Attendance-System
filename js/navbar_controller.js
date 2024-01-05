function toggleMobileNavbar() {
    var mobileNavbar = document.querySelector('.navbar');
    var hamburger = document.querySelector('.hamburger');
    if (mobileNavbar.style.display === 'flex') {
      mobileNavbar.style.display = 'none';
      hamburger.style.display = 'block';
    } else {
      mobileNavbar.style.display = 'flex';
      hamburger.style.display = 'none';
      mobileNavbar.style.justifyContent = 'center';
    }
  }