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
    <link rel="stylesheet" href="css/login_as.css" />
  </head>
  <body>
    <div class="login-div">
      <section class="login-div-L">
        <div>
          <img
            src="assets\images\graphics\girl_with_phone.png"
            class="login-graphics"
          />
        </div>
      </section>
      <section class="login-div-R">
        <div class="login-div-center">
          <div>
            <img src="assets\images\logos\pup_logo.png" class="login-logo" />
          </div>
          <div><a1>LOGIN</a1></div>
          <div>
            <button class="login-as-button" onclick="toStudentLoginPage()">
              AS A STUDENT
            </button>
          </div>
          <div>
            <button class="login-as-button" onclick="toProfessorLoginPage()">
              AS A PROFESSOR
            </button>
          </div>
        </div>
      </section>
    </div>
    <!-- <script src="scripts.js"></script> -->
    <script>
      function toStudentLoginPage() {
        window.location.href = "pages/login_as_student.php";
        return false;
      }
      function toProfessorLoginPage() {
        window.location.href = "pages/login_as_professor.php";
        return false;
      }
    </script>
  </body>
</html>
