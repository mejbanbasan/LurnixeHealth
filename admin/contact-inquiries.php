<?php
/**
 * Lurnixe Health Card System - Manage Contact Inquiries
 * June 2026
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce login validation
check_auth();

$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];

// ----------------------------------------------------
// PROCESS ACTIONS (GET)
// ----------------------------------------------------
if (isset($_GET['action']) && isset($_GET['id'])) {
    $inquiry_id = intval($_GET['id']);
    $action = $_GET['action'];
    $allowed_statuses = [
        'mark_read' => 'read',
        'mark_new' => 'new',
        'mark_closed' => 'closed',
        'mark_reopen' => 'read',
        'mark_replied' => 'replied'
    ];
    
    if (array_key_exists($action, $allowed_statuses)) {
        $new_status = $allowed_statuses[$action];
        
        try {
            $stmt = $pdo->prepare("SELECT full_name FROM contact_inquiries WHERE id = ?");
            $stmt->execute([$inquiry_id]);
            $inquiry = $stmt->fetch();
            
            if ($inquiry) {
                $name = $inquiry['full_name'];
                
                // Update status
                if ($new_status === 'replied') {
                    $update_stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ?, reply_subject = IFNULL(reply_subject, 'Marked as Replied'), reply_message = IFNULL(reply_message, 'Inquiry was manually marked as replied by the administrator.'), replied_at = IFNULL(replied_at, CURRENT_TIMESTAMP), replied_by = IFNULL(replied_by, ?) WHERE id = ?");
                    $update_stmt->execute([$new_status, $admin_id, $inquiry_id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ? WHERE id = ?");
                    $update_stmt->execute([$new_status, $inquiry_id]);
                }
                
                // Log activity
                log_activity($pdo, $admin_id, 'inquiry_status_update', null, "Marked contact inquiry #$inquiry_id from $name as " . strtoupper($new_status));
                
                header("Location: contact-inquiries.php?success=status_updated");
                exit;
            } else {
                header("Location: contact-inquiries.php?error=not_found");
                exit;
            }
        } catch (PDOException $e) {
            error_log("Failed to update inquiry status: " . $e->getMessage());
            header("Location: contact-inquiries.php?error=db");
            exit;
        }
    }
}

// ----------------------------------------------------
// PROCESS REPLY (POST)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_reply') {
    // CSRF verification
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: contact-inquiries.php?error=csrf");
        exit;
    }
    
    $inquiry_id = intval($_POST['inquiry_id'] ?? 0);
    $reply_subject = trim($_POST['reply_subject'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    
    if ($inquiry_id <= 0 || empty($reply_subject) || empty($reply_message)) {
        header("Location: contact-inquiries.php?error=validation");
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT full_name, email FROM contact_inquiries WHERE id = ?");
        $stmt->execute([$inquiry_id]);
        $inquiry = $stmt->fetch();
        
        if ($inquiry) {
            $name = $inquiry['full_name'];
            $email = $inquiry['email'];
            
            // Save reply to database and mark as replied
            $update_stmt = $pdo->prepare("UPDATE contact_inquiries SET status = 'replied', reply_subject = ?, reply_message = ?, replied_at = CURRENT_TIMESTAMP, replied_by = ? WHERE id = ?");
            $update_stmt->execute([$reply_subject, $reply_message, $admin_id, $inquiry_id]);
            
            // Log activity
            log_activity($pdo, $admin_id, 'inquiry_replied', null, "Replied to contact inquiry #$inquiry_id from $name ($email)");
            
            header("Location: contact-inquiries.php?success=reply_sent");
            exit;
        } else {
            header("Location: contact-inquiries.php?error=not_found");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Failed to send reply for inquiry: " . $e->getMessage());
        header("Location: contact-inquiries.php?error=db");
        exit;
    }
}

// ----------------------------------------------------
// FETCH INQUIRIES LIST
// ----------------------------------------------------
$filter_status = $_GET['status'] ?? 'all';
$inquiries = [];
try {
    if ($filter_status === 'new') {
        $stmt = $pdo->prepare("SELECT ci.*, a.name as replier_name FROM contact_inquiries ci LEFT JOIN admins a ON ci.replied_by = a.id WHERE ci.status = 'new' ORDER BY ci.id DESC");
        $stmt->execute();
    } elseif ($filter_status === 'read') {
        $stmt = $pdo->prepare("SELECT ci.*, a.name as replier_name FROM contact_inquiries ci LEFT JOIN admins a ON ci.replied_by = a.id WHERE ci.status = 'read' ORDER BY ci.id DESC");
        $stmt->execute();
    } elseif ($filter_status === 'replied') {
        $stmt = $pdo->prepare("SELECT ci.*, a.name as replier_name FROM contact_inquiries ci LEFT JOIN admins a ON ci.replied_by = a.id WHERE ci.status = 'replied' ORDER BY ci.id DESC");
        $stmt->execute();
    } elseif ($filter_status === 'closed') {
        $stmt = $pdo->prepare("SELECT ci.*, a.name as replier_name FROM contact_inquiries ci LEFT JOIN admins a ON ci.replied_by = a.id WHERE ci.status = 'closed' ORDER BY ci.id DESC");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT ci.*, a.name as replier_name FROM contact_inquiries ci LEFT JOIN admins a ON ci.replied_by = a.id ORDER BY ci.id DESC");
        $stmt->execute();
    }
    $inquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to fetch contact inquiries: " . $e->getMessage());
}

$page_title = "Contact Inquiries";
require_once __DIR__ . '/includes/header.php';
$csrf = get_csrf_token();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">Contact Inquiries</h2>
        <p class="text-muted small">View and manage messages received from visitors through the public Contact page.</p>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> 
        <?php 
        $suc_type = $_GET['success'];
        if ($suc_type === 'status_updated') {
            echo "Inquiry status updated successfully.";
        } elseif ($suc_type === 'reply_sent') {
            echo "Reply saved successfully and inquiry marked as REPLIED.";
        } else {
            echo "Action completed successfully.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i> 
        <?php 
        $err_type = $_GET['error'];
        if ($err_type === 'not_found') {
            echo "Inquiry records not found.";
        } elseif ($err_type === 'csrf') {
            echo "Security token mismatch. Please reload and try again.";
        } elseif ($err_type === 'validation') {
            echo "Failed validation: Please make sure all required fields are filled.";
        } else {
            echo "An error occurred. Please try again.";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Navigation Tabs for Inquiries Status -->
<ul class="nav nav-pills mb-4" id="inquiryTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($filter_status === 'all') ? 'active' : ''; ?>" href="contact-inquiries.php?status=all"><i class="fa-solid fa-list me-2"></i> All Inquiries</a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($filter_status === 'new') ? 'active font-bold' : ''; ?>" href="contact-inquiries.php?status=new">
            <i class="fa-solid fa-envelope me-2"></i> New
            <?php if (isset($unread_inquiries) && $unread_inquiries > 0): ?>
                <span class="badge bg-danger ms-1 rounded-pill"><?php echo $unread_inquiries; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($filter_status === 'read') ? 'active' : ''; ?>" href="contact-inquiries.php?status=read"><i class="fa-solid fa-envelope-open me-2"></i> Read</a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 me-2 <?php echo ($filter_status === 'replied') ? 'active' : ''; ?>" href="contact-inquiries.php?status=replied"><i class="fa-solid fa-reply me-2"></i> Replied</a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 <?php echo ($filter_status === 'closed') ? 'active' : ''; ?>" href="contact-inquiries.php?status=closed"><i class="fa-solid fa-circle-xmark me-2"></i> Closed</a>
    </li>
</ul>

<div class="table-card bg-white">
    <div class="table-responsive small">
        <table class="table table-hover table-striped align-middle mb-0" id="inquiriesTable">
            <thead>
                <tr class="fw-bold text-muted">
                    <th style="width: 50px;">ID</th>
                    <th style="width: 140px;">Date & Time</th>
                    <th style="width: 180px;">Sender Details</th>
                    <th>Inquiry Message</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 250px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-3 fs-3 text-secondary"></i>
                            No contact inquiries found matching filter status.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inquiries as $inq): ?>
                        <tr>
                            <td><?php echo $inq['id']; ?></td>
                            <td class="text-muted">
                                <?php echo format_date($inq['created_at'], 'd M Y H:i'); ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($inq['full_name']); ?></div>
                                <?php if (!empty($inq['reply_message'])): ?>
                                    <a href="javascript:void(0);" class="text-decoration-none text-muted small view-reply-btn"
                                       data-name="<?php echo htmlspecialchars($inq['full_name']); ?>"
                                       data-email="<?php echo htmlspecialchars($inq['email']); ?>"
                                       data-msg="<?php echo htmlspecialchars($inq['message']); ?>"
                                       data-reply-subject="<?php echo htmlspecialchars($inq['reply_subject'] ?? ''); ?>"
                                       data-reply-msg="<?php echo htmlspecialchars($inq['reply_message'] ?? ''); ?>"
                                       data-replied-at="<?php echo htmlspecialchars(format_date($inq['replied_at'], 'd M Y H:i')); ?>"
                                       data-replier="<?php echo htmlspecialchars($inq['replier_name'] ?? 'System'); ?>"
                                       title="Click to view sent reply">
                                        <i class="fa-regular fa-envelope me-1 text-primary"></i><?php echo htmlspecialchars($inq['email']); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:void(0);" class="text-decoration-none text-muted small reply-btn"
                                       data-id="<?php echo $inq['id']; ?>"
                                       data-name="<?php echo htmlspecialchars($inq['full_name']); ?>"
                                       data-email="<?php echo htmlspecialchars($inq['email']); ?>"
                                       data-msg="<?php echo htmlspecialchars($inq['message']); ?>"
                                       data-subject="Re: Health Card Inquiry - <?php echo htmlspecialchars($inq['full_name']); ?>"
                                       title="Click to reply">
                                        <i class="fa-regular fa-envelope me-1 text-success"></i><?php echo htmlspecialchars($inq['email']); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($inq['phone'])): ?>
                                    <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($inq['phone']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="p-2 bg-light rounded border small" style="max-height: 120px; overflow-y: auto; white-space: pre-line;">
                                    <?php echo htmlspecialchars($inq['message']); ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                $status = $inq['status'];
                                if ($status === 'new') {
                                    echo '<span class="badge bg-danger rounded-pill"><i class="fa-solid fa-envelope me-1"></i> NEW</span>';
                                } elseif ($status === 'read') {
                                    echo '<span class="badge bg-info text-dark rounded-pill"><i class="fa-solid fa-envelope-open me-1"></i> READ</span>';
                                } elseif ($status === 'replied') {
                                    echo '<span class="badge bg-success rounded-pill"><i class="fa-solid fa-reply me-1"></i> REPLIED</span>';
                                } elseif ($status === 'closed') {
                                    echo '<span class="badge bg-secondary rounded-pill"><i class="fa-solid fa-circle-xmark me-1"></i> CLOSED</span>';
                                }
                                ?>
                            </td>
                            <td class="text-end">
                                <!-- View Reply Button (if replied) -->
                                <?php if (!empty($inq['reply_message'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1 mb-1 view-reply-btn"
                                            data-name="<?php echo htmlspecialchars($inq['full_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($inq['email']); ?>"
                                            data-msg="<?php echo htmlspecialchars($inq['message']); ?>"
                                            data-reply-subject="<?php echo htmlspecialchars($inq['reply_subject'] ?? ''); ?>"
                                            data-reply-msg="<?php echo htmlspecialchars($inq['reply_message'] ?? ''); ?>"
                                            data-replied-at="<?php echo htmlspecialchars(format_date($inq['replied_at'], 'd M Y H:i')); ?>"
                                            data-replier="<?php echo htmlspecialchars($inq['replier_name'] ?? 'System'); ?>">
                                        <i class="fa-solid fa-eye me-1"></i> View Reply
                                    </button>
                                <?php endif; ?>

                                <!-- Reply Button (if status is new or read) -->
                                <?php if ($status === 'new' || $status === 'read'): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 me-1 mb-1 reply-btn"
                                            data-id="<?php echo $inq['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($inq['full_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($inq['email']); ?>"
                                            data-msg="<?php echo htmlspecialchars($inq['message']); ?>"
                                            data-subject="Re: Health Card Inquiry - <?php echo htmlspecialchars($inq['full_name']); ?>">
                                        <i class="fa-solid fa-reply me-1"></i> Reply
                                    </button>
                                <?php endif; ?>

                                <!-- Reply Done Button (if status is new or read) -->
                                <?php if ($status === 'new' || $status === 'read'): ?>
                                    <a href="contact-inquiries.php?action=mark_replied&id=<?php echo $inq['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 me-1 mb-1" title="Mark as Replied / Reply Done">
                                        <i class="fa-solid fa-check-double me-1"></i> Reply Done
                                    </a>
                                <?php endif; ?>

                                <!-- Read Button (if status is new) -->
                                <?php if ($status === 'new'): ?>
                                    <a href="contact-inquiries.php?action=mark_read&id=<?php echo $inq['id']; ?>" class="btn btn-sm btn-outline-info rounded-pill px-3 me-1 mb-1">
                                        <i class="fa-solid fa-envelope-open me-1"></i> Read
                                    </a>
                                <?php endif; ?>

                                <!-- Close Button (if not closed) -->
                                <?php if ($status !== 'closed'): ?>
                                    <a href="contact-inquiries.php?action=mark_closed&id=<?php echo $inq['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 me-1 mb-1">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Close
                                    </a>
                                <?php endif; ?>

                                <!-- Reopen Button (if closed) -->
                                <?php if ($status === 'closed'): ?>
                                    <a href="contact-inquiries.php?action=mark_reopen&id=<?php echo $inq['id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-1 mb-1">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Reopen
                                    </a>
                                <?php endif; ?>

                                <!-- Mark as New (if read or closed) -->
                                <?php if ($status === 'read' || $status === 'closed'): ?>
                                    <a href="contact-inquiries.php?action=mark_new&id=<?php echo $inq['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 mb-1" title="Mark as New">
                                        <i class="fa-solid fa-envelope me-1"></i> Reset New
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Compose Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="contact-inquiries.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="send_reply">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="inquiry_id" id="reply_inquiry_id">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="replyModalLabel"><i class="fa-solid fa-reply me-2"></i> Send Email Response</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sender Name</label>
                            <input type="text" class="form-control rounded-2 bg-light" id="reply_sender_name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sender Email</label>
                            <input type="email" class="form-control rounded-2 bg-light" id="reply_sender_email" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Original Message</label>
                            <textarea class="form-control rounded-2 bg-light small" id="reply_original_msg" rows="3" readonly></textarea>
                        </div>
                        <div class="col-12">
                            <label for="reply_subject" class="form-label small fw-bold">Reply Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-2" id="reply_subject" name="reply_subject" required>
                            <div class="invalid-feedback">Please enter a subject.</div>
                        </div>
                        <div class="col-12">
                            <label for="reply_message" class="form-label small fw-bold">Reply Message <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-2" id="reply_message" name="reply_message" rows="6" required placeholder="Type your email response here..."></textarea>
                            <div class="invalid-feedback">Please enter a reply message.</div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fa-solid fa-paper-plane me-2"></i> Send Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Sent Reply Modal -->
<div class="modal fade" id="viewReplyModal" tabindex="-1" aria-labelledby="viewReplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewReplyModalLabel"><i class="fa-solid fa-envelope-open-text me-2"></i> View Sent Response</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Sender Name</label>
                        <input type="text" class="form-control rounded-2 bg-light" id="view_sender_name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Sender Email</label>
                        <input type="email" class="form-control rounded-2 bg-light" id="view_sender_email" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Original Message</label>
                        <textarea class="form-control rounded-2 bg-light small" id="view_original_msg" rows="3" readonly></textarea>
                    </div>
                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-reply me-1"></i> Sent Reply Details</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Reply Sent At / By</label>
                        <input type="text" class="form-control rounded-2 bg-light" id="view_replied_at" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Reply Subject</label>
                        <input type="text" class="form-control rounded-2 bg-light" id="view_reply_subject" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Reply Message</label>
                        <textarea class="form-control rounded-2 bg-light small" id="view_reply_msg" rows="5" readonly></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- DataTables & Modals initialization -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    if ($('#inquiriesTable tbody tr td').length > 1) {
        $('#inquiriesTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search inquiries..."
            }
        });
    }

    // Modal populate event hooks
    $('.reply-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const msg = $(this).data('msg');
        const subject = $(this).data('subject');
        
        $('#reply_inquiry_id').val(id);
        $('#reply_sender_name').val(name);
        $('#reply_sender_email').val(email);
        $('#reply_original_msg').val(msg);
        $('#reply_subject').val(subject);
        $('#reply_message').val('');
        
        $('#replyModal').modal('show');
    });

    $('.view-reply-btn').on('click', function() {
        const name = $(this).data('name');
        const email = $(this).data('email');
        const msg = $(this).data('msg');
        const replySubject = $(this).data('reply-subject');
        const replyMsg = $(this).data('reply-msg');
        const repliedAt = $(this).data('replied-at');
        const replier = $(this).data('replier');
        
        $('#view_sender_name').val(name);
        $('#view_sender_email').val(email);
        $('#view_original_msg').val(msg);
        $('#view_reply_subject').val(replySubject);
        $('#view_reply_msg').val(replyMsg);
        $('#view_replied_at').val(repliedAt + ' by ' + replier);
        
        $('#viewReplyModal').modal('show');
    });
});
</script>
