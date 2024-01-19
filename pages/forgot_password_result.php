<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PUP HDF Attendance System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700;1,400;1,700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../css/forgot_password.css" />
  </head>
  <body>
    <div class="background">
      <div class="forgot-password-container">
        <span class="close" onclick="toLogin()">&times;</span>
        <img src="..\assets\images\graphics\forgot_password.png" />
        <h1>EMAIL SUBMITTED</h1>
        <p>Please check your email for your reset password link.</p>
      </div>
    </div>
    <script>
      function toLogin() {
        window.location.href = "../index.php";
        return false;
      }
    </script>
  </body>
</html>