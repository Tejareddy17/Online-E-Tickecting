<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $bus_id = $_POST['bus_id'];
    $seats = $_POST['seats'];

    // Delete booking
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        // Restore seats
        $updateSeats = $conn->prepare("UPDATE buses SET seats_available = seats_available + ? WHERE id = ?");
        $updateSeats->bind_param("ii", $seats, $bus_id);
        $updateSeats->execute();

        echo "<script>alert('Booking cancelled!'); window.location.href='my_bookings.php';</script>";
    } else {
        echo "<script>alert('Error cancelling booking.'); window.location.href='my_bookings.php';</script>";
    }
}
?>
