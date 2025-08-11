<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['bus_id']) || !isset($_GET['date'])) {
    die("Error: Missing required parameters.");
}

$bus_id = mysqli_real_escape_string($conn, $_GET['bus_id']);
$journey_date = mysqli_real_escape_string($conn, $_GET['date']);
$user_id = $_SESSION['user_id'];

$bus_query = "SELECT * FROM buses WHERE id = '$bus_id'";
$bus_result = mysqli_query($conn, $bus_query);
$bus = mysqli_fetch_assoc($bus_result);

if (!$bus) {
    die("Error: Bus not found.");
}

$booked_query = "SELECT seat_number FROM bookings WHERE bus_id = '$bus_id' AND journey_date = '$journey_date'";
$booked_result = mysqli_query($conn, $booked_query);
$booked_seats = [];

while ($row = mysqli_fetch_assoc($booked_result)) {
    $booked_seats[] = $row['seat_number'];
}

$_SESSION['bus_id'] = $bus_id;
$_SESSION['journey_date'] = $journey_date;
$_SESSION['fare'] = $bus['fare'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Your Seat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dbe9f4, #f2faff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-attachment: fixed;
        }
        .overlay-wrapper {
            background: linear-gradient(to right, #ffffff, #f0f8ff);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .bus-layout {
            display: grid;
            grid-template-columns: repeat(5, 60px);
            gap: 10px;
            justify-content: center;
            margin: auto;
            max-width: 400px;
        }
        .seat {
            width: 60px;
            height: 60px;
            line-height: 60px;
            text-align: center;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
            border: 2px solid #ccc;
            user-select: none;
        }
        .seat:hover:not(.booked):not(.selected) {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }
        .available {
            background-color: #28a745;
            color: white;
        }
        .booked {
            background-color: #dc3545;
            color: white;
            pointer-events: none;
            cursor: not-allowed;
        }
        .selected {
            background-color: #ffc107;
            color: black;
            animation: bounce 0.3s ease;
            transform: scale(1.05);
            border: 2px solid #ff9800;
        }
        @keyframes bounce {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.15); }
            100% { transform: scale(1.05); }
        }
        .driver {
            background-color: #343a40;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            border-radius: 10px;
            pointer-events: none;
        }
        .driver svg {
            width: 30px;
            height: 30px;
        }
        .empty {
            visibility: hidden;
        }
        .legend .box {
            width: 25px;
            height: 25px;
            display: inline-block;
            border-radius: 5px;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="overlay-wrapper">
        <h2 class="text-center mb-4 text-primary">Choose Your Seats</h2>

        <div class="text-center mb-3">
            <label class="fw-bold me-2">Passengers:</label>
            <select id="passengerCount" class="form-select d-inline-block w-auto" onchange="resetSeats()">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <form action="payment.php" method="POST">
            <input type="hidden" name="selected_seats" id="selectedSeats">
            <input type="hidden" name="total_fare" id="totalFareInput">

            <div class="bus-layout mb-4">
                <div class="empty"></div>
                <div class="empty"></div>
                <div class="empty"></div>
                <div class="empty"></div>
                <div class="seat driver">
                    <svg viewBox="0 0 64 64" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="30" stroke="white" stroke-width="4" fill="none"/>
                        <circle cx="32" cy="32" r="6" fill="white"/>
                        <line x1="32" y1="2" x2="32" y2="20" stroke="white" stroke-width="4"/>
                        <line x1="32" y1="44" x2="32" y2="62" stroke="white" stroke-width="4"/>
                        <line x1="2" y1="32" x2="20" y2="32" stroke="white" stroke-width="4"/>
                        <line x1="44" y1="32" x2="62" y2="32" stroke="white" stroke-width="4"/>
                    </svg>
                </div>

                <?php
                $random_red = ['B3', 'C2', 'D1', 'E4']; // Simulated unavailable/maintenance seats
                $rows = ['A', 'B', 'C', 'D', 'E'];
                foreach ($rows as $row) {
                    for ($i = 1; $i <= 2; $i++) {
                        $label = $row . $i;
                        $isBooked = in_array($label, $booked_seats);
                        $isRed = in_array($label, $random_red);
                        $class = ($isBooked || $isRed) ? 'booked' : 'available';
                        $onclick = ($isBooked || $isRed) ? '' : "toggleSeat('$label')";
                        echo "<div id='seat-$label' class='seat $class' onclick=\"$onclick\">$label</div>";
                    }
                    echo '<div class="empty"></div>';
                    for ($i = 3; $i <= 4; $i++) {
                        $label = $row . $i;
                        $isBooked = in_array($label, $booked_seats);
                        $isRed = in_array($label, $random_red);
                        $class = ($isBooked || $isRed) ? 'booked' : 'available';
                        $onclick = ($isBooked || $isRed) ? '' : "toggleSeat('$label')";
                        echo "<div id='seat-$label' class='seat $class' onclick=\"$onclick\">$label</div>";
                    }
                }

                echo '<div style="grid-column: span 5; display: flex; justify-content: center; gap: 10px;">';
                for ($i = 1; $i <= 5; $i++) {
                    $label = "F$i";
                    $isBooked = in_array($label, $booked_seats);
                    $isRed = in_array($label, $random_red);
                    $class = ($isBooked || $isRed) ? 'booked' : 'available';
                    $onclick = ($isBooked || $isRed) ? '' : "toggleSeat('$label')";
                    echo "<div id='seat-$label' class='seat $class' onclick=\"$onclick\">$label</div>";
                }
                echo '</div>';
                ?>
            </div>

            <!-- Legend -->
            <div class="text-center mb-3 legend">
                <span><span class="box available" style="background-color:#28a745;"></span> Available</span>
                <span><span class="box booked" style="background-color:#dc3545;"></span> Booked</span>
                <span><span class="box selected" style="background-color:#ffc107;"></span> Selected</span>
            </div>

            <!-- Fare Info -->
            <div class="text-center">
                <p>Fare per Seat: ₹<span id="farePerSeat" data-fare="<?= $bus['fare'] ?>"><?= number_format($bus['fare'], 2) ?></span></p>
                <p>Total Fare: ₹<span id="totalFare">0.00</span></p>
            </div>

            <button type="submit" class="btn btn-success w-100" id="proceedButton" disabled>Proceed to Payment</button>
        </form>
    </div>
</div>

<script>
    let selectedSeats = [];

    function toggleSeat(label) {
        const seat = document.getElementById("seat-" + label);
        const index = selectedSeats.indexOf(label);
        const max = parseInt(document.getElementById("passengerCount").value);

        if (index > -1) {
            selectedSeats.splice(index, 1);
            seat.classList.remove("selected");
        } else {
            if (selectedSeats.length >= max) {
                alert("You can only select " + max + " seat(s).");
                return;
            }
            selectedSeats.push(label);
            seat.classList.add("selected");
        }

        updateFare();
    }

    function updateFare() {
        const fare = parseFloat(document.getElementById("farePerSeat").dataset.fare);
        const total = fare * selectedSeats.length;

        document.getElementById("selectedSeats").value = JSON.stringify(selectedSeats);
        document.getElementById("totalFare").innerText = total.toFixed(2);
        document.getElementById("totalFareInput").value = total.toFixed(2);
        document.getElementById("proceedButton").disabled = selectedSeats.length === 0;
    }

    function resetSeats() {
        selectedSeats.forEach(label => {
            const seat = document.getElementById("seat-" + label);
            seat.classList.remove("selected");
        });
        selectedSeats = [];
        updateFare();
    }
</script>

</body>
</html>