<?php
/**
 * Lurnixe Health Card System - Admin Dashboard
 * June 2026
 */
$page_title = "Dashboard";
require_once __DIR__ . '/includes/header.php';

// Check if card validity needs to be auto-expired (cron-like check on dashboard load)
try {
    $pdo->query("UPDATE members SET status = 'expired' WHERE validity_date < CURRENT_DATE() AND status = 'active'");
} catch (PDOException $e) {
    error_log("Failed to auto-expire members: " . $e->getMessage());
}

// 1. Fetch KPI metrics
$kpis = [
    'total_members' => 0,
    'active_cards' => 0,
    'expired_cards' => 0,
    'suspended_cards' => 0,
    'renewals_this_month' => 0
];

try {
    // Total members
    $kpis['total_members'] = (int)$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
    
    // Active cards
    $kpis['active_cards'] = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn();
    
    // Expired cards
    $kpis['expired_cards'] = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status = 'expired'")->fetchColumn();
    
    // Suspended cards
    $kpis['suspended_cards'] = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status = 'suspended'")->fetchColumn();
    
    // Renewals this month
    $kpis['renewals_this_month'] = (int)$pdo->query("SELECT COUNT(*) FROM renewals WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
    
    // Recent Registrations (last 5)
    $stmt = $pdo->query("SELECT * FROM members ORDER BY id DESC LIMIT 5");
    $recent_members = $stmt->fetchAll();
    
    // 2. Fetch Chart Data: Members Growth (Line Chart)
    $growth_data = array_fill(1, 12, 0); // 1 to 12 representing Jan to Dec
    $growth_query = $pdo->query("SELECT MONTH(created_at) as m, COUNT(*) as c FROM members WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) GROUP BY MONTH(created_at)");
    while ($row = $growth_query->fetch()) {
        $growth_data[(int)$row['m']] = (int)$row['c'];
    }
    
    // Fetch Chart Data: Blood Group (Pie Chart)
    $blood_groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
    $blood_data = array_fill_keys($blood_groups, 0);
    $blood_query = $pdo->query("SELECT blood_group, COUNT(*) as c FROM members GROUP BY blood_group");
    while ($row = $blood_query->fetch()) {
        if (array_key_exists($row['blood_group'], $blood_data)) {
            $blood_data[$row['blood_group']] = (int)$row['c'];
        }
    }
    
    // Fetch Chart Data: Gender (Doughnut Chart)
    $gender_data = ['Male' => 0, 'Female' => 0, 'Other' => 0];
    $gender_query = $pdo->query("SELECT gender, COUNT(*) as c FROM members GROUP BY gender");
    while ($row = $gender_query->fetch()) {
        if (array_key_exists($row['gender'], $gender_data)) {
            $gender_data[$row['gender']] = (int)$row['c'];
        }
    }

    // Fetch Chart Data: Status Overview (Bar Chart)
    $status_data = ['active' => 0, 'expired' => 0, 'suspended' => 0, 'deactivated' => 0];
    $status_query = $pdo->query("SELECT status, COUNT(*) as c FROM members GROUP BY status");
    while ($row = $status_query->fetch()) {
        if (array_key_exists($row['status'], $status_data)) {
            $status_data[$row['status']] = (int)$row['c'];
        }
    }
    
} catch (PDOException $e) {
    error_log("Dashboard query failed: " . $e->getMessage());
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">Dashboard</h2>
        <p class="text-muted small">Summary metrics and quick analytics dashboard.</p>
    </div>
</div>

<!-- KPI Cards Row -->
<div class="row g-3 mb-4">
    <!-- Total Members -->
    <div class="col-xl-3 col-sm-6">
        <div class="card kpi-card p-3 h-100" style="border-left-color: var(--primary-blue)">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block small fw-bold font-heading">TOTAL MEMBERS</span>
                    <h3 class="text-dark fw-bold mb-0 mt-1"><?php echo $kpis['total_members']; ?></h3>
                </div>
                <div class="kpi-icon-box bg-light-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <a href="members.php" class="text-primary small mt-3 text-decoration-none">View member list <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>
    
    <!-- Active Cards -->
    <div class="col-xl-3 col-sm-6">
        <div class="card kpi-card p-3 h-100" style="border-left-color: var(--primary-green)">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block small fw-bold font-heading">ACTIVE CARDS</span>
                    <h3 class="text-success fw-bold mb-0 mt-1"><?php echo $kpis['active_cards']; ?></h3>
                </div>
                <div class="kpi-icon-box bg-light-green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <a href="members.php?status=active" class="text-success small mt-3 text-decoration-none">View active list <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>
    
    <!-- Expired Cards -->
    <div class="col-xl-2 col-sm-4">
        <div class="card kpi-card p-3 h-100" style="border-left-color: var(--warning-orange)">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block small fw-bold font-heading">EXPIRED CARDS</span>
                    <h3 class="text-warning fw-bold mb-0 mt-1"><?php echo $kpis['expired_cards']; ?></h3>
                </div>
                <div class="kpi-icon-box bg-light-warning">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
            <a href="members.php?status=expired" class="text-warning small mt-3 text-decoration-none">Renew cards <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>
    
    <!-- Suspended Cards -->
    <div class="col-xl-2 col-sm-4">
        <div class="card kpi-card p-3 h-100" style="border-left-color: var(--warning-yellow)">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block small fw-bold font-heading">SUSPENDED</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: #b78a02 !important;"><?php echo $kpis['suspended_cards']; ?></h3>
                </div>
                <div class="kpi-icon-box" style="background-color: rgba(241, 196, 15, 0.08); color: #b78a02 !important;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <a href="members.php?status=suspended" class="small mt-3 text-decoration-none" style="color: #b78a02 !important;">Resolve status <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>
    
    <!-- Renewals -->
    <div class="col-xl-2 col-sm-4">
        <div class="card kpi-card p-3 h-100" style="border-left-color: #3498db">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block small fw-bold font-heading">RENEWED (MONTH)</span>
                    <h3 class="text-dark fw-bold mb-0 mt-1"><?php echo $kpis['renewals_this_month']; ?></h3>
                </div>
                <div class="kpi-icon-box" style="background-color: rgba(52, 152, 219, 0.08); color: #3498db !important;">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
            </div>
            <a href="reports.php" class="small mt-3 text-decoration-none" style="color: #3498db !important;">View reports <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Registration Growth Line Chart -->
    <div class="col-lg-8">
        <div class="table-card h-100">
            <h5 class="text-dark fw-bold font-heading mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i> Members Registration Growth (<?php echo date('Y'); ?>)</h5>
            <div style="height: 300px; position: relative;">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Gender Distribution Doughnut -->
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="text-dark fw-bold font-heading mb-3"><i class="fa-solid fa-venus-mars text-success me-2"></i> Gender Distribution</h5>
            <div style="height: 300px; position: relative;">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Blood Group Distribution Pie -->
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="text-dark fw-bold font-heading mb-3"><i class="fa-solid fa-droplet text-danger me-2"></i> Members by Blood Group</h5>
            <div style="height: 250px; position: relative;">
                <canvas id="bloodChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Card Status Overview Bar -->
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="text-dark fw-bold font-heading mb-3"><i class="fa-solid fa-id-card text-success me-2"></i> Card Status Overview</h5>
            <div style="height: 250px; position: relative;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Registrations (Last 5) -->
    <div class="col-lg-4">
        <div class="table-card h-100">
            <h5 class="text-dark fw-bold font-heading mb-3"><i class="fa-solid fa-clock text-primary me-2"></i> Recent Registrations</h5>
            <div class="table-responsive small">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Member ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_members)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_members as $member): ?>
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
                                            <a href="view-member.php?id=<?php echo $member['member_id']; ?>" class="text-dark fw-semibold text-decoration-none"><?php echo htmlspecialchars($member['name']); ?></a>
                                        </div>
                                    </td>
                                    <td><span class="font-code"><?php echo htmlspecialchars($member['member_id']); ?></span></td>
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
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- ChartJS initialization scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Growth Chart (Line)
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'New Registrations',
                data: <?php echo json_encode(array_values($growth_data)); ?>,
                borderColor: '#1A5276',
                backgroundColor: 'rgba(26, 82, 118, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Gender Chart (Doughnut)
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Male', 'Female', 'Other'],
            datasets: [{
                data: <?php echo json_encode(array_values($gender_data)); ?>,
                backgroundColor: ['#1A5276', '#27AE60', '#F1C40F'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. Blood Group Chart (Pie)
    const bloodCtx = document.getElementById('bloodChart').getContext('2d');
    new Chart(bloodCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($blood_data)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($blood_data)); ?>,
                backgroundColor: ['#E74C3C', '#F1948A', '#9B59B6', '#C39BD3', '#3498DB', '#85C1E9', '#1ABC9C', '#76D7C4'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // 4. Status Chart (Bar)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Expired', 'Suspended', 'Deactivated'],
            datasets: [{
                label: 'Card Count',
                data: <?php echo json_encode(array_values($status_data)); ?>,
                backgroundColor: ['#27AE60', '#E67E22', '#F1C40F', '#E74C3C'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});
</script>
