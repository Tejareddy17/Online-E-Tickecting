<?php
include 'db.php';

if (!isset($_GET['bus_id']) || !isset($_GET['date'])) {
    echo json_encode([]);
    exit;
}

$bus_id = mysqli_real_escape_string($conn, $_GET['bus_id']);
$journey_date = mysqli_real_escape_string($conn, $_GET['date']);

$query = "SELECT seat_number FROM bookings WHERE bus_id = '$bus_id' AND journey_date = '$journey_date'";
$result = mysqli_query($conn, $query);

$booked_seats = [];
while ($row = mysqli_fetch_assoc($result)) {
    $booked_seats[] = $row['seat_number'];
}

echo json_encode($booked_seats);
?>
