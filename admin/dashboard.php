<?php
// Get latest sensor data
$latestDHT = $conn->query("SELECT * FROM DHT22Readings ORDER BY id DESC LIMIT 1")->fetch_assoc();

// Get counts for dashboard widgets
$rfidCount = $conn->query("SELECT COUNT(*) as count FROM access_granted_rfids")->fetch_assoc()['count'];
$noticeCount = $conn->query("SELECT COUNT(*) as count FROM notices")->fetch_assoc()['count'];
$accessCount = $conn->query("SELECT COUNT(*) as count FROM rfid_data")->fetch_assoc()['count'];
$timetableCount = $conn->query("SELECT COUNT(*) as count FROM timetable")->fetch_assoc()['count'];

// Get recent access data
$recentAccess = $conn->query("SELECT * FROM rfid_data ORDER BY id DESC LIMIT 5");

// Get recent notice
$latestNotice = $conn->query("SELECT * FROM notices ORDER BY id DESC LIMIT 1")->fetch_assoc();

// Get sensor data for charts
$sensorData = $conn->query("SELECT temperature, humidity, reading_time FROM DHT22Readings ORDER BY id DESC LIMIT 10");
$chartData = [];
$labels = [];
$tempData = [];
$humData = [];

while ($row = $sensorData->fetch_assoc()) {
    $labels[] = date('H:i', strtotime($row['reading_time']));
    $tempData[] = $row['temperature'];
    $humData[] = $row['humidity'];
}

// Reverse arrays to show chronological order
$labels = array_reverse($labels);
$tempData = array_reverse($tempData);
$humData = array_reverse($humData);

// Convert to JSON for chart.js
$chartLabels = json_encode($labels);
$chartTempData = json_encode($tempData);
$chartHumData = json_encode($humData);
?>

<div class="container-fluid">
    <!-- Dashboard Widgets -->
    <div class="row mb-4">
        <!-- Current Temperature Widget -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Current Temperature</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($latestDHT['temperature']) ? $latestDHT['temperature'] . ' °C' : 'N/A'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Humidity Widget -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Current Humidity</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($latestDHT['humidity']) ? $latestDHT['humidity'] . ' %' : 'N/A'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tint fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authorized RFIDs Widget -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Authorized RFIDs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $rfidCount; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Notices Widget -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Notices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $noticeCount; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sensor Charts Row -->
    <div class="row mb-4">
        <!-- Temperature Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Temperature Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="temperatureChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Humidity Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Humidity Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="humidityChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row">
        <!-- Latest Notice Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Latest Notice</h6>
                </div>
                <div class="card-body">
                    <?php if ($latestNotice): ?>
                        <div class="text-center mb-2">
                            <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($latestNotice['notice']); ?></div>
                            <small class="text-muted">By: <?php echo htmlspecialchars($latestNotice['notice_by']); ?></small>
                            <br>
                            <small class="text-muted">Posted: <?php echo date('F j, Y, g:i a', strtotime($latestNotice['created_at'])); ?></small>
                        </div>
                        <a href="?page=notices" class="btn btn-info btn-sm w-100">Manage Notices</a>
                    <?php else: ?>
                        <p class="text-center">No notices available.</p>
                        <a href="?page=notices" class="btn btn-primary btn-sm w-100">Add Notice</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent RFID Access Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent RFID Access</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>RFID</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($recentAccess->num_rows > 0) {
                                    while ($row = $recentAccess->fetch_assoc()): 
                                        // Check if the RFID is in the authorized list
                                        $rfid_id = $row['rfid_id'];
                                        $checkAuth = $conn->query("SELECT * FROM access_granted_rfids WHERE rfid = '$rfid_id'");
                                        $isAuthorized = $checkAuth->num_rows > 0;
                                ?>
                                    <tr>
                                        <td><?php echo substr(htmlspecialchars($row['rfid_id']), 0, 10) . '...'; ?></td>
                                        <td><?php echo date('H:i:s', strtotime($row['timestamp'])); ?></td>
                                        <td>
                                            <?php if ($isAuthorized): ?>
                                                <span class="badge bg-success">Granted</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Denied</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<tr><td colspan="3" class="text-center">No recent access data</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="?page=rfid" class="btn btn-info btn-sm w-100 mt-2">Manage RFID Access</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Temperature Chart
    const tempCtx = document.getElementById('temperatureChart').getContext('2d');
    const tempChart = new Chart(tempCtx, {
        type: 'line',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                label: 'Temperature (°C)',
                data: <?php echo $chartTempData; ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    
    // Humidity Chart
    const humCtx = document.getElementById('humidityChart').getContext('2d');
    const humChart = new Chart(humCtx, {
        type: 'line',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                label: 'Humidity (%)',
                data: <?php echo $chartHumData; ?>,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
});
</script>
