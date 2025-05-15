<?php
// Get all notices
$notices = $conn->query("SELECT * FROM notices ORDER BY id DESC");
?>

<div class="container-fluid">
    <div class="row">
        <!-- Add Notice Form -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Notice</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="admin.php?page=notices">
                        <div class="mb-3">
                            <label for="notice" class="form-label">Notice Message</label>
                            <textarea class="form-control" id="notice" name="notice" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notice_by" class="form-label">Posted By</label>
                            <input type="text" class="form-control" id="notice_by" name="notice_by" required>
                        </div>
                        <button type="submit" name="add_notice" class="btn btn-primary w-100">Add Notice</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Notices List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Notices</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Notice</th>
                                    <th>Posted By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($notices->num_rows > 0) {
                                    while ($row = $notices->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['notice']); ?></td>
                                        <td><?php echo htmlspecialchars($row['notice_by']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="admin.php?page=notices&delete=1&table=notices&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this notice?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No notices found</td></tr>';
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