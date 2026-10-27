<?php
include('db_connect.php');

// Handle Clear All Records request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['clear_records'])) {
    $clear_sql = "TRUNCATE TABLE attendance";
    if ($conn->query($clear_sql) === TRUE) {
        header("Location: view_records.php?cleared=success");
        exit();
    }
}

// Fetch attendance records from database ordered by latest first
$sql = "SELECT * FROM attendance ORDER BY id DESC";
$result = $conn->query($sql);

// Calculate summary stats
$total_count = 0;
$ontime_count = 0;
$late_count = 0;

$records = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
        $total_count++;
        if (isset($row['status']) && strtolower($row['status']) === 'late') {
            $late_count++;
        } else {
            $ontime_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #e1bee7, #bbdefb, #f8bbd0);
      padding: 30px 20px;
    }

    .dashboard-container {
      background: #ffffff;
      padding: 35px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
      width: 100%;
      max-width: 900px;
      text-align: center;
    }

    .header {
      margin-bottom: 25px;
    }

    .header h2 {
      color: #4a148c;
      font-size: 28px;
      font-weight: 700;
    }

    /* Alert Banner */
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 600;
      border: 1px solid #c3e6cb;
    }

    /* Summary Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 15px;
      margin-bottom: 25px;
    }

    .stat-card {
      padding: 16px;
      border-radius: 14px;
      color: #fff;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease;
    }

    .stat-card:hover { transform: translateY(-3px); }
    .stat-card.total { background: linear-gradient(135deg, #7b1fa2, #512da8); }
    .stat-card.ontime { background: linear-gradient(135deg, #4caf50, #2e7d32); }
    .stat-card.late { background: linear-gradient(135deg, #ef5350, #c62828); }

    .stat-card h3 { font-size: 26px; font-weight: 700; }
    .stat-card p {
      font-size: 12px;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-top: 4px;
    }

    /* Table Wrapper */
    .table-wrapper {
      overflow-x: auto;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      text-align: left;
    }

    thead {
      background-color: #7b1fa2;
      color: #ffffff;
    }

    th, td { padding: 14px 18px; font-size: 14px; }
    th { font-weight: 600; letter-spacing: 0.5px; }

    tbody tr {
      border-bottom: 1px solid #f0f0f0;
      transition: background 0.2s ease;
    }

    tbody tr:nth-child(even) { background-color: #faf5ff; }
    tbody tr:hover { background-color: #f3e5f5; }

    /* Badges */
    .badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .badge-late {
      background-color: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
    }

    .badge-ontime {
      background-color: #e8f5e9;
      color: #2e7d32;
      border: 1px solid #c8e6c9;
    }

    .no-data {
      text-align: center;
      padding: 30px;
      color: #777;
      font-style: italic;
    }

    /* Action Buttons */
    .btn-container {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-block;
      padding: 12px 24px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background-color: #7b1fa2;
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(123, 31, 162, 0.25);
    }

    .btn-primary:hover {
      background-color: #4a148c;
      transform: translateY(-2px);
    }

    .btn-danger {
      background-color: #d32f2f;
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(211, 47, 47, 0.25);
    }

    .btn-danger:hover {
      background-color: #b71c1c;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <div class="dashboard-container">
    <div class="header">
      <h2>📋 Attendance Dashboard</h2>
    </div>

    <?php if (isset($_GET['cleared'])): ?>
      <div class="alert-success">
        🗑️ All attendance records have been successfully cleared!
      </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="stats-grid">
      <div class="stat-card total">
        <h3><?php echo $total_count; ?></h3>
        <p>Total Records</p>
      </div>
      <div class="stat-card ontime">
        <h3><?php echo $ontime_count; ?></h3>
        <p>On Time</p>
      </div>
      <div class="stat-card late">
        <h3><?php echo $late_count; ?></h3>
        <p>Late Arrivals</p>
      </div>
    </div>

    <!-- Data Table -->
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Student Number</th>
            <th>Sign-In Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $row): ?>
              <tr>
                <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                <td><?php echo date('Y-m-d H:i:s', strtotime($row['sign_in_time'])); ?></td>
                <td>
                  <?php 
                    $isLate = (strtolower($row['status']) === 'late');
                    $statusClass = $isLate ? 'badge-late' : 'badge-ontime';
                  ?>
                  <span class="badge <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="no-data">No attendance records found in the database.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="btn-container">
      <a href="index.html" class="btn btn-primary">⬅️ Back to Sign-In Form</a>
      
      <?php if (!empty($records)): ?>
        <form method="POST" onsubmit="return confirm('⚠️ Are you sure you want to delete ALL attendance records? This action cannot be undone.');" style="display:inline;">
          <button type="submit" name="clear_records" class="btn btn-danger">🗑️ Clear All Records</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>