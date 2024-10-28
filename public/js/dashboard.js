$(function () {
    $("#dtClosedLeads").DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    })
})

function onClickViewLead(leadId) {
    window.location.href = `/lead/${leadId}`
}

function changeBranch(value) {
    $.ajax({
        url: `/change-branch`,
        method: 'POST',
        data: {
            branchId: value
        },
        success: function (response) {
            if (response.success) {
                window.location.href = "/dashboard"
            } else {
                toastr.error("Failed to change branch.")
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', error)
            toastr.error("An error occurred.")
        }
    })
}