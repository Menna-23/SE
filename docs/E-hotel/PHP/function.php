<?php

function check_login($conn)
{
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];


        $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            return $user_data;
        }
    }


    header("Location: ../HTML/sign_in.html");
    die;
}


function getLoggedInUserId()
{
    if (isset($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }
    header("Location: ../HTML/sign_in.html");
    die;
}


function fetchUserHistory($conn, $user_id)
{
    $history_query = "
        SELECT 
            b.total_price, 
            h.action_type,
            h.action_date,
            b.check_out_date
        FROM bookings b
        LEFT JOIN history h ON b.booking_id = h.booking_id
        WHERE b.user_id = ?
    ";   //(b:Bookings      h:History)

    $stmt = $conn->prepare($history_query);
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    $stmt->close();

    return $history;
}


// Function to store check-in, check-out, and room type into the database 
function storeRoomBooking($checkInDate, $checkOutDate, $roomType, $userId) { 
    include("connection.php"); 
 
    $roomQuery = "SELECT room_id FROM rooms WHERE type = ? LIMIT 1"; 
    $stmtRoom = $conn->prepare($roomQuery); 
    $stmtRoom->bind_param("s", $roomType); 
    $stmtRoom->execute(); 
    $result = $stmtRoom->get_result(); 
    $check_in_date = date('Y-m-d', strtotime($checkInDate));  
    $check_out_date = date('Y-m-d', strtotime($checkOutDate)); 
 
    if ($row = $result->fetch_assoc()) { 
        $roomId = $row['room_id']; 
    } else { 
        echo "Error: Room type not found."; 
        $stmtRoom->close(); 
        $conn->close(); 
        return; 
    } 
    $stmtRoom->close(); 
 
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, 'confirmed')"); 
    $stmt->bind_param("iiss", $userId, $roomId, $check_in_date, $check_out_date); 
 
    if ($stmt->execute()) { 
        echo "Booking information stored successfully."; 
    } else { 
        echo "Error: " . $stmt->error; 
    } 
}
