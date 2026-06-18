<?php
/**
 * Lurnixe Health Card System - Add Member Form
 * June 2026
 */
$page_title = "Register Member";
require_once __DIR__ . '/includes/header.php';

$csrf = get_csrf_token();
$today = date('Y-m-d');
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">New Member Registration</h2>
        <p class="text-muted small">Register a new family card profile and generate a QR code.</p>
    </div>
</div>

<form id="addMemberForm" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    
    <div class="row g-4">
        <!-- 1. Personal & Contact Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-user-check me-2"></i> Personal Information</h5>
                
                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="name" name="name" required placeholder="Enter member full name">
                        <div class="invalid-feedback">Please enter the member's full name.</div>
                    </div>
                    
                    <!-- Gender -->
                    <div class="col-md-6">
                        <label for="gender" class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select rounded-2" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select the gender.</div>
                    </div>
                    
                    <!-- DOB -->
                    <div class="col-md-6">
                        <label for="dob" class="form-label small fw-bold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-2" id="dob" name="dob" required max="<?php echo $today; ?>">
                        <div class="invalid-feedback">Please select the date of birth.</div>
                    </div>
                    
                    <!-- Age (Auto-calculated) -->
                    <div class="col-md-6">
                        <label for="age" class="form-label small fw-bold">Age (Years)</label>
                        <input type="number" class="form-control rounded-2 bg-light" id="age" name="age" readonly value="0">
                    </div>
                    
                    <!-- Mobile -->
                    <div class="col-md-6">
                        <label for="mobile" class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-2" id="mobile" name="mobile" required placeholder="Enter 10-digit mobile number" pattern="[0-9]{10}">
                        <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                    </div>
                    
                    <!-- Alt Mobile -->
                    <div class="col-md-6">
                        <label for="alt_mobile" class="form-label small fw-bold">Alternate Mobile Number</label>
                        <input type="tel" class="form-control rounded-2" id="alt_mobile" name="alt_mobile" placeholder="Enter alternate mobile number" pattern="[0-9]{10}">
                    </div>
                    
                    <!-- Email -->
                    <div class="col-12">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <input type="email" class="form-control rounded-2" id="email" name="email" placeholder="name@domain.com">
                    </div>
                    
                    <!-- Address Line 1 -->
                    <div class="col-12">
                        <label for="address_line1" class="form-label small fw-bold">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="address_line1" name="address_line1" required placeholder="Street address, building name, flat number">
                        <div class="invalid-feedback">Please enter the address.</div>
                    </div>
                    
                    <!-- Address Line 2 -->
                    <div class="col-12">
                        <label for="address_line2" class="form-label small fw-bold">Address Line 2</label>
                        <input type="text" class="form-control rounded-2" id="address_line2" name="address_line2" placeholder="Apartment, suite, unit, landmark">
                    </div>
                    
                    <!-- City -->
                    <div class="col-md-4">
                        <label for="city" class="form-label small fw-bold">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="city" name="city" required placeholder="City">
                        <div class="invalid-feedback">Please specify the city.</div>
                    </div>
                    
                    <!-- State -->
                    <div class="col-md-4">
                        <label for="state" class="form-label small fw-bold">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="state" name="state" required placeholder="State">
                        <div class="invalid-feedback">Please specify the state.</div>
                    </div>
                    
                    <!-- Pincode -->
                    <div class="col-md-4">
                        <label for="pincode" class="form-label small fw-bold">PIN Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="pincode" name="pincode" required placeholder="ZIP / PIN Code" pattern="[0-9]{5,6}">
                        <div class="invalid-feedback">Please enter a valid PIN Code.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 2. Photo Upload & Card Settings Sidebar -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Photo Upload Box -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-camera me-2"></i> Photo Upload</h5>
                        <div class="text-center py-2">
                            <div class="bg-light rounded border mx-auto mb-3 d-flex align-items-center justify-content-center text-muted" id="photoPreviewContainer" style="width: 140px; height: 160px; overflow: hidden;">
                                <i class="fa-solid fa-user-plus fs-1"></i>
                            </div>
                            <label class="btn btn-outline-primary btn-sm rounded-pill px-4" for="photo">
                                <i class="fa-solid fa-upload me-1"></i> Choose File
                            </label>
                            <input type="file" class="form-control d-none" id="photo" name="photo" accept="image/jpeg, image/png">
                            <p class="text-muted small mt-2 mb-0" style="font-size: 0.75rem;">JPG/PNG formats only. Max size 2MB.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card Validity Setting -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <h5 class="text-primary fw-bold mb-3 font-heading border-bottom pb-2"><i class="fa-solid fa-sliders me-2"></i> Card Settings</h5>
                        
                        <div class="mb-3">
                            <label for="validity" class="form-label small fw-bold">Card Validity <span class="text-danger">*</span></label>
                            <select class="form-select rounded-2" id="validity" name="validity" required>
                                <option value="1">1 Year Validity</option>
                                <option value="2">2 Years Validity</option>
                                <option value="3">3 Years Validity</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="validity_date" class="form-label small fw-bold">Card Expiration Date</label>
                            <input type="date" class="form-control rounded-2 bg-light" id="validity_date" name="validity_date" readonly value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                        </div>
                        
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="payment_status" name="payment_status" value="1">
                                <label class="form-check-label small fw-bold text-dark" for="payment_status">
                                    Payment Confirmed
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 3. Health & Medical Information -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-notes-medical me-2"></i> Medical Information</h5>
                <div class="row g-3">
                    <!-- Blood Group -->
                    <div class="col-md-3">
                        <label for="blood_group" class="form-label small fw-bold">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select rounded-2" id="blood_group" name="blood_group" required>
                            <option value="">Select Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                        <div class="invalid-feedback">Please select the blood group.</div>
                    </div>
                    
                    <!-- Emergency Name -->
                    <div class="col-md-4">
                        <label for="emergency_name" class="form-label small fw-bold">Emergency Contact Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="emergency_name" name="emergency_name" required placeholder="Name of relative">
                        <div class="invalid-feedback">Please specify an emergency contact person.</div>
                    </div>
                    
                    <!-- Emergency Mobile -->
                    <div class="col-md-5">
                        <label for="emergency_mobile" class="form-label small fw-bold">Emergency Contact Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control rounded-2" id="emergency_mobile" name="emergency_mobile" required placeholder="10-digit number" pattern="[0-9]{10}">
                        <div class="invalid-feedback">Please enter a valid 10-digit number.</div>
                    </div>
                    
                    <!-- Allergies -->
                    <div class="col-md-6">
                        <label for="allergies" class="form-label small fw-bold">Known Allergies</label>
                        <textarea class="form-control rounded-2" id="allergies" name="allergies" rows="3" placeholder="Food allergies, drug reaction details, etc."></textarea>
                    </div>
                    
                    <!-- Medical Conditions -->
                    <div class="col-md-6">
                        <label for="health_info" class="form-label small fw-bold">Existing Medical Conditions</label>
                        <textarea class="form-control rounded-2" id="health_info" name="health_info" rows="3" placeholder="Diabetes, Hypertension, Heart condition log details..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Family Members Repeatable Section -->
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
                            <tr id="emptyFamilyRow">
                                <td colspan="5" class="text-center text-muted py-3 small">No family members linked yet. Click "Add Dependent" to add rows.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Form Submission Button -->
        <div class="col-12 text-end mt-4 mb-5">
            <a href="members.php" class="btn btn-outline-secondary rounded-pill px-5 me-2">Cancel</a>
            <button type="submit" class="btn btn-success rounded-pill px-5">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Custom javascript for front end form logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Auto calculate age based on Date of Birth selection
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

    // 2. Auto calculate validity expiration date
    $('#validity').on('change', function() {
        const years = parseInt($(this).val());
        const start = new Date();
        start.setFullYear(start.getFullYear() + years);
        
        const yyyy = start.getFullYear();
        let mm = start.getMonth() + 1; // Months start at 0
        let dd = start.getDate();
        
        if (mm < 10) mm = '0' + mm;
        if (dd < 10) dd = '0' + dd;
        
        $('#validity_date').val(`${yyyy}-${mm}-${dd}`);
    });

    // 3. Photo Upload file preview handling
    $('#photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Check file size (max 2MB)
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

    // 4. Repeatable Family Members Rows handler
    let familyRowIndex = 0;
    $('#addFamilyRowBtn').click(function() {
        // Remove empty row indicator
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

    // Remove Family member row
    $(document).on('click', '.remove-family-row-btn', function() {
        const rowId = $(this).data('row-id');
        $(`#${rowId}`).remove();
        
        // Show empty row indicator if all removed
        if ($('#familyTableBody tr.family-row').length === 0) {
            $('#emptyFamilyRow').show();
        }
    });

    // 5. AJAX Form submit handler
    $('#addMemberForm').submit(function(e) {
        e.preventDefault();
        
        const form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const formData = new FormData(form);
        showLoader();
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>admin/ajax/add-member.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                hideLoader();
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#27AE60',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Open Card PDF download in a new tab
                        window.open(`generate-card.php?id=${response.member_id}`, '_blank');
                        // Redirect current window to members list
                        window.location.href = 'members.php';
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                console.error("AJAX Error Details: Status=" + status + ", Error=" + error + ", Response=" + xhr.responseText);
                Swal.fire('Error', 'Server connection failure.', 'error');
            }
        });
    });
});
</script>
