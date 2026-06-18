<?php
/**
 * Lurnixe Health Card System - System Audit Activity Logs
 * June 2026
 */
$page_title = "Activity Logs";
require_once __DIR__ . '/includes/header.php';

// Enforce Super Admin authorization check
require_super_admin();

$logs = [];
try {
    // Fetch logs with associated admin name
    $logs = $pdo->query("SELECT l.*, a.name as admin_name FROM activity_logs l LEFT JOIN admins a ON l.admin_id = a.id ORDER BY l.id DESC LIMIT 1000")->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to query activity logs: " . $e->getMessage());
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">System Activity Logs</h2>
        <p class="text-muted small">Global audit ledger of administrative events and authentication actions.</p>
    </div>
</div>

<div class="table-card bg-white">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle w-100" id="activityLogsTable">
            <thead>
                <tr class="fw-bold text-muted small">
                    <th style="width: 180px;">Timestamp</th>
                    <th>Administrator</th>
                    <th>Action Type</th>
                    <th>Target Member</th>
                    <th>Log Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-muted"><?php echo format_date($log['created_at'], 'd M Y H:i:s'); ?></td>
                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($log['admin_name'] ?? 'System'); ?></td>
                        <td>
                            <?php 
                            $action = $log['action'];
                            $badge_class = 'bg-secondary';
                            if ($action === 'login_success') $badge_class = 'bg-success';
                            elseif ($action === 'login_failed') $badge_class = 'bg-danger';
                            elseif ($action === 'add_member') $badge_class = 'bg-primary';
                            elseif ($action === 'edit_member') $badge_class = 'bg-warning text-dark';
                            elseif ($action === 'delete_member') $badge_class = 'bg-danger';
                            elseif ($action === 'renew_card') $badge_class = 'bg-info text-dark';
                            elseif ($action === 'update_status') $badge_class = 'bg-secondary';
                            
                            echo '<span class="badge ' . $badge_class . ' rounded-pill small">' . strtoupper($action) . '</span>';
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($log['target_member_id'])): ?>
                                <a href="view-member.php?id=<?php echo $log['target_member_id']; ?>" class="font-code text-decoration-none fw-bold"><?php echo htmlspecialchars($log['target_member_id']); ?></a>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="log-detail-box">
                                <?php echo htmlspecialchars($log['details']); ?>
                            </div>
                        </td>
                        <td class="text-muted font-code"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- DataTables initialization -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#activityLogsTable').DataTable({
        order: [[0, 'desc']], // Default sort by Timestamp desc
        pageLength: 50,
        lengthMenu: [25, 50, 100],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search audit logs..."
        }
    });
});
</script>
