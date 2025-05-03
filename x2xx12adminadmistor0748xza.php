<?php
include 'db_connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: x1xxlogin4807.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: x1xxlogin4807.php");
    exit();
}


// Delete functionality
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    $deleteQuery = "DELETE FROM reservation WHERE id = ?";
    if ($stmt = $conn->prepare($deleteQuery)) {
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
        } else {

        }
        $stmt->close();
    } else {
        echo "<div class='error-message'>Sorgu hazırlanırken hata oluştu: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #f8f8f8;
            border-bottom: 1px solid #ddd;
        }
        .logout-btn {
            background-color: orange;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        h2 {
            margin: 20px;
        }
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .success-message {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        @media (max-width: 600px) {
            table, th, td {
                font-size: 14px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .logout-btn {
                margin-top: 10px;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Rezervasyon listesi</h2>
    <button class="logout-btn" onclick="logout()">Çıkış Yap</button>
</div>

<div class="container">
    <div id="reservation-table"></div>
</div>

<script>
function fetchReservations() {
    fetch('fetch_data.php')
        .then(response => response.json())
        .then(data => {
            const tableContainer = document.getElementById('reservation-table');
            if (data.length > 0) {
                let tableHTML = `<table>
                    <tr>
                        <th>name</th>
                        <th>surname</th>
                        <th>country_code</th>
                        <th>phone</th>
                        <th>from_location</th>
                        <th>to_location</th>
                        <th>reservation_date</th>
                        <th>time</th>
                        <th>vehicle</th>
                        <th>flight_code</th>
                        <th>message</th>
                        <th>created_at</th>
                        <th>actions</th>
                    </tr>`;
                data.forEach(row => {
                    const createdAt = new Date(row.created_at);
                    createdAt.setHours(createdAt.getHours() + 6);
                    const formattedDate = createdAt.toISOString().slice(0, 19).replace('T', ' ');

                    tableHTML += `<tr>
                        <td>${row.name}</td>
                        <td>${row.surname}</td>
                        <td>${row.country_code}</td>
                        <td>${row.phone}</td>
                        <td>${row.from_location}</td>
                        <td>${row.to_location}</td>
                        <td>${row.reservation_date}</td>
                        <td>${row.time}</td>
                        <td>${row.vehicle}</td>
                        <td>${row.flight_code}</td>
                        <td>${row.message}</td>
                        <td>${formattedDate}</td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='delete_id' value='${row.id}'>
                                <button type='submit'>Delete</button>
                            </form>
                        </td>
                    </tr>`;
                });
                tableHTML += `</table>`;
                tableContainer.innerHTML = tableHTML;
            } else {
                tableContainer.innerHTML = "<p>No results found</p>";
            }
        })
        .catch(error => console.error('Error fetching reservation data:', error));
}

// Fetch reservations every 5 seconds
setInterval(fetchReservations, 5000);
fetchReservations(); // Initial fetch

function logout() {
    if (confirm('Çıkış yapmak istediğinize emin misiniz?')) {
        window.location.href = 'x2xx12adminadmistor0748xza.php?logout=true';
    }
}
</script>
</body>
</html>
