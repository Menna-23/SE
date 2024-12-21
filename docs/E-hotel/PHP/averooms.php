<?php
session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../HTML/sign_in.html");
    die();}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = trim($_POST['type']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    if (!empty($type) && !empty($check_in) && !empty($check_out)) {
        $check_in_date = date('Y-m-d', strtotime($check_in));
        $check_out_date = date('Y-m-d', strtotime($check_out));

        $stmt = $conn->prepare("
            SELECT r.* 
            FROM rooms r
            LEFT JOIN bookings b ON r.room_id = b.room_id
            WHERE r.type LIKE ? 
            AND (r.status = 'available' )
            LIMIT 1
        ");
        $type_param = "%$type%";
        $stmt->bind_param("s", $type_param);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $room = $result->fetch_assoc();
            $_SESSION['room'] = $room;   
            $_SESSION['check_in'] = $check_in_date; 
            $_SESSION['check_out'] = $check_out_date; 
            header("Location: averooms.php");  
            exit();
        } else {
            $_SESSION['error_message'] = "No rooms available for this type.";
            header("Location: averooms.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Room type, check-in, and check-out dates are required.";
        header("Location: averooms.php");
        exit();
    }
}
    
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

    <form action="averooms.php" class="forma" method="post" onsubmit="return validateDates()">
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
                <option value="Penthouse">Penthouse</option>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" value="Check Availability" name="Check_Availability" class="find-rooms-btn">
        </div>
    </form>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <main>
        <br>
        <br>
        <?php if (isset($_SESSION['room'])): ?>
            <?php $room = $_SESSION['room']; ?>
            <div class="room-container">
                <div class="room">
                    <?php 
                    if ($room['type'] == 'room') {
                        echo '<img src="../assets/room-1.jpg" alt="Room 1">';
                    } elseif ($room['type'] == 'Suite') {
                        echo '<img src="../assets/room-2.jpg" alt="Room 2">';
                    } elseif ($room['type'] == 'Penthouse') {
                        echo '<img src="../assets/room-3.jpg" alt="Room 3">';
                    }
                    ?>

                    <p>Type: <?php echo htmlspecialchars($room['type']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($room['price']); ?> EGP</p> 
                    <form method="post" action="payment.php">
                        <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($_SESSION['check_in']); ?>">
                        <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($_SESSION['check_out']); ?>">
                        <input type="hidden" name="type" value="<?php echo htmlspecialchars($room['type']); ?>">
                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($room['price']); ?>">  
                        <button type="submit" name="pay">Pay Now</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
<?php
if (!isset($_SESSION['refreshed'])) {
    $_SESSION['refreshed'] = true;
} else {
    unset($_SESSION['room']);
    unset($_SESSION['error_message']);
    unset($_SESSION['refreshed']); 
}
?>
    <script src="../avroom.js"></script>
</body>
</html>