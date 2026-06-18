/**
 * Lurnixe Health Card System - Admin Main JS
 * June 2026
 */

// Helper to show global spinner
function showLoader() {
    $('#ajaxLoaderModal').modal('show');
}

// Helper to hide global spinner
function hideLoader() {
    // Hide with timeout to prevent modal transition sticking
    setTimeout(function() {
        $('#ajaxLoaderModal').modal('hide');
    }, 300);
}

// SweetAlert2 notification helper
function showToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: icon,
        title: title
    });
}

// AJAX Status Update Handler
function updateMemberStatus(memberId, newStatus) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to change this card status to ${newStatus.toUpperCase()}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#27AE60',
        cancelButtonColor: '#E74C3C',
        confirmButtonText: 'Yes, change it!'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoader();
            $.ajax({
                url: '../admin/ajax/update-status.php',
                type: 'POST',
                data: {
                    member_id: memberId,
                    status: newStatus,
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                dataType: 'json',
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        Swal.fire(
                            'Updated!',
                            response.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoader();
                    Swal.fire('Error', 'Server connection error.', 'error');
                }
            });
        }
    });
}
