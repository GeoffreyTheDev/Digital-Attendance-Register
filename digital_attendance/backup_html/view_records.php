<?php
include 'db_connect.php';

// Fetch all attendance records
$sql = "SELECT * FROM attendance ORDER BY sign_in_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    h2 {
      text-align: center;
      margin-top: 20px;
      color: #333;
    }
    table {
      border-collapse: collapse;
      width: 90%;
      margin: 20px auto;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .late {
      color: red;
      font-weight: bold;
    }
    .ontime {
      color: green;
      font-weight: bold;
    }
    .back-link {
      display: block;
      width: fit-content;
      margin: 20px auto;
      text-decoration: none;
      color: #007bff;
      font-weight: bold;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h2>Attendance Records</h2>

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
      while($row = $result->fetch_assoc()) {
        $statusClass = ($row['status'] == "Late") ? "late" : "ontime";
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['student_name'] . "</td>";
        echo "<td>" . $row['student_number'] . "</td>";
        echo "<td>" . $row['sign_in_time'] . "</td>";
        echo "<td class='" . $statusClass . "'>" . $row['status'] . "</td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='5'>No attendance records found.</td></tr>";
    }
    ?>

  </table>

  <a class="back-link" href="index.html">← Back to Sign-In</a>
</body>
</html>
<?php
$conn->close();
?>
