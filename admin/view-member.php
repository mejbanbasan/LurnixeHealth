<?php
/**
 * Lurnixe Health Card System - View Member Profile
 * June 2026
 */
$page_title = "Member Profile";
require_once __DIR__ . '/includes/header.php';

$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    header("Location: " . BASE_URL . "admin/members.php?error=notfound");
    exit;
}

try {
    // 1. Fetch Member record
    $stmt = $pdo->prepare("SELECT m.*, a.name as registered_by_name FROM members m LEFT JOIN admins a ON m.created_by = a.id WHERE m.member_id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();
    
    if (!$member) {
        header("Location: " . BASE_URL . "admin/members.php?error=notfound");
        exit;
    }
    
    // 2. Fetch Family members list
    $fam_stmt = $pdo->prepare("SELECT * FROM family_members WHERE member_id = ? ORDER BY id ASC");
    $fam_stmt->execute([$member_id]);
    $family_members = $fam_stmt->fetchAll();
    
    // 3. Fetch Activity logs for this member
    $log_stmt = $pdo->prepare("SELECT l.*, a.name as admin_name FROM activity_logs l LEFT JOIN admins a ON l.admin_id = a.id WHERE l.target_member_id = ? ORDER BY l.id DESC");
    $log_stmt->execute([$member_id]);
    $logs = $log_stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Failed to query member profile details: " . $e->getMessage());
    header("Location: " . BASE_URL . "admin/members.php?error=db");
    exit;
}

$csrf = get_csrf_token();
$status = strtolower($member['status']);
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="text-dark fw-bold font-heading">Member Profile</h2>
        <p class="text-muted small">Detailed card registration, family, and health record ledger.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="members.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Top Summary Card (Header Profile Overview) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <?php 
                    $photo = $member['photo'];
                    if (empty($photo) || !file_exists(__DIR__ . '/../' . $photo)) {
                        echo '<div class="bg-light text-muted d-flex align-items-center justify-content-center border border-3 rounded-circle" style="width:100px; height:100px; font-size: 3rem;"><i class="fa-solid fa-user"></i></div>';
                    } else {
                        echo '<img src="' . BASE_URL . $photo . '" alt="" class="rounded-circle border border-3 border-light shadow-sm" style="width:100px; height:100px; object-fit:cover;">';
                    }
                    ?>
                </div>
                
                <div class="col text-center text-sm-start">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-center justify-content-sm-start">
                        <h3 class="text-dark fw-bold mb-0 font-heading"><?php echo htmlspecialchars($member['name']); ?></h3>
                        <?php 
                        $badge_class = 'bg-secondary';
                        if ($status === 'active') $badge_class = 'badge-active';
                        elseif ($status === 'expired') $badge_class = 'badge-expired';
                        elseif ($status === 'suspended') $badge_class = 'badge-suspended';
                        elseif ($status === 'deactivated') $badge_class = 'badge-deactivated';
                        
                        echo '<span class="badge ' . $badge_class . ' px-3 py-2 rounded-pill font-heading small">' . strtoupper($status) . '</span>';
                        ?>
                        
                        <?php if ($member['payment_status'] == 1): ?>
                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded small"><i class="fa-solid fa-circle-dollar-to-slot me-1"></i> PAID</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded small"><i class="fa-solid fa-hand-holding-dollar me-1"></i> PENDING</span>
                        <?php endif; ?>
                    </div>
                    
                    <span class="font-code text-muted d-block mt-1" style="font-size: 0.95rem; letter-spacing: 1px;">Member ID: <strong class="text-dark"><?php echo htmlspecialchars($member['member_id']); ?></strong></span>
                    <span class="text-muted small d-block">Registered by: <strong><?php echo htmlspecialchars($member['registered_by_name'] ?? 'System'); ?></strong> on <?php echo format_date($member['created_at']); ?></span>
                </div>
                
                <!-- Quick Actions Panel -->
                <div class="col-xl-5 col-lg-6 text-center text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end">
                        <a href="edit-member.php?id=<?php echo $member['member_id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                            <i class="fa-solid fa-user-gear me-1"></i> Edit Details
                        </a>
                        
                        <!-- Trigger Renewal Modal -->
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#renewCardModal">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Renew Card
                        </button>
                        
                        <a href="generate-card.php?id=<?php echo urlencode($member['member_id']); ?>&t=<?php echo time(); ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="fa-solid fa-print me-1"></i> Print / PDF
                        </a>
                        
                        <!-- Toggle actions based on current status -->
                        <?php if ($status === 'active'): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="updateMemberStatus('<?php echo $member['member_id']; ?>', 'suspended')">
                                <i class="fa-solid fa-pause me-1"></i> Suspend
                            </button>
                        <?php elseif ($status === 'suspended' || $status === 'expired'): ?>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="updateMemberStatus('<?php echo $member['member_id']; ?>', 'active')">
                                <i class="fa-solid fa-play me-1"></i> Activate
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($status !== 'deactivated'): ?>
                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="updateMemberStatus('<?php echo $member['member_id']; ?>', 'deactivated')">
                                <i class="fa-solid fa-ban me-1"></i> Deactivate
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Left Column: Personal Information & QR representation -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 h-100 bg-white">
            <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-address-card me-2"></i> Personal Information</h5>
            
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0 text-start small">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-bold" style="width: 180px;">Date of Birth</td>
                            <td class="text-dark fw-semibold"><?php echo format_date($member['dob'], 'd M Y'); ?> (<?php echo htmlspecialchars($member['age']); ?> Years old)</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Gender</td>
                            <td class="text-dark fw-semibold"><?php echo htmlspecialchars($member['gender']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Primary Mobile</td>
                            <td class="text-dark fw-semibold"><?php echo htmlspecialchars($member['mobile']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Alternate Mobile</td>
                            <td class="text-dark"><?php echo htmlspecialchars($member['alt_mobile'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Email Address</td>
                            <td class="text-dark fw-semibold"><?php echo htmlspecialchars($member['email'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Residential Address</td>
                            <td class="text-dark"><?php echo htmlspecialchars($member['address']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Location Details</td>
                            <td class="text-dark fw-semibold"><?php echo htmlspecialchars($member['city'] . ", " . $member['state'] . " - " . $member['pincode']); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">Card Validity Date</td>
                            <td class="text-dark fw-bold text-success"><?php echo format_date($member['validity_date']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- QR code preview box -->
            <div class="text-center p-3 border rounded-3 mt-4 bg-light">
                <span class="text-muted d-block small mb-2 fw-bold"><i class="fa-solid fa-qrcode me-1 text-success"></i> GENERATED PROFILE QR CODE</span>
                <?php 
                $qr = $member['qr_code'];
                if (empty($qr) || !file_exists(__DIR__ . '/../' . $qr)): 
                ?>
                    <div class="text-muted py-3 small">No QR Code found on server. Save details again to regenerate.</div>
                <?php else: ?>
                    <img src="<?php echo BASE_URL . $qr; ?>" alt="" class="img-fluid rounded border border-2 border-white shadow-sm" style="max-width: 150px;">
                    <div class="mt-2">
                        <a href="<?php echo BASE_URL . 'member.php?id=' . $member['member_id']; ?>" target="_blank" class="text-decoration-none small text-success fw-bold"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Scan link verification</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Medical Information & Family repeatable -->
    <div class="col-lg-6">
        <div class="row g-4">
            <!-- Health Profile Card -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-notes-medical me-2"></i> Medical Profile</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0 text-start small">
                            <tbody>
                                <tr>
                                    <td class="text-muted fw-bold" style="width: 180px;">Blood Group</td>
                                    <td><span class="badge bg-light-blue text-primary px-3 py-2 fs-6 font-heading fw-bold mt-1"><?php echo htmlspecialchars($member['blood_group']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Emergency Contact</td>
                                    <td class="text-dark fw-bold text-danger"><?php echo htmlspecialchars($member['emergency_name']); ?> (<i class="fa-solid fa-phone me-1 text-success"></i> <?php echo htmlspecialchars($member['emergency_mobile']); ?>)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Known Allergies</td>
                                    <td class="text-dark"><?php echo nl2br(htmlspecialchars($member['allergies'] ?? 'None registered.')); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-bold">Medical Conditions</td>
                                    <td class="text-dark"><?php echo nl2br(htmlspecialchars($member['health_info'] ?? 'None declared.')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Family Members dependents Card -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-people-roof me-2"></i> Dependents / Family Members</h5>
                    
                    <div class="table-responsive small">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Relation</th>
                                    <th>DOB</th>
                                    <th>Blood Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($family_members)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No linked family dependents.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($family_members as $fam): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($fam['name']); ?></td>
                                            <td><?php echo htmlspecialchars($fam['relation']); ?></td>
                                            <td><?php echo format_date($fam['dob']); ?></td>
                                            <td><span class="badge bg-light-blue text-primary px-2 font-heading"><?php echo htmlspecialchars($fam['blood_group']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Activity Log Audit Panel (Super Admin only) -->
    <?php if ($admin_role === 'super_admin'): ?>
        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-clock-rotate-left me-2"></i> Profile Audit History Log</h5>
                <div class="table-responsive small" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th>Timestamp</th>
                                <th>Admin User</th>
                                <th>Action Event</th>
                                <th>Details</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No activity logs recorded.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="text-muted"><?php echo format_date($log['created_at'], 'd M Y H:i:s'); ?></td>
                                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($log['admin_name'] ?? 'System'); ?></td>
                                        <td>
                                            <?php 
                                            $action_lbl = strtoupper($log['action']);
                                            $badge_lbl_class = 'bg-secondary-subtle text-secondary';
                                            if ($log['action'] === 'add_member') $badge_lbl_class = 'bg-success-subtle text-success';
                                            elseif ($log['action'] === 'edit_member') $badge_lbl_class = 'bg-warning-subtle text-warning';
                                            elseif ($log['action'] === 'renew_card') $badge_lbl_class = 'bg-primary-subtle text-primary';
                                            
                                            echo '<span class="badge ' . $badge_lbl_class . ' rounded-pill">' . $action_lbl . '</span>';
                                            ?>
                                        </td>
                                        <td><div class="log-detail-box"><?php echo htmlspecialchars($log['details']); ?></div></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Card Renewal -->
<div class="modal fade" id="renewCardModal" tabindex="-1" aria-labelledby="renewCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold font-heading" id="renewCardModalLabel"><i class="fa-solid fa-arrows-rotate me-2"></i> Renew Health Card Validity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="renewCardForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['member_id']); ?>">
                <input type="hidden" id="current_expiry" value="<?php echo htmlspecialchars($member['validity_date']); ?>">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Current Expiration Date</label>
                        <input type="text" class="form-control rounded-2 bg-light font-code" readonly value="<?php echo format_date($member['validity_date']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="renewal_duration" class="form-label small fw-bold">Renewal Extension Duration <span class="text-danger">*</span></label>
                        <select class="form-select rounded-2" id="renewal_duration" name="renewal_duration" required>
                            <option value="1">1 Year Validity</option>
                            <option value="2">2 Years Validity</option>
                            <option value="3">3 Years Validity</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_expiry" class="form-label small fw-bold">Calculated Expiry Date</label>
                        <input type="date" class="form-control rounded-2 bg-light font-code" id="new_expiry" name="new_expiry" readonly>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="renewal_payment" name="renewal_payment" value="1" required>
                        <label class="form-check-label small fw-bold text-dark" for="renewal_payment">
                            Confirm Renewal Payment Completed <span class="text-danger">*</span>
                        </label>
                        <div class="invalid-feedback">Please confirm offline payment completion.</div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fa-solid fa-circle-check me-1"></i> Confirm Renewal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Renewal Form logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Calculate new expiry date on opening/changing renewal duration
    function calculateRenewalExpiry() {
        const durationYears = parseInt($('#renewal_duration').val());
        const currentExpiryStr = $('#current_expiry').val();
        
        const currentExpiry = new Date(currentExpiryStr);
        const today = new Date();
        
        let baseDate;
        // If card is active, extend from the current expiration date.
        // Otherwise, extend from today's date!
        if (currentExpiry > today) {
            baseDate = new Date(currentExpiry);
        } else {
            baseDate = new Date(today);
        }
        
        baseDate.setFullYear(baseDate.getFullYear() + durationYears);
        
        const yyyy = baseDate.getFullYear();
        let mm = baseDate.getMonth() + 1;
        let dd = baseDate.getDate();
        
        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;
        
        $('#new_expiry').val(`${yyyy}-${mm}-${dd}`);
    }
    
    // Trigger calculation when modal displays and when dropdown modifications happen
    $('#renewCardModal').on('shown.bs.modal', function() {
        calculateRenewalExpiry();
    });
    $('#renewal_duration').on('change', function() {
        calculateRenewalExpiry();
    });
    
    // AJAX renewal form submit
    $('#renewCardForm').submit(function(e) {
        e.preventDefault();
        
        const form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        showLoader();
        
        $.ajax({
            url: 'ajax/renew-card.php',
            type: 'POST',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(response) {
                hideLoader();
                $('#renewCardModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        title: 'Renewed!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                hideLoader();
                $('#renewCardModal').modal('hide');
                Swal.fire('Error', 'Server connection failure.', 'error');
            }
        });
    });
});
</script>
