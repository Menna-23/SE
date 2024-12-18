
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../HTML/sign_in.html");
    die();
}
// Fetch room details and error messages from the session
$room = $_SESSION['room'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

// Clear session variables to avoid repeated display
unset($_SESSION['room']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="../css/AV_rooms.css">
</head>

<body>
    
        <form action="../PHP/search_rooms.php" method="post" onsubmit="return validateDates()">
            <div class="form-group">
                <label for="arrival">Arrival Date</label>
                <input type="date" id="check_in" name="check_in" placeholder="Your Arrival Date" required />
            </div>
            <div class="form-group">
                <label for="departure">Departure Date</label>
                <input type="date" id="check_out" name="check_out" placeholder="Your Departure Date" required />
            </div>
            <div class="form-group">
                <label for="type">Room Type</label>
                <select name="type" id="type" required>
                    <option value="" disabled selected>Select Room Type</option>
                    <option value="room">One Bed Room</option>
                    <option value="Suite">Family Suite</option>
                    <option value="Penthous">Penthouse</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" value="Check Availability" name="Check_Availability" class="find-rooms-btn">
            </div>
        </form>
    

    <main>
        <br>
        <br>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if ($room): ?>
            <div class="room-container">
                <div class="room">
                <?php if ($room['type']=='room'): ?>
                    <img src="../assets/room-1.jpg" alt="Room 1">
                    <?php elseif ($room['type']=='Suite'): ?>
                    <img src="../assets/room-2.jpg" alt="Room 2">
                    <?php elseif ($room['type']=='Penthouse'): ?>
                    <img src="../assets/room-3.jpg" alt="Room 3">
                    <?php endif; ?>
                    <p>Type: <?php echo htmlspecialchars($room['type']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($room['price']) ;echo " EGP"; ?></p>
                    <button>Book Now</button>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="../avroom.js"></script>
</body>

</html>



