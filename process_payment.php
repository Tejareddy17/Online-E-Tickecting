<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dummy payment success
    $_SESSION['payment_status'] = "success";
    header("Location: payment_success.php");
    exit();
} else {
    header("Location: payment.php");
    exit();
}
?>
