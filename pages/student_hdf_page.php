<?php
session_start();
require '../includes/database_connection.php';

require_once '../includes/encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// If logged in
if (isset($_SESSION['id_number'])) {
  // Redirect to professor homepage
  header("Location: professor_home.php");
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

// Fetch latest HDF data for the student
$sql = "SELECT * FROM hdf WHERE student_number = '$studentNumber' ORDER BY timestamp DESC LIMIT 1";

$result = mysqli_query($connection, $sql);
if ($result) {
  $hdfData = mysqli_fetch_assoc($result);
  mysqli_free_result($result);

  $q1DefaultValue = isset($hdfData['q1']) ? $hdfData['q1'] : '';
  $q2DefaultValue = isset($hdfData['q2']) ? $hdfData['q2'] : '';
  $q3DefaultValue = isset($hdfData['q3']) ? $hdfData['q3'] : '';
  $q4DefaultValue = isset($hdfData['q4']) ? $hdfData['q4'] : '';
  $q5DefaultValue = isset($hdfData['q5']) ? $hdfData['q5'] : '';
  $q6aDefaultValue = isset($hdfData['q6a']) ? $hdfData['q6a'] : '';
  $q6bDefaultValue = isset($hdfData['q6b']) ? $hdfData['q6b'] : '';
  $q7aDefaultValue = isset($hdfData['q7a']) ? $encryptionHelper->decryptData($hdfData['q7a']) : '';
  $q7bDefaultValue = isset($hdfData['q7b']) ? $encryptionHelper->decryptData($hdfData['q7b']) : '';
  $q8aDefaultValue = isset($hdfData['q8a']) ? $encryptionHelper->decryptData($hdfData['q8a']) : '';
  $q8bDefaultValue = isset($hdfData['q8b']) ? $encryptionHelper->decryptData($hdfData['q8b']) : '';
  $q8cDefaultValue = isset($hdfData['q8c']) ? $encryptionHelper->decryptData($hdfData['q8c']) : '';
  $q8dDefaultValue = isset($hdfData['q8d']) ? $hdfData['q8d'] : '';

  $symptoms = explode(',', $q2DefaultValue);
  $symptomStatus = array_fill_keys($symptoms, true);
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
                      <input type="radio" name="q1" value="not-yet" <?php if ($q1DefaultValue === 'not-yet') echo 'checked'; ?> />
                      Not Yet
                    </label>
                    <label>
                      <input type="radio" name="q1" value="1st-dose" <?php if ($q1DefaultValue === '1st-dose') echo 'checked'; ?>/>
                      1st Dose
                    </label>
                    <label>
                      <input type="radio" name="q1" value="2nd-dose" <?php if ($q1DefaultValue === '2nd-dose') echo 'checked'; ?>/>
                      2nd Dose (Fully Vaccinated)
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q1"
                        value="1st-booster"
                        <?php if ($q1DefaultValue === '1st-booster') echo 'checked'; ?>
                      />
                      1st Booster Shot
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q1"
                        value="2nd-booster"
                        <?php if ($q1DefaultValue === '2nd-booster') echo 'checked'; ?>
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
                        <input type="checkbox" name="q2[]" value="fever" <?php if (isset($symptomStatus['fever'])) echo 'checked'; ?> />
                        Fever
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="cough" <?php if (isset($symptomStatus['cough'])) echo 'checked'; ?> />
                        Cough
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="colds" <?php if (isset($symptomStatus['colds'])) echo 'checked'; ?> />
                        Colds
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="muscle-body-pains"
                          <?php if (isset($symptomStatus['muscle-body-pains'])) echo 'checked'; ?>
                        />
                        Muscle/body pains
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="sore-throat"
                          <?php if (isset($symptomStatus['sore-throat'])) echo 'checked'; ?>
                        />
                        Sore throat
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="diarrhea" <?php if (isset($symptomStatus['diarrhea'])) echo 'checked'; ?> />
                        Diarrhea
                      </label>
                    </div>
                    <div class="checkbox-choices-L">
                      <label>
                        <input type="checkbox" name="q2[]" value="headache" <?php if (isset($symptomStatus['headache'])) echo 'checked'; ?> />
                        Headache
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="shortness-of-breath"
                          <?php if (isset($symptomStatus['shortness-of-breath'])) echo 'checked'; ?>
                        />
                        Shortness of breath
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="difficulty-of-breathing"
                          <?php if (isset($symptomStatus['difficulty-of-breathing'])) echo 'checked'; ?>
                        />
                        Difficulty of breathing
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="loss-of-taste"
                          <?php if (isset($symptomStatus['loss-of-taste'])) echo 'checked'; ?>
                        />
                        Loss of taste
                      </label>
                      <label>
                        <input
                          type="checkbox"
                          name="q2[]"
                          value="loss-of-smell"
                          <?php if (isset($symptomStatus['loss-of-smell'])) echo 'checked'; ?>
                        />
                        Loss of smell
                      </label>
                      <label>
                        <input type="checkbox" name="q2[]" value="none" <?php if (isset($symptomStatus['none'])) echo 'checked'; ?> />
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
                      <input type="radio" name="q3" value="yes" <?php if ($q3DefaultValue === 'yes') echo 'checked'; ?> /> Yes
                    </label>
                    <label>
                      <input type="radio" name="q3" value="no" <?php if ($q3DefaultValue === 'no') echo 'checked'; ?> /> No
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q3"
                        value="uncertain"
                        <?php if ($q3DefaultValue === 'uncertain') echo 'checked'; ?>
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
                      <input type="radio" name="q4" value="yes" <?php if ($q4DefaultValue === 'yes') echo 'checked'; ?> /> Yes
                    </label>
                    <label>
                      <input type="radio" name="q4" value="no" <?php if ($q4DefaultValue === 'no') echo 'checked'; ?> /> No
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
                      <input type="radio" name="q5" value="no" <?php if ($q5DefaultValue === 'no') echo 'checked'; ?> /> No
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-positive"
                        <?php if ($q5DefaultValue === 'yes-positive') echo 'checked'; ?>
                      />
                      Yes-Positive
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-negative"
                        <?php if ($q5DefaultValue === 'yes-negative') echo 'checked'; ?>
                      />
                      Yes-Negative
                    </label>
                    <label>
                      <input
                        type="radio"
                        name="q5"
                        value="yes-pending"
                        <?php if ($q5DefaultValue === 'yes-pending') echo 'checked'; ?>
                      />
                      Yes-Pending
                    </label>
                  </div>
                </div>
                <div class="hidden-question">
                  <!-- Question 6a -->
                  <h6 class="question">6.a When was your most recent visit to this location?</h6>
                  <div class="q6-textbox-container">
                    <input type="text" name="q6a" class="textbox" value="<?php echo $q6aDefaultValue; ?>" />
                  </div>
                  <!-- Question 6a -->
                  <h6 class="question">6.b Since Then until today, what places have you been? (Besides of your home/lodging area)?</h6>
                  <div class="q6-textbox-container">
                    <input type="text" name="q6b" class="textbox" value="<?php echo $q6bDefaultValue; ?>" />
                  </div>
                </div>

                <h5>Respondent Details</h5>
                <!-- Question 7a -->
                <div class="question-textbox">
                  <h6 class="question">7.a Email Address</h6>
                  <input type="email" name="q7a" class="textbox" value="<?php echo $q7aDefaultValue; ?>" />
                </div>
                <!-- Question 7b -->
                <div class="question-textbox">
                  <h6 class="question">7.b Contact Number</h6>
                  <input type="tel" name="q7b" class="textbox" value="<?php echo $q7bDefaultValue; ?>" />
                </div>
                <h5>Contact Person Details</h5>
                <!-- Question 8a -->
                <div class="question-textbox">
                  <h6 class="question">8.a Name</h6>
                  <input type="text" name="q8a" class="textbox" value="<?php echo $q8aDefaultValue; ?>" />
                </div>
                <!-- Question 8b -->
                <div class="question-textbox">
                  <h6 class="question">8.b Contact Number</h6>
                  <input type="tel" name="q8b" class="textbox" value="<?php echo $q8bDefaultValue; ?>" />
                </div>
                <!-- Question 8c -->
                <div class="question-textbox">
                  <h6 class="question">8.c Email Address</h6>
                  <input type="email" name="q8c" class="textbox" value="<?php echo $q8cDefaultValue; ?>" />
                </div>
                <!-- Question 8d -->
                <div class="question-textbox">
                  <h6 class="question">
                    8.d Relationship to the contact person
                  </h6>
                  <input type="text" name="q8d" class="textbox" value="<?php echo $q8dDefaultValue; ?>" />
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
