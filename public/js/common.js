function capitalizeFirstLetter(str) {
    return str
        .toLowerCase() // Convert the whole string to lowercase to handle mixed case input
        .split(' ') // Split the string into an array of words
        .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize the first letter of each word
        .join(' '); // Join the words back into a single string
}

function formatDate(dateStr) {
    const date = new Date(dateStr)

    const options = {
        weekday: 'short', // 'Mon', 'Tue', etc.
        month: 'short',   // 'Jan', 'Feb', etc.
        day: '2-digit',   // 01, 02, etc.
        year: 'numeric',  // 2024
        hour: '2-digit',  // 01, 02, etc.
        minute: '2-digit', // 00, 01, etc.
        hour12: true      // AM/PM format
    }

    return date.toLocaleString('en-US', options)
}

function formatDateWithoutTime(dateStr) {
    const date = new Date(dateStr)

    const options = {
        weekday: 'short', // 'Mon', 'Tue', etc.
        month: 'short',   // 'Jan', 'Feb', etc.
        day: '2-digit',   // 01, 02, etc.
        year: 'numeric'  // 2024
    }

    return date.toLocaleString('en-US', options)
}

function addKInAmount(amount) {
    return `${amount / 1000}K`
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

function onClickDownloadFile(fileUrl) {
    // Extract the file name from the URL
    const fileName = fileUrl.split('/').pop();

    // Create an invisible anchor element to initiate download
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = fileName; // This sets the filename dynamically
    link.style.display = 'none';

    // Append to the body
    document.body.appendChild(link);

    // Simulate click to trigger download
    link.click();

    // Remove the link after triggering download
    document.body.removeChild(link);
}