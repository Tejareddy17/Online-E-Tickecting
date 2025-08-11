<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$rating = $_POST['rating'] ?? 0;
$comment = $_POST['comment'] ?? '';

if ($user_id && $rating) {
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $rating, $comment);
    $stmt->execute();
}
?>
