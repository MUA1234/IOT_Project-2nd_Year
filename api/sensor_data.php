<?php
// Database connection
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "IOT-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the most recent sensor data for charting
$sensorQuery = "SELECT temperature, humidity, reading_time FROM DHT22Readings ORDER BY reading_time DESC LIMIT 50";
$result = $conn->query($sensorQuery);

$sensorData = [];
while ($row = $result->fetch_assoc()) {
    $row['reading_time'] = date('Y-m-d H:i:s', strtotime($row['reading_time']));
    $sensorData[] = $row;
}

// Reverse the array to get chronological order
$sensorData = array_reverse($sensorData);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($sensorData);

// Close connection
$conn->close();
?>
