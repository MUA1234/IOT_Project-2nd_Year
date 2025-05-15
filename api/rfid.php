<?php
// Database connection
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "iot-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get latest entry from rfid_data
    $stmt = $pdo->query("SELECT * FROM rfid_data ORDER BY id DESC LIMIT 1");
    $latest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$latest) {
        echo json_encode(["status" => "error", "message" => "No RFID data found"]);
        exit;
    }

    $id = $latest['id'];
    $rfid = $latest['rfid_id'];
    $ck = $latest['ck'];

    if ($ck == 1) {
        echo json_encode(["status" => "Waiting"]);
        exit;
    }

    // Check if RFID exists in access_granted_rfids
    $check = $pdo->prepare("SELECT * FROM access_granted_rfids WHERE rfid = ?");
    $check->execute([$rfid]);

    $access = $check->fetch(PDO::FETCH_ASSOC);
    $accessStatus = $access ? "Granted" : "Denied";

    // Update ck to 1
    $update = $pdo->prepare("UPDATE rfid_data SET ck = 1 WHERE id = ?");
    $update->execute([$id]);

    echo json_encode(["status" => $accessStatus]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
