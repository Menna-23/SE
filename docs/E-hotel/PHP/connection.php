<?php
// Database configuration
$host = "localhost"; // Database host
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "hotel_management"; // Database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

$sql2 = "
    UPDATE rooms
    SET status = 'available'
    WHERE status = 'booked' AND room_id IN (
        SELECT room_id
        FROM bookings
        WHERE check_out_date < CURDATE() AND status = 'confirmed'
    );
";
$conn->query($sql2);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
