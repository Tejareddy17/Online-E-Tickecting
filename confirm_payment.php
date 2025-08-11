<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['booking_id']) || !isset($_POST['upi_reference'])) {
        die("Invalid request.");
    }

    $booking_id = $_POST['booking_id'];
    $upi_reference = $_POST['upi_reference'];

    // Update booking status in the database
    $query = "UPDATE bookings SET payment_status='Paid', upi_reference='$upi_reference' WHERE id='$booking_id'";
    if (mysqli_query($conn, $query)) {
        header("Location: ticket.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error updating payment: " . mysqli_error($conn);
    }
}
?>
