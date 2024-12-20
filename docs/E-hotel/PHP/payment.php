

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../css/payment.css">
</head>

<body>
    <header>
        <h1>Payment</h1>
    </header>
    <form action="process_payment.php" method="post">
        <label for="card">Card Number:</label>
        <input type="text" id="card" name="card" required>
        <label for="expiry">Expiry Date:</label>
        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" required>
        <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>">
        <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($_POST['type'] ?? ''); ?>">
        <button type="submit" name="pay">Pay Now</button>
        
    </form>
</body>

</html>