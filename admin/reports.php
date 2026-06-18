<?php
/**
 * Lurnixe Health Card System - Reports and Expiry Analytics
 * June 2026
 */
$page_title = "Reports & Analytics";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce login validation
check_auth();

$admin_role = $_SESSION['admin_role'];

// ----------------------------------------------------
// NATIVE CSV EXPORT HANDLER
// ----------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    // Read parameters
    $status_filter = trim($_GET['status'] ?? '');
    $blood_filter = trim($_GET['blood_group'] ?? '');
    $date_from = trim($_GET['date_from'] ?? '');
    $date_to = trim($_GET['date_to'] ?? '');
    $expiry_days = intval($_GET['expiry_days'] ?? 0);
    
    try {
        $conditions = [];
        $params = [];
        
        if ($expiry_days > 0) {
            // Expiry alert filter (validity_date between today and today + X days)
            $conditions[] = "validity_date >= CURRENT_DATE() AND validity_date <= DATE_ADD(CURRENT_DATE(), INTERVAL ? DAY)";
            $params[] = $expiry_days;
        } else {
            // Standard filters
            if (!empty($status_filter)) {
                $conditions[] = "status = ?";
                $params[] = $status_filter;
            }
            if (!empty($blood_filter)) {
                $conditions[] = "blood_group = ?";
                $params[] = $blood_filter;
            }
            if (!empty($date_from)) {
                $conditions[] = "created_at >= ?";
                $params[] = $date_from . " 00:00:00";
            }
            if (!empty($date_to)) {
                $conditions[] = "created_at <= ?";
                $params[] = $date_to . " 23:59:59";
            }
        }
        
        $where_sql = "";
        if (count($conditions) > 0) {
            $where_sql = " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql = "SELECT m.*, a.name as registered_by_name FROM members m LEFT JOIN admins a ON m.created_by = a.id" . $where_sql . " ORDER BY m.name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();
        
        // Output headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="LurnixeHealth_Export_' . date('Ymd_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Write column headers
        fputcsv($output, ['Member ID', 'Full Name', 'Gender', 'DOB', 'Age', 'Mobile', 'Alt Mobile', 'Email', 'Address', 'City', 'State', 'Pincode', 'Blood Group', 'Allergies', 'Medical Conditions', 'Emergency Name', 'Emergency Mobile', 'Valid Till', 'Status', 'Payment', 'Registered By', 'Registered Date']);
        
        // Write records
        foreach ($records as $row) {
            fputcsv($output, [
                $row['member_id'],
                $row['name'],
                $row['gender'],
                $row['dob'],
                $row['age'],
                $row['mobile'],
                $row['alt_mobile'] ?? 'N/A',
                $row['email'] ?? 'N/A',
                $row['address'],
                $row['city'],
                $row['state'],
                $row['pincode'],
                $row['blood_group'],
                $row['allergies'] ?? 'None',
                $row['health_info'] ?? 'None',
                $row['emergency_name'],
                $row['emergency_mobile'],
                $row['validity_date'],
                strtoupper($row['status']),
                ($row['payment_status'] == 1) ? 'PAID' : 'PENDING',
                $row['registered_by_name'] ?? 'System',
                $row['created_at']
            ]);
        }
        
        fclose($output);
        exit;
        
    } catch (PDOException $e) {
        error_log("Failed to export CSV report: " . $e->getMessage());
        die("Error: Export database query failure.");
    }
}

// ----------------------------------------------------
// UI VIEW LOADER
// ----------------------------------------------------
require_once __DIR__ . '/includes/header.php';

// Default filters
$status_filter = trim($_GET['status'] ?? '');
$blood_filter = trim($_GET['blood_group'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to = trim($_GET['date_to'] ?? '');
$expiry_days = intval($_GET['expiry_days'] ?? 0);

$members = [];
try {
    $conditions = [];
    $params = [];
    
    if ($expiry_days > 0) {
        $conditions[] = "validity_date >= CURRENT_DATE() AND validity_date <= DATE_ADD(CURRENT_DATE(), INTERVAL ? DAY)";
        $params[] = $expiry_days;
    } else {
        if (!empty($status_filter)) {
            $conditions[] = "status = ?";
            $params[] = $status_filter;
        }
        if (!empty($blood_filter)) {
            $conditions[] = "blood_group = ?";
            $params[] = $blood_filter;
        }
        if (!empty($date_from)) {
            $conditions[] = "created_at >= ?";
            $params[] = $date_from . " 00:00:00";
        }
        if (!empty($date_to)) {
            $conditions[] = "created_at <= ?";
            $params[] = $date_to . " 23:59:59";
        }
    }
    
    $where_sql = "";
    if (count($conditions) > 0) {
        $where_sql = " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql = "SELECT m.*, a.name as registered_by_name FROM members m LEFT JOIN admins a ON m.created_by = a.id" . $where_sql . " ORDER BY m.name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $members = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Failed to fetch reports list: " . $e->getMessage());
}
?>

<div class="row mb-4 align-items-center">
    <div class="col-12 col-md-6">
        <h2 class="text-dark fw-bold font-heading">Reports & Analytics</h2>
        <p class="text-muted small">Export system registrations and track card renewal schedules.</p>
    </div>
</div>

<!-- Navigation Tabs for Report Types -->
<ul class="nav nav-pills mb-4" id="reportTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($expiry_days === 0) ? 'active' : ''; ?>" href="reports.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Custom Registrations Report</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($expiry_days === 30) ? 'active' : ''; ?>" href="reports.php?expiry_days=30"><i class="fa-solid fa-hourglass-half me-2"></i> Expiring in 30 Days</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($expiry_days === 60) ? 'active' : ''; ?>" href="reports.php?expiry_days=60"><i class="fa-solid fa-hourglass-end me-2"></i> Expiring in 60 Days</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link rounded-pill px-4 <?php echo ($expiry_days === 90) ? 'active' : ''; ?>" href="reports.php?expiry_days=90"><i class="fa-solid fa-clock me-2"></i> Expiring in 90 Days</a>
    </li>
</ul>

<?php if ($expiry_days === 0): ?>
    <!-- Custom Filter Form -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-white">
        <h5 class="text-dark fw-bold mb-3 small text-uppercase font-heading" style="letter-spacing: 1px;"><i class="fa-solid fa-filter text-success me-2"></i> Query Criteria</h5>
        <form method="GET" action="reports.php" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label small fw-bold">Card Status</label>
                <select class="form-select rounded-2" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="expired" <?php echo $status_filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    <option value="deactivated" <?php echo $status_filter === 'deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="blood_group" class="form-label small fw-bold">Blood Group</label>
                <select class="form-select rounded-2" id="blood_group" name="blood_group">
                    <option value="">All Blood Groups</option>
                    <?php 
                    $bg_groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                    foreach ($bg_groups as $bg) {
                        $selected = ($blood_filter === $bg) ? 'selected' : '';
                        echo "<option value=\"$bg\" $selected>$bg</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label small fw-bold">Registration Date Range</label>
                <div class="input-group">
                    <input type="date" class="form-control rounded-start-2" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                    <span class="input-group-text bg-light">to</span>
                    <input type="date" class="form-control rounded-end-2" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                    <button class="btn btn-primary px-4" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
                </div>
            </div>
        </form>
    </div>
<?php else: ?>
    <!-- Expiry Alert Banner -->
    <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4" role="alert">
        <div class="fs-2 text-warning"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <h5 class="alert-heading fw-bold mb-0">Expiry Action Alert</h5>
            <span class="small text-muted">Showing all family health card members whose validity will lapse within the next <strong><?php echo $expiry_days; ?> days</strong>. Please coordinate offline renewals.</span>
        </div>
    </div>
<?php endif; ?>

<!-- Results & Exports panel -->
<div class="table-card bg-white">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 g-2">
        <span class="small fw-bold text-muted"><?php echo count($members); ?> Record(s) Found</span>
        
        <?php if (!empty($members)): ?>
            <a href="reports.php?action=export_csv&status=<?php echo $status_filter; ?>&blood_group=<?php echo $blood_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>&expiry_days=<?php echo $expiry_days; ?>" class="btn btn-success btn-sm rounded-pill px-4">
                <i class="fa-solid fa-file-csv me-1"></i> Download CSV Spreadsheet
            </a>
        <?php endif; ?>
    </div>
    
    <div class="table-responsive small">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr class="fw-bold text-muted">
                    <th>Name</th>
                    <th>Member ID</th>
                    <th>Mobile</th>
                    <th>Blood Group</th>
                    <th>Valid Till</th>
                    <th>Status</th>
                    <th>Registered By</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No matching records found. Try adjusting filter criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php 
                                    $photo = $member['photo'];
                                    if (empty($photo) || !file_exists(__DIR__ . '/../' . $photo)) {
                                        echo '<div class="avatar-table-img bg-light text-muted d-flex align-items-center justify-content-center border" style="width:30px; height:30px;"><i class="fa-solid fa-user" style="font-size:0.75rem;"></i></div>';
                                    } else {
                                        echo '<img src="' . BASE_URL . $photo . '" alt="" class="avatar-table-img" style="width:30px; height:30px;">';
                                    }
                                    ?>
                                    <span class="text-dark fw-bold"><?php echo htmlspecialchars($member['name']); ?></span>
                                </div>
                            </td>
                            <td><span class="font-code"><?php echo htmlspecialchars($member['member_id']); ?></span></td>
                            <td><?php echo htmlspecialchars($member['mobile']); ?></td>
                            <td><span class="badge bg-light-blue text-primary px-3 py-1 font-heading fw-bold"><?php echo htmlspecialchars($member['blood_group']); ?></span></td>
                            <td class="fw-bold <?php echo ($expiry_days > 0) ? 'text-danger' : ''; ?>"><?php echo format_date($member['validity_date']); ?></td>
                            <td>
                                <?php 
                                $status = strtolower($member['status']);
                                $badge_class = 'bg-secondary';
                                if ($status === 'active') $badge_class = 'badge-active';
                                elseif ($status === 'expired') $badge_class = 'badge-expired';
                                elseif ($status === 'suspended') $badge_class = 'badge-suspended';
                                elseif ($status === 'deactivated') $badge_class = 'badge-deactivated';
                                
                                echo '<span class="badge ' . $badge_class . ' rounded-pill">' . strtoupper($status) . '</span>';
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($member['registered_by_name'] ?? 'System'); ?></td>
                            <td class="text-end">
                                <a href="view-member.php?id=<?php echo $member['member_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fa-solid fa-eye me-1"></i> Profile
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
