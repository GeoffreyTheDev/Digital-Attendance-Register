<?php
include 'db_connect.php';

// Fetch attendance records (latest first)
$sql = "SELECT * FROM attendance ORDER BY sign_in_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attendance Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #ede7f6, #e3f2fd, #fce4ec);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    header {
      width: 100%;
      background: linear-gradient(90deg, #7b1fa2, #512da8);
      color: white;
      text-align: center;
      padding: 20px 0;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    h1 {
      margin: 0;
      font-size: 28px;
    }

    .table-container {
      background: white;
      margin-top: 40px;
      width: 90%;
      max-width: 900px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      padding: 14px 10px;
      text-align: center;
      border-bottom: 1px solid #eee;
      font-size: 15px;
    }

    th {
      background-color: #7b1fa2;
      color: white;
      font-weight: bold;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    .late {
      color: red;
      font-weight: bold;
    }

    .ontime {
      color: green;
      font-weight: bold;
    }

    .no-records {
      text-align: center;
      padding: 20px;
      color: #666;
    }

    .back-btn {
      margin: 30px 0;
      background-color: #512da8;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
      text-decoration: none;
    }

    .back-btn:hover {
      background-color: #311b92;
    }

    footer {
      margin-top: auto;
      padding: 15px;
      color: #777;
      font-size: 13px;
    }
  </style>
</head>
<body>
  <header>
    <h1>📋 Attendance Dashboard</h1>
  </header>

  <div class="table-container">
    <table>
      <tr>
        <th>ID</th>
        <th>Student Name</th>
        <th>Student Number</th>
        <th>Sign-In Time</th>
        <th>Status</th>
      </tr>

      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $statusClass = ($row['status'] === 'Late') ? 'late' : 'ontime';
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['student_name']}</td>
                  <td>{$row['student_number']}</td>
                  <td>{$row['sign_in_time']}</td>
                  <td class='{$statusClass}'>{$row['status']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='5' class='no-records'>No attendance records found.</td></tr>";
      }
      ?>
    </table>
  </div>

  <a href="index.php" class="back-btn">← Back to Sign-In</a>

  <footer>
    &copy; <?php echo date("Y"); ?> Digital Attendance Register | WAD621S Project
  </footer>
</body>
</html>
<?php
$conn->close();
?>
