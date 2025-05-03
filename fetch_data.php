<?php
include 'db_connection.php';

$sql = "SELECT * FROM reservation ORDER BY created_at DESC";
$result = $conn->query($sql);

$reservations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Adjust date and time by adding 7 hours
        $row["created_at"] = date('Y-m-d H:i:s', strtotime($row["created_at"] . ' +7 hours'));
        $reservations[] = $row;
    }
}

echo json_encode($reservations);

$conn->close();
?>
