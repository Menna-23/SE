<?php 
session_start();
include("connection.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $card = $_POST['card'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Validate inputs
    if (empty($card) || empty($expiry) || empty($cvv)) {
        echo "<p>Please fill in all fields.</p>";
        exit;
    }

    // Redirect to index.html
    header("Location: ../HTML/index.html");
    exit;
}
?>
