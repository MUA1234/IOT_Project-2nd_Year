<?php
header('Content-Type: application/json');

// DB credentials
$db_host = "mysql-iot-admin.alwaysdata.net";
$db_user = "iot-admin";
$db_password = "IOT-admin1234#@";
$db_name = "iot-admin_db";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch latest notice
    $stmt = $pdo->query("SELECT notice, notice_by FROM notices ORDER BY created_at DESC LIMIT 1");
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'notice' => $notice['notice'] ?? null,
        'noticeBy' => $notice['notice_by'] ?? null
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
