<?php
include 'db.php';

$bus_id = $_GET['bus_id'];
$journey_date = $_GET['date'];

$booked_query = "SELECT seat_number FROM bookings WHERE bus_id = '$bus_id' AND journey_date = '$journey_date'";
$result = mysqli_query($conn, $booked_query);
$booked = [];

while ($row = mysqli_fetch_assoc($result)) {
    $booked[] = $row['seat_number'];
}

$rows = ['A', 'B', 'C', 'D', 'E'];

echo "<div class='empty'></div><div class='empty'></div><div class='empty'></div><div class='empty'></div>
<div class='seat driver'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256 ...'></path></svg></div>";

foreach ($rows as $row) {
    for ($i = 1; $i <= 2; $i++) {
        $label = $row . $i;
        $class = in_array($label, $booked) ? 'booked' : 'available';
        $click = $class == 'available' ? "onclick=\"toggleSeat('$label')\"" : '';
        echo "<div id='seat-$label' class='seat $class' $click>$label</div>";
    }
    echo '<div class="empty"></div>';
    for ($i = 3; $i <= 4; $i++) {
        $label = $row . $i;
        $class = in_array($label, $booked) ? 'booked' : 'available';
        $click = $class == 'available' ? "onclick=\"toggleSeat('$label')\"" : '';
        echo "<div id='seat-$label' class='seat $class' $click>$label</div>";
    }
}

echo '<div style="grid-column: span 5; display: flex; justify-content: center; gap: 10px;">';
for ($i = 1; $i <= 5; $i++) {
    $label = "F$i";
    $class = in_array($label, $booked) ? 'booked' : 'available';
    $click = $class == 'available' ? "onclick=\"toggleSeat('$label')\"" : '';
    echo "<div id='seat-$label' class='seat $class' $click>$label</div>";
}
echo '</div>';
