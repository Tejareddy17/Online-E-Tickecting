<?php
// ticket_view.php
include 'db.php';

$booking_id = $_GET['booking_id'] ?? '';
if (!$booking_id) exit('Invalid Booking ID');

$booking_query = mysqli_query($conn, "SELECT b.*, u.full_name, bs.bus_name, bs.bus_type, bs.departure_time 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN buses bs ON b.bus_id = bs.id
    WHERE b.id = '$booking_id'");

$data = mysqli_fetch_assoc($booking_query);
if (!$data) exit('Booking not found');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 30px; }
        .ticket { background: #fff; padding: 20px; border-radius: 12px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .ticket h2 { text-align: center; color: #2e7d32; }
        .detail { margin: 10px 0; }
        .label { font-weight: bold; display: inline-block; width: 130px; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>Booking Confirmed</h2>
        <div class="detail"><span class="label">Booking ID:</span> <?= $data['id'] ?></div>
        <div class="detail"><span class="label">Passenger:</span> <?= htmlspecialchars($data['full_name']) ?></div>
        <div class="detail"><span class="label">Bus Name:</span> <?= htmlspecialchars($data['bus_name']) ?></div>
        <div class="detail"><span class="label">Bus Type:</span> <?= htmlspecialchars($data['bus_type']) ?></div>
        <div class="detail"><span class="label">Departure:</span> <?= htmlspecialchars($data['departure_time']) ?></div>
        <div class="detail"><span class="label">Seats:</span> <?= htmlspecialchars($data['seat_number']) ?></div>
        <div class="detail"><span class="label">Journey Date:</span> <?= htmlspecialchars($data['journey_date']) ?></div>
        <div class="detail"><span class="label">Status:</span> <?= htmlspecialchars($data['booking_status']) ?></div>
    </div>
</body>
</html>
