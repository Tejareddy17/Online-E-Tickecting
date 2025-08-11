<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Store token in database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send reset link via email
        $resetLink = "http://localhost/e_ticketing/reset_password.php?token=" . $token;
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com'; // Your email
            $mail->Password = 'your_password'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@example.com', 'E-Ticketing Support');
            $mail->addAddress($email);

            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click this link to reset your password: " . $resetLink;

            $mail->send();
            echo json_encode(["status" => "success", "message" => "Reset link sent to your email"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Email could not be sent"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
    }

    $stmt->close();
    $conn->close();
}
?>
