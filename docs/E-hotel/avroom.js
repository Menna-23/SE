function validateDates() {
    const checkIn = new Date(document.getElementById('check_in').value);
    const checkOut = new Date(document.getElementById('check_out').value);
    const today = new Date();

    if (checkIn < today || checkOut < today) {
        alert('Dates must not be earlier than today.');
        return false;
    }

    const diffInDays = (checkOut - checkIn) / (1000 * 60 * 60 * 24);
    if (diffInDays < 1) {
        alert('Check-out date must be at least one day after check-in date.');
        return false;
    }

    return true;
}