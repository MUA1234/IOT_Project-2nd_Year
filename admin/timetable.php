<?php
// Get all timetable entries
$timetable = $conn->query("SELECT * FROM timetable ORDER BY today ASC");

// Get specific timetable entry for editing if edit parameter is set
$editEntry = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = $_GET['edit'];
    $result = $conn->query("SELECT * FROM timetable WHERE id = $editId");
    if ($result->num_rows > 0) {
        $editEntry = $result->fetch_assoc();
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Add/Edit Timetable Form -->
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php echo $editEntry ? 'Edit Timetable Entry' : 'Add New Timetable Entry'; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form method="post" action="admin.php?page=timetable<?php echo $editEntry ? '&edit=' . $editEntry['id'] : ''; ?>">
                        <?php if ($editEntry): ?>
                            <input type="hidden" name="id" value="<?php echo $editEntry['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="today" class="form-label">Date</label>
                            <input type="date" class="form-control" id="today" name="today" 
                                   value="<?php echo $editEntry ? $editEntry['today'] : date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sub1" class="form-label">Subject 1</label>
                                <input type="text" class="form-control" id="sub1" name="sub1" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub1']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub2" class="form-label">Subject 2</label>
                                <input type="text" class="form-control" id="sub2" name="sub2" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub2']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sub3" class="form-label">Subject 3</label>
                                <input type="text" class="form-control" id="sub3" name="sub3" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub3']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub4" class="form-label">Subject 4</label>
                                <input type="text" class="form-control" id="sub4" name="sub4" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub4']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sub5" class="form-label">Subject 5</label>
                                <input type="text" class="form-control" id="sub5" name="sub5" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub5']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub6" class="form-label">Subject 6</label>
                                <input type="text" class="form-control" id="sub6" name="sub6" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub6']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sub7" class="form-label">Subject 7</label>
                                <input type="text" class="form-control" id="sub7" name="sub7" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub7']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sub8" class="form-label">Subject 8</label>
                                <input type="text" class="form-control" id="sub8" name="sub8" 
                                       value="<?php echo $editEntry ? htmlspecialchars($editEntry['sub8']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="<?php echo $editEntry ? 'update_timetable' : 'add_timetable'; ?>" class="btn btn-primary w-100">
                            <?php echo $editEntry ? 'Update Timetable' : 'Add Timetable'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Timetable List -->
        <div class="col-xl-7 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Timetable Entries</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Subjects</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($timetable->num_rows > 0) {
                                    while ($row = $timetable->fetch_assoc()): 
                                        $date = date('Y-m-d', strtotime($row['today']));
                                        $dayName = date('l', strtotime($row['today']));
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <?php echo $date; ?><br>
                                            <small class="text-muted"><?php echo $dayName; ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" type="button" 
                                                    data-bs-toggle="collapse" data-bs-target="#subjects<?php echo $row['id']; ?>" 
                                                    aria-expanded="false" aria-controls="subjects<?php echo $row['id']; ?>">
                                                View Subjects
                                            </button>
                                            <div class="collapse mt-2" id="subjects<?php echo $row['id']; ?>">
                                                <div class="card card-body">
                                                    <ol class="mb-0 ps-3">
                                                        <li><?php echo htmlspecialchars($row['sub1']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub2']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub3']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub4']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub5']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub6']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub7']); ?></li>
                                                        <li><?php echo htmlspecialchars($row['sub8']); ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="admin.php?page=timetable&edit=<?php echo $row['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="admin.php?page=timetable&delete=1&table=timetable&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this timetable entry?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo '<tr><td colspan="4" class="text-center">No timetable entries found</td></tr>';
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