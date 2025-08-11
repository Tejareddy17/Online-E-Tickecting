<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $route = $_POST['route'];
    $seats = $_POST['seats'];

    $stmt = $conn->prepare("INSERT INTO buses (name, route, seats_available) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $route, $seats);

    if ($stmt->execute()) {
        echo "<script>alert('Bus added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding bus.');</script>";
    }
}
?>

<form action="add_bus.php" method="post">
    <input type="text" name="name" placeholder="Bus Name" required>
    <input type="text" name="route" placeholder="Route" required>
    <input type="number" name="seats" placeholder="Seats Available" required>
    <button type="submit">Add Bus</button>
</form>
