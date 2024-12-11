<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = trim($_POST['type']);

    if (!empty($type)) {
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE type LIKE ? and status = 'available' LIMIT 1");
        $type_param = "%$type%";
        $stmt->bind_param("s", $type_param);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $room = $result->fetch_assoc();
            // Store room details in the session
            $_SESSION['room'] = $room;
            header("Location: averooms.php");
            exit();
        } else {
            // No room found
            $_SESSION['error_message'] = "No rooms avaliable on this type.";
            header("Location: averooms.php");
            exit();
        }
    } else {
        // Validation error
        $_SESSION['error_message'] = "Room type is required.";
        header("Location: averooms.php");
        exit();
    }
}
?>