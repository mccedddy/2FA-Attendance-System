<?php 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TEST</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/global.css" />
</head>
<body>
  <form method="POST" action="../services/record_attendance.php">
    <input type="text" class="large-textbox" placeholder="NFC UID" name="UIDresult" />
    <input type="text" class="large-textbox" placeholder="Room" name="room" />
    <button type="submit">Simulate Tap NFC</button>
  </form>
  <form method="POST" action="../services/mark_all_absent.php">
        <input type="text" class="large-textbox" name="schedule_id" placeholder="Schedule ID" />
        <input type="text" class="large-textbox" name="date" placeholder="Date" />
        <button type="submit">Set remaining students as absent</button>
    </form>
</body>
</html>
