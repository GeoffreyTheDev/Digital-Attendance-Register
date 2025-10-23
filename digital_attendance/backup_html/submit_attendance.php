<?php
// Include database connection
include 'db_connect.php';

// Get form data
$student_name = $_POST['student_name'];
$student_number = $_POST['student_number'];
$sign_in_time = $_POST['sign_in_time'];
$status = $_POST['status'];

// Insert into the database
$sql = "INSERT INTO attendance (student_name, student_number, sign_in_time, status)
        VALUES ('$student_name', '$student_number', '$sign_in_time', '$status')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Attendance recorded successfully!</h2>";
    echo "<p><a href='index.html'>Go back</a></p>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
