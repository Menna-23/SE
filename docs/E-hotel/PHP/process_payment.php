<?php
session_start();
include("function.php");
include("connection.php"); 


if (!isset($_SESSION['user_id'])) {
    echo "<p>User is not logged in. Please log in first.</p>";
    exit;
}

$userId = $_SESSION['user_id']; 

$userHistory = fetchUserHistory($conn, $userId);

if (!empty($userHistory)) {
    echo "<h3>Your History:</h3>";
    echo "<ul>";
    foreach ($userHistory as $history) {
        echo "<li>";
        echo "Action: " . htmlspecialchars($history['action_type']) . "<br>";
        echo "Date: " . htmlspecialchars($history['action_date']) . "<br>";
        echo "Total Price: $" . htmlspecialchars($history['total_price']);
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No history found.</p>";
}

$check_in = $_SESSION['check_in'] ?? '';
$check_out = $_SESSION['check_out'] ?? '';
$type = $_SESSION['room']['type'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $card = $_POST['card'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if (empty($card) || empty($expiry) || empty($cvv)) {
        echo "<p>Please fill in all fields.</p>";
        exit;
    }

    $query = "SELECT room_id, price FROM rooms WHERE type = ? AND status = 'available' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $roomId = $row['room_id'];
        $pricePerNight = $row['price'];
    } else {
        echo "<p>No available rooms found for this type.</p>";
        exit;
    }

    $checkInDate = date('Y-m-d', strtotime($check_in));
    $checkOutDate = date('Y-m-d', strtotime($check_out));
    $datediff = strtotime($checkOutDate) - strtotime($checkInDate);
    $daysCount = round($datediff / (60 * 60 * 24));  

    $totalPrice = $pricePerNight * $daysCount;

    $storeBookingQuery = "
        INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, total_price) 
        VALUES (?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($storeBookingQuery);
    $stmt->bind_param("iissi", $userId, $roomId, $checkInDate, $checkOutDate, $totalPrice);
    $stmt->execute();

    $bookingId = $stmt->insert_id;

    $updateRoomStatusQuery = "UPDATE rooms SET status = 'booked' WHERE room_id = ? LIMIT 1";
    $stmt = $conn->prepare($updateRoomStatusQuery);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();

    $historyQuery = "
        INSERT INTO history (booking_id, action_date, action_type, amount) 
        VALUES (?, CURRENT_TIMESTAMP, 'payment', ?)
    ";
    $stmt = $conn->prepare($historyQuery);
    $stmt->bind_param("ii", $bookingId, $totalPrice);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: ../index.html");
    exit;
}
?>
