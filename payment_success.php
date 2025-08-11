<?php
session_start();
if (!isset($_SESSION['payment_status'])) {
    header("Location: payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #e9f7e9; }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; box-shadow: 0px 0px 10px #ccc; border-radius: 10px; }
        button { background: blue; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 Payment Successful!</h2>
        <p>Thank you for booking your bus ticket.</p>
        <a href="my_bookings.php"><button>View My Bookings</button></a>
    </div>
</body>
</html>
