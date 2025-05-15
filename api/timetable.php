<?php
// Database credentials
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "iot-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get today's date
$today = date('Y-m-d');

// Fetch the timetable data for today
$sql = "SELECT * FROM timetable WHERE today = '$today' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output the timetable data as JSON
    $row = $result->fetch_assoc();
    echo json_encode([
        'sub1' => $row['sub1'],
        'sub2' => $row['sub2'],
        'sub3' => $row['sub3'],
        'sub4' => $row['sub4'],
        'sub5' => $row['sub5'],
        'sub6' => $row['sub6'],
        'sub7' => $row['sub7'],
        'sub8' => $row['sub8']
    ]);
} else {
    echo json_encode(['error' => 'No timetable found for today']);
}

// Close the connection
$conn->close();
?>
