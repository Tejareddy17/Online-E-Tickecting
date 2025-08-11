<?php
session_start();
include 'db.php';

/**
 * Function to send a structured JSON response
 */
function sendResponse($status, $message, $redirect = null) {
    $response = ["status" => $status, "message" => $message];
    
    if ($redirect) {
        $response["redirect"] = $redirect;
    }

    echo json_encode($response);
    exit();
}

// ✅ Ensure request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse("error", "Invalid request method.");
}

// ✅ Retrieve and sanitize input fields
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// ✅ Validate inputs
if (empty($email) || empty($password)) {
    sendResponse("error", "Email and password are required.");
}

// ✅ Prepare SQL statement to fetch user details
$stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// ✅ Check if user exists
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $full_name, $hashed_password);
    $stmt->fetch();

    // ✅ Verify password
    if (password_verify($password, $hashed_password)) {
        // Secure session handling
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $full_name;

        // ✅ Handle AJAX & normal login paths
        $isAjax = !empty($_POST['ajax']);
        if ($isAjax) {
            sendResponse("success", "Login successful", "dashboard.php");
        } else {
            header("Location: dashboard.php");
            exit();
        }
    } else {
        sendResponse("error", "Invalid credentials.");
    }
} else {
    sendResponse("error", "User not found.");
}

// ✅ Cleanup
$stmt->close();
$conn->close();
?>