<?php
include('db_connect.php');

$success = false;
$error_msg = "";
$student_name = "";
$student_number = "";
$status = "On Time";
$time_str = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = trim($_POST['student_name'] ?? '');
    $student_number = trim($_POST['student_number'] ?? '');

    $now = new DateTime();
    $current_time = $now->format('Y-m-d H:i:s');
    $time_str = $now->format('H:i');
    $hour = (int)$now->format('H');
    
    // Automatic late status calculation (8:00 AM threshold)
    $status = ($hour >= 8) ? "Late" : "On Time";

    if (!empty($student_name) && !empty($student_number)) {
        $stmt = $conn->prepare("INSERT INTO attendance (student_name, student_number, sign_in_time, status) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $student_name, $student_number, $current_time, $status);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $sql = "INSERT INTO attendance (student_name, student_number, sign_in_time, status) 
                    VALUES ('$student_name', '$student_number', '$current_time', '$status')";
            if ($conn->query($sql) === TRUE) {
                $success = true;
            } else {
                $error_msg = "Database error: " . $conn->error;
            }
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Confirmation</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
      overflow: hidden;
      position: relative;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Floating Background Shapes */
    .bg-shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.18);
      backdrop-filter: blur(8px);
      animation: float 8s ease-in-out infinite;
      z-index: 1;
    }
    .shape1 { width: 180px; height: 180px; top: 10%; left: 10%; animation-delay: 0s; }
    .shape2 { width: 240px; height: 240px; bottom: 12%; right: 8%; animation-delay: 2s; }
    .shape3 { width: 120px; height: 120px; top: 65%; left: 12%; animation-delay: 4s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-22px) rotate(10deg); }
    }

    /* Main Glassmorphism Card */
    .card {
      position: relative;
      z-index: 10;
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.7);
      padding: 40px 32px;
      border-radius: 28px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 420px;
      text-align: center;
      animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popIn {
      from { opacity: 0; transform: scale(0.85); }
      to { opacity: 1; transform: scale(1); }
    }

    .icon-wrapper {
      width: 76px;
      height: 76px;
      margin: 0 auto 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .success-icon {
      background: linear-gradient(135deg, #11998e, #38ef7d);
      color: white;
      animation: pulse 2s infinite;
    }

    .error-icon {
      background: linear-gradient(135deg, #ff416c, #ff4b2b);
      color: white;
    }

    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(56, 239, 125, 0.5); }
      70% { box-shadow: 0 0 0 18px rgba(56, 239, 125, 0); }
      100% { box-shadow: 0 0 0 0 rgba(56, 239, 125, 0); }
    }

    h2 {
      color: #2c3e50;
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .subtext {
      color: #6c757d;
      font-size: 14px;
      margin-bottom: 24px;
    }

    /* Details Card */
    .details-box {
      background: #f8f9fa;
      border-radius: 16px;
      padding: 18px;
      margin-bottom: 25px;
      text-align: left;
      border: 1px solid #e9ecef;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px dashed #dee2e6;
      font-size: 14px;
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      color: #6c757d;
      font-weight: 600;
    }

    .detail-value {
      color: #212529;
      font-weight: 700;
    }

    /* Badges */
    .badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 12px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .badge-ontime {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .badge-late {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    /* Navigation Buttons */
    .btn {
      display: block;
      width: 100%;
      padding: 14px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      transition: all 0.3s ease;
      margin-bottom: 12px;
    }

    .btn-dashboard {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      box-shadow: 0 6px 20px rgba(37, 117, 252, 0.4);
    }

    .btn-dashboard:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(37, 117, 252, 0.6);
    }

    .btn-signin {
      background: rgba(108, 117, 125, 0.1);
      color: #495057;
    }

    .btn-signin:hover {
      background: rgba(108, 117, 125, 0.2);
    }
  </style>
</head>
<body>

  <!-- Animated background blobs -->
  <div class="bg-shape shape1"></div>
  <div class="bg-shape shape2"></div>
  <div class="bg-shape shape3"></div>

  <div class="card">
    <?php if ($success): ?>
      <div class="icon-wrapper success-icon">✓</div>
      <h2>Attendance Recorded!</h2>
      <p class="subtext">Your attendance has been logged successfully.</p>

      <div class="details-box">
        <div class="detail-row">
          <span class="detail-label">Student Name</span>
          <span class="detail-value"><?php echo htmlspecialchars($student_name); ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Student ID</span>
          <span class="detail-value"><?php echo htmlspecialchars($student_number); ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Time Logged</span>
          <span class="detail-value"><?php echo $time_str; ?></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="badge <?php echo ($status === 'Late') ? 'badge-late' : 'badge-ontime'; ?>">
            <?php echo $status; ?>
          </span>
        </div>
      </div>
    <?php else: ?>
      <div class="icon-wrapper error-icon">✕</div>
      <h2>Submission Error</h2>
      <p class="subtext"><?php echo !empty($error_msg) ? htmlspecialchars($error_msg) : 'An unexpected error occurred.'; ?></p>
    <?php endif; ?>

    <a href="view_records.php" class="btn btn-dashboard">👩‍🏫 View Attendance Dashboard</a>
    <a href="index.html" class="btn btn-signin">⬅️ Back to Sign-In</a>
  </div>

</body>
</html>