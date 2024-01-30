<?php
session_start();
require '../includes/database_connection.php';

// If logged in
if (isset($_SESSION['id_number'])) {
  // Redirect to professor homepage
  header("Location: professor_homepage.php");
}
if (isset($_SESSION['student_number'])) {
  $studentNumber = $_SESSION['student_number'];

  // SQL query
  $sql = "SELECT * FROM students WHERE student_number = '$studentNumber'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $student = mysqli_fetch_assoc($result);

    // Get student info
    if ($student) {  
      $name = strtoupper($student['last_name']) . ', ' . strtoupper($student['first_name']);
      $studentNumber = $student['student_number'];
    }
      
    // Free result from memory
    mysqli_free_result($result);
  }
} else {
  // Redirect to login
  header("Location: ../index.php");
}

// Logout
if (isset($_POST['logout'])) {
  require '../includes/logout.php';
}

// Submit HDF
if (isset($_POST['submit'])) {
  require '../includes/submit_hdf.php';
}
?>

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
    <link rel="stylesheet" href="../css/student_hdf_page.css" />
    <script>
      window.onload = function() {
        document.getElementById("hdf-form").addEventListener("keypress", function(event) {
          // Prevent form submission with enter
          if (event.keyCode === 13) {
            event.preventDefault();
          }
        });
      };
    </script>
  </head>
  <body>
    <nav class="navbar">
      <a onclick="toStudentHomepage()"><h1>PUP HDF Attendance System</h1></a>
      <form method="POST" class="logout-form">
        <button type="submit" name="logout" class="logout-button"><p class="logout-text">LOGOUT</p></button>
        <img src="../assets/images/icons/settings_black.svg" onclick="toSettings()" class="nav-button" />
      </form>
    </nav>
    <section class="main">
      <div class="main-container">
        <div class="top-container">
          <h2>Health Declaration Form</h2>
          <h4><?php echo $name ?> - <?php echo $studentNumber ?></h4>
        </div>
        <div class="bottom-container">
          <form action="" method="POST" name="hdf-form" class="hdf-form" id="hdf-form">
            <div class="questions">
              <div class="questions-L">
                <!-- Question 1 -->
                <div>
                  <h6 class="question">
                    1. Have you been vaccinated for COVID-19?
                  </h6>
                  <div class="radio-choices">
                    <label>
                      <input type="radio" name="q1" value="not-yet" />
                      Not Yet
                    </label>
                    <label>
                      <input type="radio" name="q1" value="1st-dose" />
                      1st Dose
                    </label>
                    <label>
                      <input type="radio" name="q1" value="2nd-dose" />
                      2nd Dose (Fully Vaccinated)
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q1"
                        value="1st-booster"
                      />
                      1st Booster Shot
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q1"
                        value="2nd-booster"
                      />
                      2nd Booster Shot
                    </label>
                  </div>
                </div>
                <!-- Question 2 -->
                <div>
                  <h6 class="question">
                    2. Are you experiencing any symptoms in the past 7 days such
                    as:
                  </h6>
                  <div class="checkbox-choices">
                    <div class="checkbox-choices-L">
                      <label>
                        <input type="checkbox" name="q2[]" value="fever" />
                        Fever
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="cough" />
                        Cough
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="colds" />
                        Colds
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="muscle-body-pains"
                        />
                        Muscle/body pains
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="sore-throat"
                        />
                        Sore throat
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="diarrhea" />
                        Diarrhea
                      </label>
                    </div>
                    <div class="checkbox-choices-L">
                      <label>
                        <input type="checkbox" name="q2[]" value="headache" />
                        Headache
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="shortness-of-breath"
                        />
                        Shortness of breath
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="difficulty-of-breathing"
                        />
                        Difficulty of breathing
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="loss-of-taste"
                        />
                        Loss of taste
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="loss-of-smell"
                        />
                        Loss of smell
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="none" />
                        None of the above
                      </label>
                    </div>
                  </div>
                </div>
                <!-- Question 3 -->
                <div>
                  <h6 class="question">
                    3. Have you had exposure to a probable or confirmed case in
                    the last 14 days?
                  </h6>
                  <div class="radio-choices">
                    <label>
                      <input type="radio" name="q3" value="yes" /> Yes
                    </label>
                    <label>
                      <input type="radio" name="q3" value="no" /> No
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q3"
                        value="uncertain"
                      />
                      Uncertain
                    </label>
                  </div>
                </div>
                <!-- Question 4 -->
                <div>
                  <h6 class="question">
                    4. Have you had in contact with somebody with body pains,
                    headache, sore throat, fever, diarrhea, cough, colds,
                    shortness of breath, loss of taste, or loss of smell in the
                    past 7 days?
                  </h6>
                  <div class="radio-choices">
                    <label>
                      <input type="radio" name="q4" value="yes" /> Yes
                    </label>
                    <label>
                      <input type="radio" name="q4" value="no" /> No
                    </label>
                  </div>
                </div>
              </div>
              <div class="questions-R">
                <!-- Question 5 -->
                <div>
                  <h6 class="question">
                    5. Have you been tested for Covid-19 in the last 14 days?
                  </h6>
                  <div class="radio-choices">
                    <label>
                      <input type="radio" name="q5" value="no" /> No
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-positive"
                      />
                      Yes-Positive
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-negative"
                      />
                      Yes-Negative
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-pending"
                      />
                      Yes-Pending
                    </label>
                  </div>
                </div>
                <div class="hidden-question">
                  <!-- Question 6a -->
                  <h6 class="question">6.a When was your most recent visit to this location?</h6>
                  <div class="q6-textbox-container">
                    <input type="text" name="q6a" class="textbox" />
                  </div>
                  <!-- Question 6a -->
                  <h6 class="question">6.b Since Then until today, what places have you been? (Besides of your home/lodging area)?</h6>
                  <div class="q6-textbox-container">
                    <input type="text" name="q6b" class="textbox" />
                  </div>
                </div>

                <h5>Respondent Details</h5>
                <!-- Question 7a -->
                <div class="question-textbox">
                  <h6 class="question">7.a Email Address</h6>
                  <input type="email" name="q7a" class="textbox" />
                </div>
                <!-- Question 7b -->
                <div class="question-textbox">
                  <h6 class="question">7.b Contact Number</h6>
                  <input type="tel" name="q7b" class="textbox" />
                </div>
                <h5>Contact Person Details</h5>
                <!-- Question 8a -->
                <div class="question-textbox">
                  <h6 class="question">8.a Name</h6>
                  <input type="text" name="q8a" class="textbox" />
                </div>
                <!-- Question 8b -->
                <div class="question-textbox">
                  <h6 class="question">8.b Contact Number</h6>
                  <input type="tel" name="q8b" class="textbox" />
                </div>
                <!-- Question 8c -->
                <div class="question-textbox">
                  <h6 class="question">8.c Email Address</h6>
                  <input type="email" name="q8c" class="textbox" />
                </div>
                <!-- Question 8d -->
                <div class="question-textbox">
                  <h6 class="question">
                    8.d Relationship to the contact person
                  </h6>
                  <input type="text" name="q8d" class="textbox" />
                </div>
              </div>
            </div>
          </form>
          <div class="button-container">
            <button class="confirm-button" id="confirm-button" onclick="validateForm()">
              SUBMIT
            </button>
          </div>
        </div>
      </div>
    </section>

    <div id="confirmationModal" class="modal-blur">
      <div class="modal-content">
        <img src="../assets/images/graphics/falling_girl_with_phone.png" class="confirmation-graphics" />
        <h6>ARE YOU SURE?</h6>
        <p>Kindly make sure that the data submitted is all true.</p>
        <div class="buttons-container">
          <button id="confirmationBackButton" onclick="closeConfirmationModal()">BACK</button>
          <button id="submitButton" onclick="submitForm(event)">SUBMIT</button>
        </div>
      </div>
    </div>

    <div id="scoreModal" class="modal-blur">
      <div class="modal-content">
        <span class="close-modal" onclick="closescoreModal()">&times;</span>
        <div class="score-container">
          <div id="yellow">
            <div id="white">
              <h2 id="score">Score</h2>
            </div>
          </div>
        </div>
        <h2 id="result">RESULT</h2>
        <p id="message1">Message 1</p>
        <h6 id="message2">Message 2</h6>
      </div>
    </div>

    <script src="../js/hdf_validator.js"></script>
    <script>
      function toStudentHomepage() {
        window.location.href = "student_homepage.php";
        return false;
      }
      function toSettings() {
        window.location.href = "student_settings_page.php";
        return false;
      }
    </script>
  </body>
</html>
