<?php
/**
 * Lurnixe Health Card System - Edit Member Form
 * June 2026
 */
$page_title = "Edit Member Details";
require_once __DIR__ . '/includes/header.php';

$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    header("Location: " . BASE_URL . "admin/members.php?error=notfound");
    exit;
}

try {
    // 1. Fetch member details
    $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();
    
    if (!$member) {
        header("Location: " . BASE_URL . "admin/members.php?error=notfound");
        exit;
    }
    
    // 2. Fetch linked family members
    $fam_stmt = $pdo->prepare("SELECT * FROM family_members WHERE member_id = ? ORDER BY id ASC");
    $fam_stmt->execute([$member_id]);
    $family_members = $fam_stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Failed to load member for editing: " . $e->getMessage());
    header("Location: " . BASE_URL . "admin/members.php?error=db");
    exit;
}

$csrf = get_csrf_token();
$today = date('Y-m-d');

// Split address if possible (assuming line1, line2 format joined by comma)
$address_raw = $member['address'];
$address_parts = explode(', ', $address_raw);
$addr_line1 = $address_parts[0] ?? '';
$addr_line2 = isset($address_parts[1]) ? implode(', ', array_slice($address_parts, 1)) : '';
?>

<div class="row mb-4 align-items-center">
    <div class="col-8">
        <h2 class="text-dark fw-bold font-heading">Edit Member Profile</h2>
        <p class="text-muted small">Update member registration info and medical profile registry.</p>
    </div>
    <div class="col-4 text-end">
        <span class="font-code fw-bold text-success fs-5 p-2 bg-light rounded border">ID: <?php echo htmlspecialchars($member['member_id']); ?></span>
    </div>
</div>

<form id="editMemberForm" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['member_id']); ?>">
    
    <div class="row g-4">
        <!-- 1. Personal Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-user-pen me-2"></i> Personal Information</h5>
                
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="name" name="name" required value="<?php echo htmlspecialchars($member['name']); ?>">
                        <div class="invalid-feedback">Please enter the member's full name.</div>
                    </div>
                    
                    <!-- Gender -->
                    <div class="col-md-6">
                        <label for="gender" class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select rounded-2" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo $member['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $member['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $member['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <div class="invalid-feedback">Please select the gender.</div>
                    </div>
                    
                    <!-- DOB -->
                    <div class="col-md-6">
                        <label for="dob" class="form-label small fw-bold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-2" id="dob" name="dob" required max="<?php echo $today; ?>" value="<?php echo $member['dob']; ?>">
                        <div class="invalid-feedback">Please select the date of birth.</div>
                    </div>
                    
                    <!-- Age (Auto-calculated) -->
                    <div class="col-md-6">
                        <label for="age" class="form-label small fw-bold">Age (Years)</label>
                        <input type="number" class="form-control rounded-2 bg-light" id="age" name="age" readonly value="<?php echo $member['age']; ?>">
                    </div>
                    
                    <!-- Mobile -->
                    <div class="col-md-6">
                        <label for="mobile" class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-2" id="mobile" name="mobile" required value="<?php echo htmlspecialchars($member['mobile']); ?>" pattern="[0-9]{10}">
                        <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                    </div>
                    
                    <!-- Alt Mobile -->
                    <div class="col-md-6">
                        <label for="alt_mobile" class="form-label small fw-bold">Alternate Mobile Number</label>
                        <input type="tel" class="form-control rounded-2" id="alt_mobile" name="alt_mobile" value="<?php echo htmlspecialchars($member['alt_mobile'] ?? ''); ?>" pattern="[0-9]{10}">
                    </div>
                    
                    <!-- Email -->
                    <div class="col-12">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" class="form-control rounded-2" id="email" name="email" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>">
                    </div>
                    
                    <!-- Address Line 1 -->
                    <div class="col-12">
                        <label for="address_line1" class="form-label small fw-bold">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="address_line1" name="address_line1" required value="<?php echo htmlspecialchars($addr_line1); ?>">
                        <div class="invalid-feedback">Please enter the address.</div>
                    </div>
                    
                    <!-- Address Line 2 -->
                    <div class="col-12">
                        <label for="address_line2" class="form-label small fw-bold">Address Line 2</label>
                        <input type="text" class="form-control rounded-2" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($addr_line2); ?>">
                    </div>
                    
                    <!-- City -->
                    <div class="col-md-4">
                        <label for="city" class="form-label small fw-bold">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="city" name="city" required value="<?php echo htmlspecialchars($member['city']); ?>">
                        <div class="invalid-feedback">Please specify the city.</div>
                    </div>
                    
                    <!-- State -->
                    <div class="col-md-4">
                        <label for="state" class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="state" name="state" required value="<?php echo htmlspecialchars($member['state']); ?>">
                        <div class="invalid-feedback">Please specify the state.</div>
                    </div>
                    
                    <!-- Pincode -->
                    <div class="col-md-4">
                        <label for="pincode" class="form-label small fw-bold">PIN Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="pincode" name="pincode" required value="<?php echo htmlspecialchars($member['pincode']); ?>" pattern="[0-9]{5,6}">
                        <div class="invalid-feedback">Please enter a valid PIN Code.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Upload & Validity -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Photo Upload Box -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-camera me-2"></i> Update Photo</h5>
                        <div class="text-center py-2">
                            <div class="bg-light rounded border mx-auto mb-3 d-flex align-items-center justify-content-center text-muted" id="photoPreviewContainer" style="width: 140px; height: 160px; overflow: hidden;">
                                <?php if (!empty($member['photo']) && file_exists(__DIR__ . '/../' . $member['photo'])): ?>
                                    <img src="<?php echo BASE_URL . $member['photo']; ?>" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <i class="fa-solid fa-user fs-1"></i>
                                <?php endif; ?>
                            </div>
                            <label class="btn btn-outline-primary btn-sm rounded-pill px-4" for="photo">
                                <i class="fa-solid fa-upload me-1"></i> Choose New Photo
                            </label>
                            <input type="file" class="form-control d-none" id="photo" name="photo" accept="image/jpeg, image/png">
                            <p class="text-muted small mt-2 mb-0" style="font-size: 0.75rem;">Leave empty to keep current photo.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Status & settings -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-sliders me-2"></i> Card Settings</h5>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label small fw-bold">Card Status</label>
                            <!-- If role is limited and status is deactivated, make it disabled -->
                            <select class="form-select rounded-2" id="status" name="status" <?php echo ($member['status'] == 'deactivated' && $admin_role !== 'super_admin') ? 'disabled' : ''; ?>>
                                <option value="active" <?php echo $member['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="expired" <?php echo $member['status'] == 'expired' ? 'selected' : ''; ?>>Expired</option>
                                <option value="suspended" <?php echo $member['status'] == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                <option value="deactivated" <?php echo $member['status'] == 'deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                            </select>
                            <?php if ($member['status'] == 'deactivated' && $admin_role !== 'super_admin'): ?>
                                <small class="text-danger mt-1 d-block" style="font-size:0.7rem;">Only Super Admin can reactivate this profile.</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="validity_date" class="form-label small fw-bold">Expiry Date</label>
                            <input type="date" class="form-control rounded-2 bg-light" id="validity_date" name="validity_date" readonly value="<?php echo $member['validity_date']; ?>">
                            <small class="text-muted mt-1 d-block" style="font-size:0.7rem;">To extend validity, use the "Renew Card" action on the Profile page.</small>
                        </div>
                        
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="payment_status" name="payment_status" value="1" <?php echo $member['payment_status'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label small fw-bold text-dark" for="payment_status">
                                    Payment Confirmed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Health Details -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-notes-medical me-2"></i> Medical Information</h5>
                <div class="row g-3">
                    <!-- Blood Group -->
                    <div class="col-md-3">
                        <label for="blood_group" class="form-label small fw-bold">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select rounded-2" id="blood_group" name="blood_group" required>
                            <option value="">Select Group</option>
                            <?php 
                            $bg_options = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                            foreach ($bg_options as $bg) {
                                $selected = ($member['blood_group'] == $bg) ? 'selected' : '';
                                echo "<option value=\"$bg\" $selected>$bg</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Please select the blood group.</div>
                    </div>
                    
                    <!-- Emergency Name -->
                    <div class="col-md-4">
                        <label for="emergency_name" class="form-label small fw-bold">Emergency Contact Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="emergency_name" name="emergency_name" required value="<?php echo htmlspecialchars($member['emergency_name']); ?>">
                        <div class="invalid-feedback">Please specify an emergency contact person.</div>
                    </div>
                    
                    <!-- Emergency Mobile -->
                    <div class="col-md-5">
                        <label for="emergency_mobile" class="form-label small fw-bold">Emergency Contact Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-2" id="emergency_mobile" name="emergency_mobile" required value="<?php echo htmlspecialchars($member['emergency_mobile']); ?>" pattern="[0-9]{10}">
                        <div class="invalid-feedback">Please enter a valid 10-digit number.</div>
                    </div>
                    
                    <!-- Allergies -->
                    <div class="col-md-6">
                        <label for="allergies" class="form-label small fw-bold">Known Allergies</label>
                        <textarea class="form-control rounded-2" id="allergies" name="allergies" rows="3"><?php echo htmlspecialchars($member['allergies'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Medical Conditions -->
                    <div class="col-md-6">
                        <label for="health_info" class="form-label small fw-bold">Existing Medical Conditions</label>
                        <textarea class="form-control rounded-2" id="health_info" name="health_info" rows="3"><?php echo htmlspecialchars($member['health_info'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Repeater Section -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h5 class="text-primary fw-bold mb-0 font-heading"><i class="fa-solid fa-people-roof me-2"></i> Linked Family Members <span class="text-muted small">(Optional)</span></h5>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addFamilyRowBtn">
                        <i class="fa-solid fa-plus me-1"></i> Add Dependent
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="familyTable">
                        <thead class="bg-light">
                            <tr class="small fw-bold">
                                <th>Name</th>
                                <th style="width: 200px;">Relation</th>
                                <th style="width: 200px;">Date of Birth</th>
                                <th style="width: 150px;">Blood Group</th>
                                <th style="width: 80px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="familyTableBody">
                            <?php 
                            $familyRowIndex = 0;
                            if (empty($family_members)): 
                            ?>
                                <tr id="emptyFamilyRow">
                                    <td colspan="5" class="text-center text-muted py-3 small">No family members linked yet. Click "Add Dependent" to add rows.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($family_members as $fam): 
                                    $familyRowIndex++;
                                ?>
                                    <tr class="family-row" id="famRow_<?php echo $familyRowIndex; ?>">
                                        <td>
                                            <input type="text" class="form-control form-control-sm rounded-2" name="family[<?php echo $familyRowIndex; ?>][name]" required value="<?php echo htmlspecialchars($fam['name']); ?>">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm rounded-2" name="family[<?php echo $familyRowIndex; ?>][relation]" required>
                                                <option value="">Select Relation</option>
                                                <option value="Spouse" <?php echo $fam['relation'] == 'Spouse' ? 'selected' : ''; ?>>Spouse</option>
                                                <option value="Son" <?php echo $fam['relation'] == 'Son' ? 'selected' : ''; ?>>Son</option>
                                                <option value="Daughter" <?php echo $fam['relation'] == 'Daughter' ? 'selected' : ''; ?>>Daughter</option>
                                                <option value="Father" <?php echo $fam['relation'] == 'Father' ? 'selected' : ''; ?>>Father</option>
                                                <option value="Mother" <?php echo $fam['relation'] == 'Mother' ? 'selected' : ''; ?>>Mother</option>
                                                <option value="Other" <?php echo $fam['relation'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm rounded-2" name="family[<?php echo $familyRowIndex; ?>][dob]" required value="<?php echo $fam['dob']; ?>" max="<?php echo $today; ?>">
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm rounded-2" name="family[<?php echo $familyRowIndex; ?>][blood_group]" required>
                                                <option value="">Select</option>
                                                <?php 
                                                foreach ($bg_options as $bg) {
                                                    $sel = ($fam['blood_group'] == $bg) ? 'selected' : '';
                                                    echo "<option value=\"$bg\" $sel>$bg</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-family-row-btn" data-row-id="famRow_<?php echo $familyRowIndex; ?>">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Form Submission Button -->
        <div class="col-12 text-end mt-4 mb-5">
            <a href="view-member.php?id=<?php echo htmlspecialchars($member['member_id']); ?>" class="btn btn-outline-secondary rounded-pill px-5 me-2">Cancel</a>
            <button type="submit" class="btn btn-success rounded-pill px-5">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Edit Page JS handlers -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // DOB Age calculate
    $('#dob').on('change', function() {
        const dobVal = $(this).val();
        if (dobVal) {
            const birthDate = new Date(dobVal);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            $('#age').val(age >= 0 ? age : 0);
        }
    });

    // Image preview
    $('#photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Image size exceeds 2MB limit.', 'error');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreviewContainer').html(`<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`);
            }
            reader.readAsDataURL(file);
        }
    });

    // Family Row Repeater
    let familyRowIndex = <?php echo $familyRowIndex; ?>;
    $('#addFamilyRowBtn').click(function() {
        // Hide empty table text indicator
        $('#emptyFamilyRow').hide();
        
        familyRowIndex++;
        const rowHtml = `
            <tr class="family-row" id="famRow_${familyRowIndex}">
                <td>
                    <input type="text" class="form-control form-control-sm rounded-2" name="family[${familyRowIndex}][name]" required placeholder="Enter full name">
                </td>
                <td>
                    <select class="form-select form-select-sm rounded-2" name="family[${familyRowIndex}][relation]" required>
                        <option value="">Select Relation</option>
                        <option value="Spouse">Spouse</option>
                        <option value="Son">Son</option>
                        <option value="Daughter">Daughter</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm rounded-2" name="family[${familyRowIndex}][dob]" required max="<?php echo $today; ?>">
                </td>
                <td>
                    <select class="form-select form-select-sm rounded-2" name="family[${familyRowIndex}][blood_group]" required>
                        <option value="">Select</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle remove-family-row-btn" data-row-id="famRow_${familyRowIndex}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#familyTableBody').append(rowHtml);
    });

    // Remove family row
    $(document).on('click', '.remove-family-row-btn', function() {
        const rowId = $(this).data('row-id');
        $(`#${rowId}`).remove();
        
        if ($('#familyTableBody tr.family-row').length === 0) {
            $('#emptyFamilyRow').show();
        }
    });

    // Edit Form Submit
    $('#editMemberForm').submit(function(e) {
        e.preventDefault();
        
        const form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const formData = new FormData(form);
        showLoader();
        
        $.ajax({
            url: 'ajax/edit-member.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                hideLoader();
                if (response.success) {
                    Swal.fire({
                        title: 'Updated!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = `view-member.php?id=${response.member_id}`;
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                hideLoader();
                Swal.fire('Error', 'Server connection failure.', 'error');
            }
        });
    });
});
</script>
