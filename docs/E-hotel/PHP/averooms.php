
<?php
session_start();

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
    <title>Available Rooms </title>
    <link rel="stylesheet" href="../css/AV_rooms.css">
</head>

<body>
    <header>
 <section class="booking">
    <div class="section__container booking__container">
      <form action="../PHP/search_rooms.php" method="post">
        <div class="form-group">
          <label for="arrival">Arrival Date</label>
          <input type="date" placeholder="Your Arrival Date" name="check_in"/>
        </div>
        <div class="form-group">
          <label for="departure">Departure Date</label>
          <input type="date" placeholder="Your Departure Date" name="check_out" />
        </div>
        <div class="form-group">
          <label for="type">Room Type</label>
          <select placeholder="type of the room" name="type" id="type">
            <option value="room">one bed room</option>
            <option value="Suite">family Suite</option>
            <option value="Penthous">Penthous</option>
          </select>
        </div>
        <input type="submit" value="Check Availability" name="Check_Availability"  class="find-rooms-btn"></button>
      </form>
    </div>
  </section> 

        
    </header>
</body>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link rel="stylesheet" href="../css/room.css">
</head>
<body>
    <br>
    <br>
    <main>
    <?php if ($error_message): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <?php if ($room): ?>
        <div class="room-container">
            <div class="room">
                <img src="room1.jpg" alt="Room 1">
            <p>Type: <?php echo htmlspecialchars($room['type']); ?></p>
            <p>Price: <?php echo htmlspecialchars($room['price']); ?></p>
            <button>Book Now</button>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>




