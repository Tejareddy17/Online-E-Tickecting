<?php
session_start();
include 'db.php';

if (!isset($_POST['from']) || !isset($_POST['to']) || empty($_POST['from']) || empty($_POST['to'])) {
    die("Error: Please provide 'from' and 'to' parameters.");
}

$from = mysqli_real_escape_string($conn, $_POST['from']);
$to = mysqli_real_escape_string($conn, $_POST['to']);

$query = "SELECT b.id, b.bus_name, b.bus_type, b.departure_time, b.arrival_time, b.fare, b.available_seats, b.journey_date 
          FROM buses b
          JOIN routes r ON b.route_id = r.id
          WHERE r.from_city = '$from' 
          AND r.to_city = '$to' 
          AND b.journey_date >= '2024-04-01' 
          AND b.journey_date <= '2024-05-31'
          ORDER BY b.journey_date";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Buses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('bus-bg.svg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
            animation: fadeIn 1s ease-in;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            min-height: 100vh;
            padding: 40px 15px;
        }
        .container-custom {
            max-width: 1000px;
            margin: auto;
            background: #ffffffcc;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            animation: slideUp 1s ease-out;
        }
        h3 {
            font-weight: 700;
            margin-bottom: 25px;
            animation: fadeIn 1s ease-in-out;
        }
        .btn-back {
            background-color: #ff6b6b;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s ease-in-out;
        }
        .btn-back:hover {
            transform: scale(1.05);
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table tr {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .table tr:hover {
            background-color: #f2f2f2;
            transform: translateY(-3px);
        }
        .btn-success {
            padding: 6px 12px;
            transition: transform 0.3s ease-in-out;
        }
        .btn-success:hover {
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeIn {
            0% {opacity: 0;}
            100% {opacity: 1;}
        }

        @keyframes slideUp {
            0% {opacity: 0; transform: translateY(20px);}
            100% {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="overlay">
    <div class="container container-custom">
        <h3 class="text-center text-danger">Available Buses</h3>
        <a href="dashboard.php" class="btn-back mb-4 d-inline-block">← Go Back</a>

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Bus Name</th>
                    <th>Bus Type</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Fare (₹)</th>
                    <th>Seats Available</th>
                    <th>Journey Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($bus = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($bus['bus_name']); ?></td>
                            <td><?= htmlspecialchars($bus['bus_type']); ?></td>
                            <td><?= htmlspecialchars($bus['departure_time']); ?></td>
                            <td><?= htmlspecialchars($bus['arrival_time']); ?></td>
                            <td>₹<?= htmlspecialchars($bus['fare']); ?></td>
                            <td><?= htmlspecialchars($bus['available_seats']); ?></td>
                            <td><?= htmlspecialchars($bus['journey_date']); ?></td>
                            <td>
                                <a href="select_seats.php?bus_id=<?= $bus['id']; ?>&date=<?= $bus['journey_date']; ?>" class="btn btn-success">Select Seats</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="8" class="text-danger">No buses available for the selected route.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
