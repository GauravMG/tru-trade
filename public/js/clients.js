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