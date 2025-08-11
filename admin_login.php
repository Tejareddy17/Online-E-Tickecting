<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
    } else {
        echo "<script>alert('Invalid credentials!'); window.location.href='admin_login.php';</script>";
    }
}
?>

<form action="admin_login.php" method="post">
    <input type="text" name="username" placeholder="Admin Username" required>
    <input type="password" name="password" placeholder="Admin Password" required>
    <button type="submit">Login</button>
</form>
