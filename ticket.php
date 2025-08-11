<?php
session_start();
include 'db.php';

if (!isset($_SESSION['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_SESSION['booking_id'];

$query = "SELECT b.*, bk.seat_number, bk.journey_date FROM bookings bk
          JOIN buses b ON bk.bus_id = b.id
          WHERE bk.id = '$booking_id'";
$result = mysqli_query($conn, $query);
$ticket = mysqli_fetch_assoc($result);

if (!$ticket) {
    die("Error: Ticket not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ticket Confirmation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-success">Payment Successful! Ticket Confirmed</h2>
    <p><strong>Bus Name:</strong> <?= htmlspecialchars($ticket['bus_name']); ?></p>
    <p><strong>Seat Number:</strong> <?= htmlspecialchars($ticket['seat_number']); ?></p>
    <p><strong>Journey Date:</strong> <?= htmlspecialchars($ticket['journey_date']); ?></p>
    <p><strong>Departure:</strong> <?= htmlspecialchars($ticket['departure_time']); ?></p>
    <p><strong>Arrival:</strong> <?= htmlspecialchars($ticket['arrival_time']); ?></p>
    <p><strong>Fare Paid:</strong> ₹<?= htmlspecialchars($ticket['fare']); ?></p>

    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
</div>

</body>
</html>
