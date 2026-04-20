/**
 * Gym Management System — Custom JavaScript
 */

// Initialize DataTables on all .datatable elements
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
        });
    }
});

// SweetAlert2 Delete Confirmation
function confirmDelete(url, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${name}". This cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e94560',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Flash message auto-dismiss
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 4000);

// Show loading spinner
function showSpinner() {
    document.querySelector('.spinner-overlay')?.classList.add('active');
}

function hideSpinner() {
    document.querySelector('.spinner-overlay')?.classList.remove('active');
}

// AJAX helper
function ajaxRequest(url, data, callback) {
    showSpinner();
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            hideSpinner();
            callback(response);
        },
        error: function(xhr) {
            hideSpinner();
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        }
    });
}
