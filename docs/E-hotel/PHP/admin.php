<?php
session_start();
include("connection.php");

if (isset($_POST['log-out'])) {
    session_destroy();
    header("location: ../index.html");
    die;
}

// Fetch booking requests to cancel 
$booking_requests_query = "
SELECT 
    rooms.room_number,
    users.name AS user_name,
    bookings.check_out_date,
     bookings.early_checkout_date,
     bookings.booking_id
FROM 
    rooms
INNER JOIN 
    bookings ON bookings.room_id = rooms.room_id
INNER JOIN 
    users ON bookings.user_id = users.user_id
WHERE request = 'early_checkout'";

$booking_requests_result = $conn->query($booking_requests_query);

// Fetch all rooms 
$room_status_query = "SELECT * FROM rooms";
$room_status_result = $conn->query($room_status_query);


$room_status_query_update = "SELECT * FROM rooms where status not like 'booked'";
$room_status_result_update = $conn->query($room_status_query_update );
// Fetch rooms that have check_out date as today
$today = date('Y-m-d');
$rooms_available_today_query = "SELECT r.*
FROM rooms r
INNER JOIN bookings b ON r.room_id = b.room_id 
WHERE b.check_out_date = '$today';";
$rooms_available_today_result = $conn->query($rooms_available_today_query);

if (isset($_POST['accept']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Update check_out_date, reset early_checkout_date, and request
    $approve_query = "UPDATE `bookings`
                      SET total_price = total_price/(check_out_date-check_in_date)*(early_checkout_date-check_in_date) , 
                      `check_out_date` = `early_checkout_date`,
                          `early_checkout_date` = NULL,
                          `request` = 'request',
                          `status` = 'confirmed',
                          `notification` = 'Approved'
                      WHERE `booking_id` = $booking_id";

    if ($conn->query($approve_query) === TRUE) {
        $_SESSION['update_message'] = "Early checkout approved successfully.";
    } else {
        $_SESSION['update_message'] = "Error approving request: " . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['reject']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Reset early_checkout_date and clear the request
    $deny_query = "UPDATE `bookings`
                   SET `early_checkout_date` = NULL,
                       `request` = NULL,
                       `notification` = 'Denied'
                   WHERE `booking_id` = $booking_id";

    if ($conn->query($deny_query) === TRUE) {
        $_SESSION['update_message'] = "Early checkout denied successfully.";
    } else {
        $_SESSION['update_message'] = "Error denying request: " . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_number'], $_POST['new_status'])) {
    $room_number = $_POST['room_number'];
    $new_status = $_POST['new_status'];


    $update_status_query = "UPDATE rooms SET status = '$new_status' WHERE room_number = '$room_number'";
    if ($conn->query($update_status_query) === TRUE) {
        
        $_SESSION['update_message'] = "Room status updated successfully.";
    } else {
        
        $_SESSION['update_message'] = "Error updating room status.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <header>
        <h1>Admin Panel</h1>
        <form action="profile.php" method="POST">
        <button type="submit" name="log-out" class="logout-btn">Logout</button>
</Form>
    </header>

    <main>
        <!-- Requests Section -->
        <section class="requests-section">
            <h2>Booking Requests</h2>
            <?php if ($booking_requests_result->num_rows > 0): ?>
                <?php while ($row = $booking_requests_result->fetch_assoc()): ?>
                    <div class="request">
                        <p><strong>Room_number</strong> <?php echo $row['room_number']; ?></p>
                        <p><strong>Requested By:</strong> <?php echo $row['user_name']; ?></p>
                        <p><strong>current checkout date:</strong> <?php echo $row['check_out_date']; ?></p>
                        <p><strong>new date:</strong> <?php echo $row['early_checkout_date']; ?></p>
                        <form method="POST" action="">
    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
    <button type="submit" class="accept-btn" name="accept">Accept</button>
    <button type="submit" class="reject-btn" name="reject">Reject</button>
</form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No booking requests at the moment.</p>
            <?php endif; ?>
        </section>

        <!-- Room Status Section -->
       <!-- Room Status Section -->
<section class="room-status-section">
    <h2>Room Status</h2>
    <!-- Table to display all rooms and their statuses -->
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($room_status_result->num_rows > 0): ?>
                <?php while ($row = $room_status_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No rooms available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Form to update room status -->
    <h3>Update Room Status</h3>
    <form method="POST" action="">
        <label for="room_number">Select Room Number:</label>
        <select name="room_number" id="room_number" required>
            <?php 
            // Re-fetch room data for the dropdown
            $room_status_result_update = $conn->query($room_status_query_update );
            if ($room_status_result_update->num_rows > 0): 
                while ($row = $room_status_result_update->fetch_assoc()): ?>
                    <option value="<?php echo $row['room_number']; ?>">
                        <?php echo htmlspecialchars($row['room_number']); ?>
                    </option>
                <?php endwhile; 
            else: ?>
                <option>No rooms available</option>
            <?php endif; ?>
        </select>

        <label for="new_status">Change Status to:</label>
        <select name="new_status" id="new_status" required>
            <option value="Available">Available</option>
            <option value="Maintenance">Maintenance</option>
        </select>

        <button type="submit" class="update-status-btn">Update Status</button>
    </form>
</section>


        <!-- Rooms Available Today -->
        <section class="available-today-section">
            <h2>Rooms will be Available Today</h2>
            <table>
                <?php if ($rooms_available_today_result->num_rows > 0): ?>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Status</th>
            </tr>
                </thead>
                    <?php while ($row = $rooms_available_today_result->fetch_assoc()): ?>
                        <tbody>
                        <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        </tbody>
                        <?php endwhile; ?>
                    <table border="1" cellspacing="0" cellpadding="10">
    </table>

                <?php else: ?>
                    <li>No rooms available for today.</li>
                <?php endif; ?>
            
        </section>
    </main>

</body>

</html>

<?php

$conn->close();
?>
