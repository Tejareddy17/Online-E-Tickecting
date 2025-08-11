<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "<script>alert('Booking cancelled!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error cancelling booking.'); window.location.href='admin_dashboard.php';</script>";
    }
}
?>
