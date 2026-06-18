<?php
/**
 * Lurnixe Health Card System - Admin User Administration
 * June 2026
 */
$page_title = "Manage Admins";
require_once __DIR__ . '/includes/header.php';

// Enforce Super Admin authorization check
require_super_admin();

$csrf = get_csrf_token();
$error_msg = "";
$success_msg = "";

// ----------------------------------------------------
// 1. ADD NEW ADMIN ACTION
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_add'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? 'admin');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        $error_msg = "Security token mismatch. Please reload the page.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        $error_msg = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        try {
            // Check if email already exists
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
            $check_stmt->execute([$email]);
            if ($check_stmt->fetchColumn() > 0) {
                $error_msg = "An administrator account with that email already exists.";
            } else {
                // Hashing using Bcrypt cost factor 12
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                $insert_stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
                $insert_stmt->execute([$name, $email, $hashed_password, $role]);
                
                log_activity($pdo, $_SESSION['admin_id'], 'create_admin', null, "Created new admin account for $name ($role)");
                $success_msg = "Administrator account created successfully.";
            }
        } catch (PDOException $e) {
            error_log("Failed to create admin: " . $e->getMessage());
            $error_msg = "Database operation failed.";
        }
    }
}

// ----------------------------------------------------
// 2. TOGGLE STATUS ACTION (AJAX / POST)
// ----------------------------------------------------
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $admin_id_to_toggle = intval($_GET['id']);
    
    // Prevent toggling oneself
    if ($admin_id_to_toggle === intval($_SESSION['admin_id'])) {
        header("Location: admins.php?error=self_status");
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT status, name FROM admins WHERE id = ?");
        $stmt->execute([$admin_id_to_toggle]);
        $target_admin = $stmt->fetch();
        
        if ($target_admin) {
            $new_status = ($target_admin['status'] === 'active') ? 'inactive' : 'active';
            
            $update_stmt = $pdo->prepare("UPDATE admins SET status = ? WHERE id = ?");
            $update_stmt->execute([$new_status, $admin_id_to_toggle]);
            
            log_activity($pdo, $_SESSION['admin_id'], 'toggle_admin_status', null, "Toggled status of admin " . $target_admin['name'] . " to $new_status");
            header("Location: admins.php?success=1");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Failed to toggle admin status: " . $e->getMessage());
    }
}

// ----------------------------------------------------
// 3. DELETE ADMIN ACTION
// ----------------------------------------------------
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $admin_id_to_delete = intval($_GET['id']);
    
    // Prevent self deletion
    if ($admin_id_to_delete === intval($_SESSION['admin_id'])) {
        header("Location: admins.php?error=self_delete");
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT name FROM admins WHERE id = ?");
        $stmt->execute([$admin_id_to_delete]);
        $target_admin = $stmt->fetch();
        
        if ($target_admin) {
            // Delete admin record
            $delete_stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $delete_stmt->execute([$admin_id_to_delete]);
            
            log_activity($pdo, $_SESSION['admin_id'], 'delete_admin', null, "Deleted admin account for " . $target_admin['name']);
            header("Location: admins.php?success=1");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Failed to delete admin: " . $e->getMessage());
    }
}

// ----------------------------------------------------
// FETCH ALL ADMINS LIST
// ----------------------------------------------------
$admins = [];
try {
    $admins = $pdo->query("SELECT id, name, email, role, status, created_at FROM admins ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    error_log("Failed to load admins: " . $e->getMessage());
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">Manage Admins</h2>
        <p class="text-muted small">Create and manage admin portal access accounts.</p>
    </div>
</div>

<!-- Alert messages -->
<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> 
        <?php 
        $err = $_GET['error'];
        if ($err === 'self_status') echo "Operation Blocked: You cannot deactivate your own account.";
        elseif ($err === 'self_delete') echo "Operation Blocked: You cannot delete your own account.";
        else echo "An error occurred during database operation.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left column: Add new admin form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-user-shield me-2"></i> Create Account</h5>
            
            <form action="" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="action_add" value="1">
                
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-2" id="name" name="name" required placeholder="Enter full name">
                    <div class="invalid-feedback">Please enter the admin's name.</div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control rounded-2" id="email" name="email" required placeholder="name@lurnixehealth.com">
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label small fw-bold">Access Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control rounded-2" id="password" name="password" required placeholder="Min 6 characters" minlength="6">
                    <div class="invalid-feedback">Please enter a password (min 6 characters).</div>
                </div>
                
                <div class="mb-4">
                    <label for="role" class="form-label small fw-bold">Authorization Role <span class="text-danger">*</span></label>
                    <select class="form-select rounded-2" id="role" name="role" required>
                        <option value="admin">Admin (Limited Management)</option>
                        <option value="super_admin">Super Admin (Full Access)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success rounded-pill w-100 py-2">
                    <i class="fa-solid fa-circle-check me-2"></i> Register Account
                </button>
            </form>
        </div>
    </div>
    
    <!-- Right column: List of current admins -->
    <div class="col-lg-8">
        <div class="table-card bg-white h-100">
            <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-users-gear me-2"></i> Administrator Directory</h5>
            
            <div class="table-responsive small">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Access Role</th>
                            <th>Account Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $adm): ?>
                            <tr>
                                <td><?php echo $adm['id']; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($adm['name']); ?></td>
                                <td><?php echo htmlspecialchars($adm['email']); ?></td>
                                <td>
                                    <?php 
                                    if ($adm['role'] === 'super_admin') {
                                        echo '<span class="badge bg-primary rounded-pill">SUPER ADMIN</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary rounded-pill">ADMIN</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($adm['status'] === 'active') {
                                        echo '<span class="badge bg-success-subtle text-success rounded-pill">ACTIVE</span>';
                                    } else {
                                        echo '<span class="badge bg-danger-subtle text-danger rounded-pill">INACTIVE</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($adm['id'] !== intval($_SESSION['admin_id'])): ?>
                                        <!-- Toggle Status button -->
                                        <a href="admins.php?toggle_status=1&id=<?php echo $adm['id']; ?>" class="btn btn-sm <?php echo $adm['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?> rounded-pill px-3 me-1">
                                            <?php echo $adm['status'] === 'active' ? '<i class="fa-solid fa-user-slash me-1"></i> Block' : '<i class="fa-solid fa-user-check me-1"></i> Unblock'; ?>
                                        </a>
                                        
                                        <!-- Delete Admin -->
                                        <a href="javascript:void(0);" onclick="confirmDeleteAdmin(<?php echo $adm['id']; ?>, '<?php echo htmlspecialchars(addslashes($adm['name'])); ?>')" class="btn btn-sm btn-outline-danger border-0 rounded-circle">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Active Session</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Admin Action Script -->
<script>
function confirmDeleteAdmin(id, name) {
    Swal.fire({
        title: 'Delete Administrator?',
        text: `Are you sure you want to permanently delete account for ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E74C3C',
        cancelButtonColor: '#7F8C8D',
        confirmButtonText: 'Yes, delete permanently!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `admins.php?delete=1&id=${id}`;
        }
    });
}
</script>
