<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT full_name FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
$full_name = $user['full_name'] ?? 'User';

// Fetch available "From" and "To" locations
$from_query = "SELECT DISTINCT from_city FROM routes";
$from_result = mysqli_query($conn, $from_query);

$to_query = "SELECT DISTINCT to_city FROM routes";
$to_result = mysqli_query($conn, $to_query);

// Set today's date for date filtering
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Booking Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('illustration-luxuary-ac-bus-illustration-isolated-generative-ai_945369-29417.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .booking-container {
            width: 100%;
            max-width: 600px;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-weight: 600;
        }
        .search-btn {
            width: 100%;
            background: #dc3545;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .welcome-text {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="overlay">
    <div class="booking-container">
        <div class="welcome-text">
            Welcome, <?= htmlspecialchars($full_name); ?>!
        </div>

        <h3 class="text-center text-danger mb-4">Book Your Bus Ticket</h3>

        <form action="search_buses.php" method="POST">
            <div class="mb-3">
                <label class="form-label">From:</label>
                <select class="form-select" name="from" required>
                    <option value="">-- Select From --</option>
                    <?php while ($from = mysqli_fetch_assoc($from_result)) { ?>
                        <option value="<?= htmlspecialchars($from['from_city']); ?>">
                            <?= htmlspecialchars($from['from_city']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">To:</label>
                <select class="form-select" name="to" required>
                    <option value="">-- Select To --</option>
                    <?php while ($to = mysqli_fetch_assoc($to_result)) { ?>
                        <option value="<?= htmlspecialchars($to['to_city']); ?>">
                            <?= htmlspecialchars($to['to_city']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Journey:</label>
                <input type="date" class="form-control" name="journey_date" min="<?= $today ?>" required>
            </div>

            <button type="submit" class="btn search-btn">Search Buses</button>
        </form>
    </div>
</div>

</body>
</html>
