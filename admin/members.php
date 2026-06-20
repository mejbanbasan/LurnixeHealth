<?php
/**
 * Lurnixe Health Card System - Admin Members Directory
 * June 2026
 */
$page_title = "Members Directory";
require_once __DIR__ . '/includes/header.php';

// Generate CSRF token for AJAX actions
$csrf = get_csrf_token();
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="text-dark fw-bold font-heading">Members Directory</h2>
        <p class="text-muted small">Manage registry profiles, card statuses, and renewals.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add-member.php" class="btn btn-success rounded-pill px-4">
            <i class="fa-solid fa-user-plus me-2"></i> Add New Member
        </a>
        <button id="exportCsvBtn" class="btn btn-outline-primary rounded-pill px-4 ms-2">
            <i class="fa-solid fa-file-csv me-2"></i> Export CSV
        </button>
    </div>
</div>

<!-- Filters Panel -->
<div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
    <h5 class="text-dark fw-bold mb-3 small text-uppercase font-heading" style="letter-spacing: 1px;"><i class="fa-solid fa-filter text-success me-2"></i> Filters</h5>
    <div class="row g-3">
        <!-- Status Filter -->
        <div class="col-md-3">
            <label for="filterStatus" class="form-label small fw-bold">Card Status</label>
            <select class="form-select rounded-2" id="filterStatus">
                <option value="">All Statuses</option>
                <option value="active" <?php echo (($_GET['status'] ?? '') === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="expired" <?php echo (($_GET['status'] ?? '') === 'expired') ? 'selected' : ''; ?>>Expired</option>
                <option value="suspended" <?php echo (($_GET['status'] ?? '') === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                <option value="deactivated" <?php echo (($_GET['status'] ?? '') === 'deactivated') ? 'selected' : ''; ?>>Deactivated</option>
            </select>
        </div>
        
        <!-- Blood Group Filter -->
        <div class="col-md-3">
            <label for="filterBlood" class="form-label small fw-bold">Blood Group</label>
            <select class="form-select rounded-2" id="filterBlood">
                <option value="">All Blood Groups</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
            </select>
        </div>
        
        <!-- Date Range Filter -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Registration Date Range</label>
            <div class="input-group">
                <input type="date" class="form-control rounded-start-2" id="filterDateFrom" placeholder="From">
                <span class="input-group-text bg-light">to</span>
                <input type="date" class="form-control rounded-end-2" id="filterDateTo" placeholder="To">
                <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button"><i class="fa-solid fa-rotate-left"></i> Reset</button>
            </div>
        </div>
    </div>
</div>

<!-- Members Table Panel -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle w-100" id="membersTable">
            <thead>
                <tr>
                    <th style="width: 60px;">Photo</th>
                    <th>Name</th>
                    <th>Member ID</th>
                    <th>Mobile</th>
                    <th>Blood Group</th>
                    <th>Valid Till</th>
                    <th>Status</th>
                    <th style="width: 150px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Populated via AJAX DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- Hidden CSRF token value -->
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- DataTables setup script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize DataTable
    const table = $('#membersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajax/search-members.php',
            type: 'GET',
            data: function(d) {
                d.status = $('#filterStatus').val();
                d.blood_group = $('#filterBlood').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { 
                data: 'photo', 
                orderable: false,
                render: function(data, type, row) {
                    if (!data) {
                        return '<div class="avatar-table-img bg-light text-muted d-flex align-items-center justify-content-center border" style="width:40px; height:40px;"><i class="fa-solid fa-user"></i></div>';
                    } else {
                        return '<img src="<?php echo BASE_URL; ?>' + data + '" class="avatar-table-img" alt="">';
                    }
                }
            },
            { data: 'name' },
            { 
                data: 'member_id',
                render: function(data) {
                    return '<span class="font-code fw-semibold">' + data + '</span>';
                }
            },
            { data: 'mobile' },
            { 
                data: 'blood_group',
                render: function(data) {
                    return '<span class="badge bg-light-blue text-primary px-3 py-1 font-heading fw-bold">' + data + '</span>';
                }
            },
            { data: 'validity_date' },
            { 
                data: 'status',
                render: function(data) {
                    const status = data.toLowerCase();
                    let badgeClass = 'bg-secondary';
                    if (status === 'active') badgeClass = 'badge-active';
                    else if (status === 'expired') badgeClass = 'badge-expired';
                    else if (status === 'suspended') badgeClass = 'badge-suspended';
                    else if (status === 'deactivated') badgeClass = 'badge-deactivated';
                    
                    return '<span class="badge ' + badgeClass + ' px-3 py-1 rounded-pill">' + data.toUpperCase() + '</span>';
                }
            },
            { 
                data: 'actions', 
                orderable: false, 
                class: 'text-end',
                render: function(data, type, row) {
                    return `
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                                <li><a class="dropdown-item py-2" href="view-member.php?id=${row.member_id}"><i class="fa-solid fa-eye text-primary me-2"></i> View Profile</a></li>
                                <li><a class="dropdown-item py-2" href="edit-member.php?id=${row.member_id}"><i class="fa-solid fa-pen text-warning me-2"></i> Edit Details</a></li>
                                <li><a class="dropdown-item py-2" href="generate-card.php?id=${row.member_id}&t=${Date.now()}" target="_blank"><i class="fa-solid fa-id-card text-success me-2"></i> Download Card</a></li>
                                <li><hr class="dropdown-divider"></li>
                                ${row.status.toLowerCase() !== 'active' ? 
                                    `<li><a class="dropdown-item py-2 text-success" href="javascript:void(0);" onclick="updateMemberStatus('${row.member_id}', 'active')"><i class="fa-solid fa-play me-2"></i> Reactivate</a></li>` : 
                                    `<li><a class="dropdown-item py-2 text-warning" href="javascript:void(0);" onclick="updateMemberStatus('${row.member_id}', 'suspended')"><i class="fa-solid fa-pause me-2"></i> Suspend Card</a></li>`
                                }
                                ${row.status.toLowerCase() !== 'deactivated' ? 
                                    `<li><a class="dropdown-item py-2 text-danger" href="javascript:void(0);" onclick="updateMemberStatus('${row.member_id}', 'deactivated')"><i class="fa-solid fa-ban me-2"></i> Deactivate</a></li>` : 
                                    ''
                                }
                                <?php if ($admin_role === 'super_admin'): ?>
                                    <li><a class="dropdown-item py-2 text-danger fw-bold" href="javascript:void(0);" onclick="deleteMember('${row.member_id}')"><i class="fa-solid fa-trash-can me-2"></i> Delete</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [25, 50, 100],
        order: [[1, 'asc']], // Sort by Name
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search members..."
        }
    });

    // Handle filter triggers
    $('#filterStatus, #filterBlood, #filterDateFrom, #filterDateTo').on('change', function() {
        table.draw();
    });

    // Reset Filters
    $('#clearFiltersBtn').click(function() {
        $('#filterStatus').val('');
        $('#filterBlood').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        table.draw();
    });

    // Export CSV handler
    $('#exportCsvBtn').click(function() {
        const status = $('#filterStatus').val();
        const blood = $('#filterBlood').val();
        const dateFrom = $('#filterDateFrom').val();
        const dateTo = $('#filterDateTo').val();
        
        window.location.href = `reports.php?action=export_csv&status=${status}&blood_group=${blood}&date_from=${dateFrom}&date_to=${dateTo}`;
    });
});

// Super Admin Delete handler
function deleteMember(memberId) {
    Swal.fire({
        title: 'Delete Profile?',
        text: "This action will permanently delete the member and all associated family records!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E74C3C',
        cancelButtonColor: '#7F8C8D',
        confirmButtonText: 'Yes, delete permanently!'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoader();
            $.ajax({
                url: 'ajax/delete-member.php',
                type: 'POST',
                data: {
                    member_id: memberId,
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            $('#membersTable').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Connection failure to server.', 'error');
                }
            });
        }
    });
}
</script>
