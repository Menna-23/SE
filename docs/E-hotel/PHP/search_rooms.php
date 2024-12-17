<?php
session_start();
include("connection.php");
include("function.php");
if (isset($_POST['profile'])) {
    if (isset($_SESSION['user_id'])) {
        header("Location: ./profile.php");
        die();
    } else {
        header("Location: ../HTML/sign_in.html");
        exit();
    }
}

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
            AND (r.status = 'available' 
            OR (b.check_out_date < ? OR b.check_in_date > ?))
            LIMIT 1
    ");
        $type_param = "%$type%";
        $stmt->bind_param("sss", $type_param, $check_in_date, $check_out_date);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $room = $result->fetch_assoc();

            $_SESSION['room'] = $room;
            header("Location: averooms.php");
            exit();
        } else {

            $_SESSION['error_message'] = "No rooms avaliable on this type.";
            header("Location: averooms.php");
            exit();
        }
    } else {

        $_SESSION['error_message'] = "Room type is required.";
        header("Location: averooms.php");
        exit();
    }
}
