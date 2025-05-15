<?php
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "iot-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query latest sensor data
$sql = "SELECT * FROM DHT22Readings ORDER BY reading_time DESC LIMIT 1";
$result = $conn->query($sql);

// Check if query is valid
if (!$result) {
    die("Query failed: " . $conn->error);
}

$data = array();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = array(
        'temperature' => $row["temperature"],
        'humidity' => $row["humidity"],
        'reading_time' => $row["reading_time"]
    );
} else {
    $data = array('error' => 'No sensor data found');
}

$conn->close();

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
