<?php
require('fpdf/fpdf.php');
include 'db.php';

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    $query = $conn->prepare("SELECT u.name AS user_name, b.name AS bus_name, b.route, bk.seats_booked 
                             FROM bookings bk 
                             JOIN users u ON bk.user_id = u.id 
                             JOIN buses b ON bk.bus_id = b.id 
                             WHERE bk.id = ?");
    $query->bind_param("i", $booking_id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'E-Ticket');

        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Name: ' . $row['user_name']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Bus: ' . $row['bus_name']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Route: ' . $row['route']);
        $pdf->Ln(10);
        $pdf->Cell(40, 10, 'Seats: ' . $row['seats_booked']);

        $pdf->Output();
    } else {
        echo "Invalid booking ID.";
    }
}
?>
require('fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Hello, PDF!');
$pdf->Output();

