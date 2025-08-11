<?php
session_start();
include 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch available "From" locations
$from_query = "SELECT DISTINCT from_city FROM routes";
$from_result = mysqli_query($conn, $from_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        select, input, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .payment-box {
            display: none;
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
            background: #f9f9f9;
            border-radius: 8px;
            transition: all 0.4s ease-in-out;
        }
        .error-message {
            color: red;
            font-weight: bold;
            display: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Book Your Ticket</h2>
        <p id="error-message" class="error-message"></p>

        <form id="bookingForm" action="process_payment.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" id="name" required>

            <label>Phone Number:</label>
            <input type="tel" name="phone" id="phone" required>

            <label>Email:</label>
            <input type="email" name="email" id="email" required>

            <label>From:</label>
            <select name="from" id="from" required>
                <option value="">-Select-</option>
                <?php while ($from = mysqli_fetch_assoc($from_result)) { ?>
                    <option value="<?= htmlspecialchars($from['from_city']); ?>"><?= htmlspecialchars($from['from_city']); ?></option>
                <?php } ?>
            </select>

            <label>To:</label>
            <select name="to" id="to" required>
                <option value="">-Select-</option>
            </select>

            <label>Departure Date:</label>
            <input type="date" name="departure_date" id="departure_date" required>

            <label>Number of Passengers:</label>
            <input type="number" name="seats" id="seats" min="1" required>

            <label>
                <input type="checkbox" id="confirmDetails" required> Confirm your details
            </label>

            <button type="button" id="showPayment">Proceed to Payment</button>

            <div class="payment-box fade-in" id="paymentBox">
                <h3>Payment Options</h3>
                <label><input type="radio" name="payment_method" value="Credit Card" required> Credit Card</label><br>
                <label><input type="radio" name="payment_method" value="PayPal" required> PayPal</label><br>
                <label><input type="radio" name="payment_method" value="Net Banking" required> Net Banking</label><br>
                <button type="submit">Confirm Payment</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("from").addEventListener("change", function() {
            let from = this.value;
            let toDropdown = document.getElementById("to");

            if (from) {
                fetch(fetch_destinations.php?from=${encodeURIComponent(from)})
                    .then(response => response.json())
                    .then(data => {
                        toDropdown.innerHTML = '<option value="">-Select-</option>';
                        data.forEach(route => {
                            let option = document.createElement("option");
                            option.value = route.to_city;
                            option.textContent = route.to_city;
                            toDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching destinations:', error));
            } else {
                toDropdown.innerHTML = '<option value="">-Select-</option>';
            }
        });

        document.getElementById("showPayment").addEventListener("click", function() {
            let name = document.getElementById("name").value;
            let phone = document.getElementById("phone").value;
            let email = document.getElementById("email").value;
            let from = document.getElementById("from").value;
            let to = document.getElementById("to").value;
            let date = document.getElementById("departure_date").value;
            let seats = document.getElementById("seats").value;
            let confirm = document.getElementById("confirmDetails").checked;
            let errorMessage = document.getElementById("error-message");

            if (!name || !phone || !email || !from || !to || !date || seats < 1 || !confirm) {
                errorMessage.style.display = "block";
                errorMessage.textContent = "Please fill all the fields correctly and confirm your details!";
                return;
            }

            errorMessage.style.display = "none";
            document.getElementById("paymentBox").style.display = "block";
        });
    </script>
</body>
</html>