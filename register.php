<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='register.php';</script>";
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, dob, age, gender, email, address, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssss", $fullname, $dob, $age, $gender, $email, $address, $phone, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error registering. Please try again.'); window.location.href='register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
