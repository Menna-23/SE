<?php
session_start();

include("connection.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something is posted
    $email = trim($_POST['email']);
    $password = trim($_POST['Password']);

    if (!empty($email) && !empty($password)) {
        // Use prepared statements to prevent SQL injection
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user_data = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $user_data['password'])) {
                // Password matches
                $_SESSION['user_id'] = $user_data['user_id'];
                if ($user_data['role'] === "admin") {
                    header("Location: ./admin.php");
                    die;
                } else {
                    header("Location: ../index.html");
                    die;
                }
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Please enter valid email and password.";
    }
}
