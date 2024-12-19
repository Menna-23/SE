<?php
session_start();
include("connection.php");
include("function.php");

// Log-out logic
if (isset($_POST['log-out'])) {
    session_destroy();
    header("location: ../index.html");
    die;
}

// Check if early check-out form is submitted
if (isset($_POST['submit_early_checkout'])) {
    if (!empty($_POST['early_date'])) {
        $early_checkout_date = $_POST['early_date'];
        $user_id = getLoggedInUserId();

        // Query to check if the requested early checkout date is valid
        $query = "SELECT check_out_date FROM bookings WHERE user_id = ? AND check_out_date > ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $user_id, $early_checkout_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the early checkout request
            $update_query = "UPDATE bookings 
                             SET early_checkout_date = ?, notification = 'under-Review', request='early_checkout' 
                             WHERE user_id = ? AND check_out_date > ? LIMIT 1";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $early_checkout_date, $user_id, $early_checkout_date);

            if ($stmt->execute()) {
                $_SESSION['notification'] = "Your early checkout request is under review.";
            } else {
                $_SESSION['notification'] = "Error processing your request. Please try again.";
            }
        } else {
            $_SESSION['notification'] = "Invalid early checkout date. Ensure it's before your original check-out date.";
        }
    } else {
        $_SESSION['notification'] = "Please select an early check-out date.";
    }
}

// Get user data
$user_id = getLoggedInUserId();
if ($user_id === null) {
    header("location: ../HTML/sign_in.html");
    exit;
}

$query = "SELECT name, email, phone, age, national_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>
    <header>
        <h1>Profile Page</h1>
        <form action="profile.php" method="POST">
            <button type="submit" name="log-out" class="logout-btn">Logout</button>
        </form>
    </header>

    <div class="container">
        <!-- User Data Section -->
        <div class="section">
            <h2>User Data</h2>
            <ul>
                <li><span>Name:</span> <?= htmlspecialchars($user['name']) ?></li>
                <li><span>Email:</span> <?= htmlspecialchars($user['email']) ?></li>
                <li><span>Phone:</span> <?= htmlspecialchars($user['phone']) ?></li>
                <li><span>Age:</span> <?= htmlspecialchars($user['age']) ?></li>
                <li><span>National ID:</span> <?= htmlspecialchars($user['national_id']) ?></li>
            </ul>
        </div>

        <!-- Booking and History Section -->
        <div class="section">
            <h2>Booking and History</h2>
            <?php $history = fetchUserHistory($conn, $user_id); ?>
            <ul>
                <?php if (!empty($history)): ?>
                    <?php foreach ($history as $row): ?>
                        <li>
                            <span>Action Type:</span> <?= htmlspecialchars($row['action_type']) ?> |
                            <span>Action Date:</span> <?= htmlspecialchars($row['action_date']) ?> |
                            <span>check out date:</span> <?= htmlspecialchars($row['check_out_date'])  ?> |
                            <span>Total Price:</span> <?= htmlspecialchars($row['total_price']) ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No history found.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Early Check-out Request Section -->
        <div class="section">
            <h2>Early Check-out Request</h2>
            <form action="profile.php" method="POST">
                <label for="early_date">Early Check-out Date</label>
                <input type="date" id="early_date" name="early_date" required />
                <button type="submit" name="submit_early_checkout" class="request-btn">Request</button>
            </form>

            <!-- Display Notification -->
            <?php if (isset($_SESSION['notification'])): ?>
                <div class="notification"><?= htmlspecialchars($_SESSION['notification']); ?></div>
                <?php unset($_SESSION['notification']); ?>
            <?php endif; ?>
        </div>

        <!-- Check Request Status Section -->
        <div class="section">
            <h2>Check Request Status</h2>
            <?php
            $status_query = "SELECT notification FROM bookings WHERE user_id = ? LIMIT 1";
            $stmt = $conn->prepare($status_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $status = $result->fetch_assoc();
            echo $status ? htmlspecialchars($status['notification']) : "No request submitted yet.";
            ?>
        </div>
    </div>
</body>

</html>