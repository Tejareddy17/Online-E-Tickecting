<?php
include 'db.php';

if (isset($_GET['from'])) {
    $from_city = mysqli_real_escape_string($conn, $_GET['from']);

    $query = "SELECT DISTINCT to_city FROM routes WHERE from_city = '$from_city'";
    $result = mysqli_query($conn, $query);

    $destinations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $destinations[] = $row;
    }

    echo json_encode($destinations);
}
?>
