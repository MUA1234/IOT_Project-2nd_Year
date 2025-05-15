<?php
// Get all authorized RFIDs
$rfids = $conn->query("SELECT * FROM access_granted_rfids ORDER BY id DESC");

// Get recent access logs with status
$accessLogs = $conn->query("SELECT r.*, 
                           CASE WHEN a.rfid IS NOT NULL THEN 'Granted' ELSE 'Denied' END AS status
                           FROM rfid_data r
                           LEFT JOIN access_granted_rfids a ON r.rfid_id = a.rfid
                           ORDER BY r.id DESC LIMIT 20");
?>

<div class="container-fluid">
    <div class="row">
        <!-- Add RFID Form -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Authorized RFID</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="admin.php?page=rfid">
                        <div class="mb-3">
                            <label for="rfid" class="form-label">RFID ID</label>
                            <input type="text" class="form-control" id="rfid" name="rfid" required>
                            <small class="form-text text-muted">Enter the full RFID identifier.</small>
                        </div>
                        <button type="submit" name="add_rfid" class="btn btn-primary w-100">Add RFID</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Authorized RFIDs List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Authorized RFIDs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>RFID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($rfids->num_rows > 0) {
                                    while ($row = $rfids->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['rfid']); ?></td>
                                        <td>
                                            <a href="admin.php?page=rfid&delete=1&table=access_granted_rfids&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this RFID?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<tr><td colspan="3" class="text-center">No authorized RFIDs found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- RFID Access Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent RFID Access Logs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>RFID</th>
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($accessLogs->num_rows > 0) {
                                    while ($row = $accessLogs->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['rfid_id']); ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'Granted'): ?>
                                                <span class="badge bg-success">Granted</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Denied</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<tr><td colspan="4" class="text-center">No access logs found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>