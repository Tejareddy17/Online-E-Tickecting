<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $conn->prepare("SELECT full_name, email, address, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode(["status" => "success", "user" => $result]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, phone = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $full_name, $email, $address, $phone, $password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $email, $address, $phone, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }

    $stmt->close();
    $conn->close();
}
?>
