<?php
session_start();
include 'db.php';

$razorpay_key = 'rzp_test_YourKeyHere'; // Replace with your Razorpay key

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_seats'])) {
    $_SESSION['selected_seats'] = json_decode($_POST['selected_seats']);
    header("Location: payment.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? null;
$bus_id = $_SESSION['bus_id'] ?? null;
$journey_date = $_SESSION['journey_date'] ?? null;
$selected_seats = $_SESSION['selected_seats'] ?? [];
$fare_per_seat = $_SESSION['fare'] ?? 0;
$total_fare = count($selected_seats) * $fare_per_seat;

if (isset($_POST['pay_now'])) {
    foreach ($selected_seats as $seat_number) {
        $insert = "INSERT INTO bookings (user_id, bus_id, seat_number, journey_date, booking_status)
                   VALUES ('$user_id', '$bus_id', '$seat_number', '$journey_date', 'booked')";
        mysqli_query($conn, $insert);
    }

    $_SESSION['booking_id'] = mysqli_insert_id($conn);
    $_SESSION['payment_success'] = true;

    $user_query = mysqli_query($conn, "SELECT full_name, email FROM users WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($user_query);
    $full_name = $user['full_name'] ?? 'N/A';
    $email = $user['email'] ?? '';

    $subject = "Bus Ticket Confirmation";
    $message = "Dear $full_name,\n\nYour booking is confirmed.\nBooking ID: " . $_SESSION['booking_id'] . "\nSeats: " . implode(", ", $selected_seats) . "\nTotal Fare: ₹" . number_format($total_fare, 2) . "\n\nThanks!";
    $headers = "From: no-reply@e-ticketing.com";
    if (!empty($email)) {
        mail($email, $subject, $message, $headers);
    }

    header("Location: payment.php");
    exit();
}

$payment_success = $_SESSION['payment_success'] ?? false;
if ($payment_success) unset($_SESSION['payment_success']);

$user_query = mysqli_query($conn, "SELECT full_name FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);
$full_name = $user['full_name'] ?? 'N/A';

$bus_query = mysqli_query($conn, "SELECT departure_time, bus_name, bus_type FROM buses WHERE id = '$bus_id'");
$bus = mysqli_fetch_assoc($bus_query);
$departure_time = $bus['departure_time'] ?? 'N/A';
$bus_name = $bus['bus_name'] ?? 'N/A';
$bus_type = $bus['bus_type'] ?? 'N/A';

$qr_data = "upi://pay?pa=receiver@upi&pn=E-Ticketing&am=$total_fare&cu=INR";
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($qr_data) . "&size=200x200";

$verification_qr_data = "Booking ID: " . ($_SESSION['booking_id'] ?? '') . "\n" .
                        "Name: $full_name\n" .
                        "Bus Name: $bus_name\n" .
                        "Bus Type: $bus_type\n" .
                        "Departure Time: $departure_time\n" .
                        "Selected Seats: " . implode(", ", $selected_seats) . "\n" .
                        "Total Fare: ₹" . number_format($total_fare, 2);
$verification_qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($verification_qr_data) . "&size=200x200";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #fce4ec);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .blur-overlay { filter: blur(5px); }

        .success-msg { animation: fadeIn 1s ease forwards; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .btn-animated {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-animated:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .rating {
            direction: rtl;
            display: inline-flex;
            font-size: 2rem;
        }

        .rating input { display: none; }

        .rating label {
            cursor: pointer;
            color: #ccc;
            transition: transform 0.3s;
        }

        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: gold;
            transform: scale(1.2);
        }

        .floating-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
            z-index: 9999;
            text-align: center;
        }

        .payment-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .payment-options label {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border: 2px solid #ccc;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .payment-options input[type="radio"] {
            accent-color: green;
            transform: scale(1.2);
        }

        .payment-options label:hover {
            background: #f1f1f1;
        }

        .icon {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .svg-bus {
            width: 120px;
            animation: moveBus 3s infinite linear;
        }

        @keyframes moveBus {
            0% { transform: translateX(-100px); }
            50% { transform: translateX(100px); }
            100% { transform: translateX(-100px); }
        }
    </style>
</head>
<body>

<div id="mainContent">
    <div class="container mt-5">
        <h2 class="text-center text-success">Payment Page</h2>

        <?php if ($payment_success): ?>
            <div class="alert alert-success text-center success-msg">
                <strong>✅ Payment Successful!</strong> Your booking is confirmed.
            </div>

            <script>
                // Confetti after payment success
                setTimeout(() => confetti(), 500);
            </script>

            <div class="text-center">
                <p><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></p>
                <p><strong>Bus Name:</strong> <?= htmlspecialchars($bus_name) ?></p>
                <p><strong>Bus Type:</strong> <?= htmlspecialchars($bus_type) ?></p>
                <p><strong>Departure Time:</strong> <?= htmlspecialchars($departure_time) ?></p>
                <p><strong>Selected Seats:</strong> <?= implode(", ", $selected_seats) ?></p>
                <p><strong>Total Fare:</strong> ₹<?= number_format($total_fare, 2) ?></p>
                <h5 class="mt-3">Verification QR Code</h5>
                <img src="<?= $verification_qr_url ?>" alt="Verification QR Code" class="mt-2">
                <br><br>
                <button class="btn btn-danger btn-animated" data-bs-toggle="modal" data-bs-target="#feedbackModal">Exit</button>
            </div>
        <?php else: ?>
            <form method="POST">
                <p><strong>Bus Name:</strong> <?= htmlspecialchars($bus_name) ?></p>
                <p><strong>Bus Type:</strong> <?= htmlspecialchars($bus_type) ?></p>
                <p><strong>Selected Seats:</strong> <?= implode(", ", $selected_seats) ?></p>
                <p><strong>Fare per Seat:</strong> ₹<?= number_format($fare_per_seat, 2) ?></p>
                <p><strong>Total Fare:</strong> ₹<?= number_format($total_fare, 2) ?></p>

                <h5 class="text-primary mt-4">Scan to Pay via UPI</h5>
                <div class="text-center mb-3">
                    <img src="<?= $qr_code_url ?>" alt="UPI QR Code">
                </div>

                <div class="payment-options">
                    <label><input type="radio" name="payment_method" value="razorpay" checked> <img src="icons/razorpay.png" class="icon"> Razorpay</label>
                    <label><input type="radio" name="payment_method" value="phonepe"> <img src="icons/phonepe.png" class="icon"> PhonePe</label>
                    <label><input type="radio" name="payment_method" value="gpay"> <img src="icons/gpay.png" class="icon"> Google Pay</label>
                    <label><input type="radio" name="payment_method" value="card"> <img src="icons/card.png" class="icon"> Card</label>
                </div>

                <button type="submit" name="pay_now" class="btn btn-success w-100 btn-animated">Pay Now</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="floating-box" id="floatingBox">
    <svg class="svg-bus" viewBox="0 0 64 32" xmlns="http://www.w3.org/2000/svg">
        <rect width="50" height="20" y="5" x="5" rx="4" ry="4" fill="#2196f3"/>
        <circle cx="15" cy="28" r="4" fill="#424242"/>
        <circle cx="40" cy="28" r="4" fill="#424242"/>
        <rect x="8" y="8" width="10" height="8" fill="#BBDEFB"/>
        <rect x="20" y="8" width="10" height="8" fill="#BBDEFB"/>
        <rect x="32" y="8" width="10" height="8" fill="#BBDEFB"/>
    </svg>
    <h5 class="mt-3">Processing Payment...</h5>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">Rate Your Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="feedbackForm">
                <div class="modal-body text-center">
                    <div class="rating mb-3">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
                            <label for="star<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                    <textarea name="comment" class="form-control" rows="3" placeholder="Any comments?"></textarea>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="redirectHome()">Skip</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>

<script>
function showFloatingBox() {
    document.getElementById('floatingBox').style.display = 'block';
    document.getElementById('mainContent').classList.add('blur-overlay');
}

document.querySelector('form')?.addEventListener('submit', function(e) {
    const method = document.querySelector('input[name="payment_method"]:checked')?.value;
    if (['razorpay', 'card'].includes(method)) {
        e.preventDefault();
        showFloatingBox();
        const options = {
            key: "<?= $razorpay_key ?>",
            amount: <?= $total_fare * 100 ?>,
            currency: "INR",
            name: "E-Ticketing",
            description: "Bus Ticket Payment",
            handler: function () {
                document.querySelector('form').submit();
            },
            prefill: {
                name: "<?= htmlspecialchars($full_name) ?>",
                email: "<?= $email ?? 'test@example.com' ?>"
            },
            theme: { color: "#3399cc" }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    } else {
        showFloatingBox();
    }
});

function redirectHome() {
    window.location.href = "index.php";
}

document.getElementById('feedbackForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const rating = document.querySelector('input[name="rating"]:checked')?.value || 0;
    const comment = this.comment.value;
    fetch('submit_feedback.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'rating=' + rating + '&comment=' + encodeURIComponent(comment)
    });
    alert("Thanks for your feedback!");
    redirectHome();
});
</script>
</body>
</html>