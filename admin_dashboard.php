<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$result = $conn->query("SELECT bookings.id, users.name, buses.name AS bus_name, bookings.seats_booked 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        JOIN buses ON bookings.bus_id = buses.id");
?>

<h2>Admin Dashboard</h2>

<table border="1">
    <tr>
        <th>User</th>
        <th>Bus</th>
        <th>Seats Booked</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['name']; ?></td>
        <td><?= $row['bus_name']; ?></td>
        <td><?= $row['seats_booked']; ?></td>
        <td>
            <form action="admin_cancel.php" method="post">
                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                <button type="submit">Cancel Booking</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<a href="add_bus.php">Add New Bus</a>
<a href="logout.php">Logout</a>

