<?php
include 'db_connect.php';
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST['student_name']);
    $student_number = trim($_POST['student_number']);

    // Get current date and time
    $sign_in_time = date("Y-m-d H:i:s");
    $today = date('Y-m-d');

    // Determine status (late after 8AM)
    $hour = date("H");
    $status = ($hour >= 8) ? "Late" : "On Time";

    // Check for duplicates (same student same date)
    $check = $conn->prepare("SELECT * FROM attendance WHERE student_number = ? AND DATE(sign_in_time) = ?");
    $check->bind_param("ss", $student_number, $today);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "<p class='error'>⚠️ You have already signed in today.</p>";
    } else {
        $insert = $conn->prepare("INSERT INTO attendance (student_name, student_number, sign_in_time, status) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $student_name, $student_number, $sign_in_time, $status);
        if ($insert->execute()) {
            $message = "<p class='success'>✅ Attendance recorded successfully!</p>";
        } else {
            $message = "<p class='error'>❌ Database error. Please try again.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Digital Attendance Register</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #e1bee7, #bbdefb, #f8bbd0);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: white;
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.1);
      width: 360px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .container:hover {
      transform: translateY(-5px);
    }

    h2 {
      color: #4a148c;
      margin-bottom: 25px;
    }

    label {
      display: block;
      text-align: left;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
      transition: border 0.3s;
    }

    input[type="text"]:focus {
      border-color: #7b1fa2;
      outline: none;
    }

    button {
      width: 100%;
      background-color: #7b1fa2;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #4a148c;
    }

    .message {
      margin-top: 15px;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }

    .admin-link {
      margin-top: 20px;
      display: inline-block;
      text-decoration: none;
      color: #512da8;
      font-weight: bold;
    }

    .admin-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Digital Attendance Register</h2>

    <form method="POST">
      <label for="student_name">Full Name:</label>
      <input type="text" id="student_name" name="student_name" placeholder="Enter full name" required>

      <label for="student_number">Student Number:</label>
      <input type="text" id="student_number" name="student_number" placeholder="Enter student number" required pattern="[0-9]{9}" title="Enter a valid 9-digit student number">

      <button type="submit">Sign In</button>
    </form>

    <div class="message">
      <?php echo $message; ?>
    </div>

    <a href="view_records.php" class="admin-link">👩‍🏫 View Attendance Dashboard</a>
  </div>
</body>
</html>
