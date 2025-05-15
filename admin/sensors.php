<?php
// Get sensor readings with pagination
$page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

// Get total count for pagination
$totalCount = $conn->query("SELECT COUNT(*) as count FROM DHT22Readings")->fetch_assoc()['count'];
$totalPages = ceil($totalCount / $per_page);

// Get sensor readings for current page
$sensorData = $conn->query("SELECT * FROM DHT22Readings ORDER BY id DESC LIMIT $offset, $per_page");

// Get date range for filtering
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Apply date filter if provided
if (!empty($startDate) && !empty($endDate)) {
    $sensorData = $conn->query("SELECT * FROM DHT22Readings 
                               WHERE reading_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' 
                               ORDER BY id DESC LIMIT $offset, $per_page");
    
    // Update count for pagination with filter
    $totalCount = $conn->query("SELECT COUNT(*) as count FROM DHT22Readings 
                               WHERE reading_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'")->fetch_assoc()['count'];
    $totalPages = ceil($totalCount / $per_page);
}

// Get statistics
$stats = $conn->query("SELECT 
                      AVG(temperature) as avg_temp, 
                      MAX(temperature) as max_temp, 
                      MIN(temperature) as min_temp,
                      AVG(humidity) as avg_hum,
                      MAX(humidity) as max_hum,
                      MIN(humidity) as min_hum
                      FROM DHT22Readings")->fetch_assoc();

// Apply date filter to statistics if provided
if (!empty($startDate) && !empty($endDate)) {
    $stats = $conn->query("SELECT 
                         AVG(temperature) as avg_temp, 
                         MAX(temperature) as max_temp, 
                         MIN(temperature) as min_temp,
                         AVG(humidity) as avg_hum,
                         MAX(humidity) as max_hum,
                         MIN(humidity) as min_hum
                         FROM DHT22Readings
                         WHERE reading_time BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'")->fetch_assoc();
}
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Temperature Statistics</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Avg: <?php echo number_format($stats['avg_temp'], 1); ?> °C<br>
                                Min: <?php echo number_format($stats['min_temp'], 1); ?> °C<br>
                                Max: <?php echo number_format($stats['max_temp'], 1); ?> °C
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-thermometer-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Humidity Statistics</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                Avg: <?php echo number_format($stats['avg_hum'], 1); ?> %<br>
                                Min: <?php echo number_format($stats['min_hum'], 1); ?> %<br>
                                Max: <?php echo number_format($stats['max_hum'], 1); ?> %
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tint fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Data Filtering</div>
                            <form method="get" action="admin.php">
                                <input type="hidden" name="page" value="sensors">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="date" class="form-control form-control-sm" name="start_date" value="<?php echo $startDate; ?>" placeholder="Start Date">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="date" class="form-control form-control-sm" name="end_date" value="<?php echo $endDate; ?>" placeholder="End Date">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-filter"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sensor Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">DHT22 Sensor Readings</h6>
            <?php if (!empty($startDate) && !empty($endDate)): ?>
                <span class="badge bg-info">
                    Filtered: <?php echo $startDate; ?> to <?php echo $endDate; ?>
                    <a href="admin.php?page=sensors" class="text-white ms-2"><i class="fas fa-times"></i></a>
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Temperature (°C)</th>
                            <th>Humidity (%)</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($sensorData->num_rows > 0) {
                            while ($row = $sensorData->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['temperature']; ?></td>
                                <td><?php echo $row['humidity']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($row['reading_time'])); ?></td>
                            </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No sensor data found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page_num > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="admin.php?page=sensors&page_num=<?php echo $page_num - 1; ?><?php echo !empty($startDate) && !empty($endDate) ? '&start_date=' . $startDate . '&end_date=' . $endDate : ''; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php 
                        $startPage = max(1, $page_num - 2);
                        $endPage = min($totalPages, $page_num + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <li class="page-item <?php echo $i === $page_num ? 'active' : ''; ?>">
                                <a class="page-link" href="admin.php?page=sensors&page_num=<?php echo $i; ?><?php echo !empty($startDate) && !empty($endDate) ? '&start_date=' . $startDate . '&end_date=' . $endDate : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page_num < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="admin.php?page=sensors&page_num=<?php echo $page_num + 1; ?><?php echo !empty($startDate) && !empty($endDate) ? '&start_date=' . $startDate . '&end_date=' . $endDate : ''; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Temperature & Humidity Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Temperature & Humidity Trends</h6>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 400px;">
                <canvas id="sensorChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get last 24 hours of sensor data for the chart
    <?php 
    $chartData = $conn->query("SELECT temperature, humidity, reading_time FROM DHT22Readings 
                              ORDER BY reading_time DESC LIMIT 24");
    
    $labels = [];
    $tempData = [];
    $humData = [];
    
    while ($row = $chartData->fetch_assoc()) {
        $labels[] = date('H:i', strtotime($row['reading_time']));
        $tempData[] = $row['temperature'];
        $humData[] = $row['humidity'];
    }
    
    // Reverse arrays to show chronological order
    $labels = array_reverse($labels);
    $tempData = array_reverse($tempData);
    $humData = array_reverse($humData);
    ?>
    
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const sensorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: <?php echo json_encode($tempData); ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    yAxisID: 'y',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Humidity (%)',
                    data: <?php echo json_encode($humData); ?>,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            stacked: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Temperature & Humidity Trends (Last 24 Readings)'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Temperature (°C)'
                    },
                    min: <?php echo min($tempData) - 5; ?>,
                    max: <?php echo max($tempData) + 5; ?>
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Humidity (%)'
                    },
                    min: <?php echo min($humData) - 5; ?>,
                    max: <?php echo max($humData) + 5; ?>,
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
</script>