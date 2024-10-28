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