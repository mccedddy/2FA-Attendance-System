<?php 
require 'database_connection.php';
date_default_timezone_set('Asia/Manila');
require_once 'encryption.php';
$encryptionHelper = new EncryptionHelper($encryptionKey);

// FETCH

// Fetch profile data
if (!isset($_SESSION['user_name'])) {
  $userId = $_SESSION['id_number'];
  $sql = "SELECT * FROM professors WHERE id_number = '$userId'";
  $result = mysqli_query($connection, $sql);

  // Check if the query was successful
  if ($result) {
    $professor = mysqli_fetch_assoc($result);

    // Get professor info
    if ($professor) {
      $userName = strtoupper($professor['last_name']) . ', ' . strtoupper($professor['first_name']);
      $_SESSION['user_name'] = $userName;
      $userEmail = $encryptionHelper->decryptData($professor['email']);
      $_SESSION['user_email'] = $userEmail;
      $userPassword = $professor['password'];
      $_SESSION['user_password'] = $userPassword;
    }
          
    // Free result from memory
    mysqli_free_result($result);
  } else {
    echo 'Error: ' . mysqli_error($connection);
  }
}

// Fetch sections
function fetchSections() {
  global $connection;
  $sectionsSQL = "SELECT * FROM sections";
  $sectionsResult = mysqli_query($connection, $sectionsSQL);
  $sections = [];
  while ($row = mysqli_fetch_assoc($sectionsResult)) {
    $sectionsInfo = [
              'section'      => $row['section'],
            ];
    $sections[] = $sectionsInfo['section'];
  }
  return $sections;
  mysqli_free_result($sectionsResult);
}

// Fetch classlist
function fetchClasslist($tableName, $condition = '') {
  global $connection;
  global $encryptionHelper;
    
  $classlistSQL = "SELECT * FROM $tableName $condition";
  $classlistResult = mysqli_query($connection, $classlistSQL);
  $classlist = [];
  
  while ($row = mysqli_fetch_assoc($classlistResult)) {
    $profileInfo = [
          'lastName'      => $row['last_name'],
          'firstName'     => $row['first_name'],
          'idNumber'      => isset($row['id_number']) ? $row['id_number'] : $row['id_number'],
          'section'       => isset($row['section']) ? $row['section'] : '',
          'nfcUid'        => isset($row['nfc_uid']) ? $row['nfc_uid'] : '',
          'email'         => $encryptionHelper->decryptData($row['email']),
      ];
    $classlist[] = $profileInfo;
  }
  mysqli_free_result($classlistResult);   
  return $classlist;
}

// Fetch schedule
function fetchSchedule() {
  global $connection;
  global $sectionPage;
  
  $scheduleSQL = "SELECT *, CONCAT(professors.last_name, ', ', professors.first_name) AS professor_name
                FROM schedule
                INNER JOIN subjects ON schedule.subject_code = subjects.subject_code
                INNER JOIN professors ON schedule.professor = professors.id_number
                WHERE schedule.section = '$sectionPage' ORDER BY FIELD(schedule.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), schedule.start_time";
  $scheduleResult = mysqli_query($connection, $scheduleSQL);
  $schedule = [];

  while ($row = mysqli_fetch_assoc($scheduleResult)) {
      $classInfo = [
          'subjectCode'   => $row['subject_code'],
          'subjectName'   => $row['subject_name'],
          'day'           => $row['day'],
          'startTime'     => $row['start_time'],
          'endTime'       => $row['end_time'],
          'professor'     => $row['professor_name'],
          'id'            => $row['id'],
          'section'       => $row['section']
      ];
      $schedule[] = $classInfo;
  }
  mysqli_free_result($scheduleResult);
  return $schedule;
}

// Fetch subjects
function fetchSubjects() {
  global $connection;
  
  $subjectsSQL = "SELECT * FROM subjects";
  $subjectsResult = mysqli_query($connection, $subjectsSQL);
  $subjects = [];
  while ($row = mysqli_fetch_assoc($subjectsResult)) {
    $subjectInfo = [
              'subjectCode'       => $row['subject_code'],
              'subjectName'       => $row['subject_name'],
            ];
    $subjects[] = $subjectInfo;
  }
  mysqli_free_result($subjectsResult);
  return $subjects;
}

// ADD

// Add profile (student or professor)
function addProfile($profileType) {
  global $connection;
  global $encryptionHelper;

  // Common fields
  $lastName = $_POST['last_name'];
  $firstName = $_POST['first_name'];
  $email = $_POST['email'];
  $idNumber = $_POST['id_number'];
    
  // Fields specific to students
  if ($profileType === 'student') {
    $idNumber = $_POST['id_number'];
    $section = $_POST['year'] . '-' . $_POST['section'];
    $nfcUid = $_POST['nfc_uid'];
  }
    
  // Hash the password (Default: Last Name)
  $hashedPassword = password_hash($lastName, PASSWORD_DEFAULT);
    
  // Encrypt email
  $encryptedEmail = $encryptionHelper->encryptData($email);
    
  // SQL query
  if ($profileType === 'student') {
    $sql = "INSERT INTO students (last_name, first_name, id_number, section, nfc_uid, email, password)
          VALUES ('$lastName', '$firstName', '$idNumber', '$section', '$nfcUid', '$encryptedEmail', '$hashedPassword')";
  } elseif ($profileType === 'professor') {
      $sql = "INSERT INTO professors (last_name, first_name, id_number, email, password)
          VALUES ('$lastName', '$firstName', '$idNumber', '$encryptedEmail', '$hashedPassword')";
  }
    
  $stmt = mysqli_prepare($connection, $sql);
    
  try {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
        
    // Redirect
    if ($profileType === 'student') {
      header("Location: admin_classlist.php");
    } elseif ($profileType === 'professor') {
      header("Location: admin_professors.php");
    }
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      if ($profileType === 'student') {
        header("Location: admin_classlist.php");
      } elseif ($profileType === 'professor') {
        header("Location: admin_professors.php");
      }
      exit;
    } else {
      throw $exception;
    }
  }
}

// Add section
if (isset($_POST['add-section'])) {
  $section = $_POST['year'] . '-' . $_POST['section'];

  // Check if section exists
  $checkSectionSQL = "SELECT COUNT(*) as sectionCount FROM sections WHERE section = '$section'";

  // Prepare and execute query
  $stmtCheckSection = mysqli_prepare($connection, $checkSectionSQL);
  mysqli_stmt_execute($stmtCheckSection);
  mysqli_stmt_bind_result($stmtCheckSection, $sectionCount);
  mysqli_stmt_fetch($stmtCheckSection);
  mysqli_stmt_close($stmtCheckSection);

  // Create section
  if ($sectionCount == 0) {
    // SQL query
    $sql = "INSERT INTO sections (section) VALUES ('$section')";

    // Execute query
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }
  header("Location: ../pages/admin_sections.php");
}

// Add schedule
if (isset($_POST['add-class'])) {
  $section = $_SESSION['selected_section'];
  $subjectCode = $_POST['subject'];
  $day = $_POST['day'];
  $startTime = $_POST['start_time'];
  $endTime = $_POST['end_time'];
  $professor = $_POST['professor'];

  // SQL query 
  $sql = "INSERT INTO schedule (section, subject_code, day, start_time, end_time, professor)
            VALUES ('$section', '$subjectCode', '$day', '$startTime', '$endTime', '$professor')";

  // Use prepared statement
  $stmt = mysqli_prepare($connection, $sql);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    header("Location: ../pages/admin_schedule.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: ../pages/admin_schedule.php");
      exit;
    } else {
      throw $exception;
    }
  }
}

// Add subject
if (isset($_POST['add-subject'])) {
  $subjectCode = $_POST['subject_code'];
  $subjectName = $_POST['subject_name'];

  // SQL query
  $sql = "INSERT INTO subjects (subject_code, subject_name)
            VALUES ('$subjectCode', '$subjectName')";

  // Use prepared statement
  $stmt = mysqli_prepare($connection, $sql);

  try {
    // Execute query
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
    
    header("Location: ../pages/admin_subjects.php");
  } catch (mysqli_sql_exception $exception) {
    // Check if duplicate entry
    if ($exception->getCode() == 1062) {
      header("Location: ../pages/admin_subjects.php");
      exit; 
    } else {
      throw $exception;
    }
  }
}

// Add attendance
if (isset($_POST['student'])) {
  $idNumber = $_POST['student'];
  $status = $_POST['status'];
  $time = $_POST['time'];
  $date = $_POST['date'];
  $room = $_POST['room'];
  $subjectCode = $_POST['subject'];
  $idNumber = $_POST['professor'];
  $section = $_POST['section'];

  $dayOfWeek = date('l', strtotime($date));

  // SQL query to retrieve the schedule id
  $sql = "SELECT * 
          FROM schedule 
          WHERE subject_code = '$subjectCode' 
          AND section = '$section' 
          AND professor = '$idNumber'
          AND day = '$dayOfWeek'
          AND '$time' BETWEEN DATE_SUB(start_time, INTERVAL 1 HOUR) AND end_time";

  $stmt = mysqli_prepare($connection, $sql);
  mysqli_stmt_execute($stmt);

  // Execute query and get the result
  $result = mysqli_stmt_get_result($stmt);
  $scheduleData = mysqli_fetch_assoc($result);
  echo json_encode(['scheduleData' => $scheduleData]);

  if ($result && mysqli_num_rows($result) > 0) {
    $scheduleId = $scheduleData['id'];

    // SQL query to insert data into the attendance table
    $sql_insert = "INSERT INTO attendance (id_number, room, time, date, status, schedule_id)
                   VALUES ('$idNumber', '$room', '$time', '$date', '$status', '$scheduleId')";

    // Execute insert query
    mysqli_query($connection, $sql_insert);

  } else {
    exit;
  }
}

// EDIT

// DELETE

?>