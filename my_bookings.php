<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch user bookings
$query = "SELECT bookings.id, buses.name AS bus_name, routes.from_city, routes.to_city, bookings.seats_booked 
          FROM bookings
          INNER JOIN buses ON bookings.bus_id = buses.id
          INNER JOIN routes ON bookings.route_id = routes.id
          WHERE bookings.user_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query execution failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>My Bookings</h2>
        <table border="1">
            <tr>
                <th>Bus Name</th>
                <th>Route</th>
                <th>Seats Booked</th>
                <th>Action</th>
            </tr>
            <?php while ($booking = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($booking['bus_name']); ?></td>
                    <td><?= htmlspecialchars($booking['from_city']) . " → " . htmlspecialchars($booking['to_city']); ?></td>
                    <td><?= htmlspecialchars($booking['seats_booked']); ?></td>
                    <td><a href="cancel_booking.php?id=<?= $booking['id']; ?>">Cancel</a></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
