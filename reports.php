<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the staff is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in as staff to view reports.";
    exit;
}

// Get the current date
$current_date = date('Y-m-d');

// Query to count total bookings made today
$totalBookingsQuery = $pdo->prepare("SELECT COUNT(*) AS total_bookings FROM Bookings WHERE DATE(booking_time) = ?");
$totalBookingsQuery->execute([$current_date]);
$totalBookings = $totalBookingsQuery->fetch(PDO::FETCH_ASSOC);

// Query to count bookings by library
$libraryBookingsQuery = $pdo->prepare("SELECT library_id, COUNT(*) AS bookings_count FROM Bookings WHERE DATE(booking_time) = ? GROUP BY library_id");
$libraryBookingsQuery->execute([$current_date]);
$libraryBookings = $libraryBookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Query to count bookings by time
$timeBookingsQuery = $pdo->prepare("SELECT HOUR(booking_time) AS hour, COUNT(*) AS bookings_count FROM Bookings WHERE DATE(booking_time) = ? GROUP BY hour ORDER BY hour");
$timeBookingsQuery->execute([$current_date]);
$timeBookings = $timeBookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Function to display reports
function displayReports($totalBookings, $libraryBookings, $timeBookings) {
    ?>
    <div class="container">
        <h2>Daily Booking Reports</h2>

        <h3>Total Bookings Today: <?= htmlspecialchars($totalBookings['total_bookings']) ?></h3>

        <h4>Bookings by Library:</h4>
        <table>
            <thead>
                <tr>
                    <th>Library ID</th>
                    <th>Number of Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libraryBookings as $library): ?>
                    <tr>
                        <td><?= htmlspecialchars($library['library_id']) ?></td>
                        <td><?= htmlspecialchars($library['bookings_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Bookings by Hour:</h4>
        <table>
            <thead>
                <tr>
                    <th>Hour</th>
                    <th>Number of Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeBookings as $time): ?>
                    <tr>
                        <td><?= htmlspecialchars($time['hour']) . ":00" ?></td>
                        <td><?= htmlspecialchars($time['bookings_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Display reports
displayReports($totalBookings, $libraryBookings, $timeBookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        @media screen and (max-width: 400px) {
    .features {
        background-color: #3b679e;
    }
}

@media screen and (max-width: 800px) {
    .features {
        background-color: #7f9bbd;
    }
}

* {

    font-family: 'Poppins', sans-serif;
}

body {
    background: #3b679e;
    justify-content: center;
    color:beige;
}

*:before,
*:after{
padding: 0;
margin: 0;
box-sizing: border-box;
}

body{
    text-align: center;
background-color: #2d4452;
}

        .container {
            margin-top: 140px;
            margin-left: auto;
            margin-right:auto;
            width: 80%; /* Adjust the width of the container */
            background-color: rgba(255,255,255,0.13);
            padding: 20px; /* Padding inside the container */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional shadow for aesthetics */
            border-radius: 8px; /* Rounded corners */
        }

        h2 {
            text-align: center; /* Center the header */
        }

        table {
            width: 100%; /* Full width of the container */
            border-collapse: collapse; /* Collapse borders for better appearance */
            margin-top: 20px; /* Space between header and table */
        }

        th, td {
            border: 1px solid #ccc; /* Set border for table cells */
            padding: 10px; /* Add padding for better spacing */
            text-align: center; /* Center text in cells */
        }

        th {
            background-color: #007BFF; /* Header background color */
            color: white; /* color: white; /* Text color for header */
        }

        tr:nth-child(even) {
            background-color: #106369; /* Zebra striping for rows */
        }

        tr:hover {
            background-color: #e0e0e0; /* Highlight row on hover */
        }
        
        .lib-header {
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 2px 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
        top: 0;
        left: 0;
        position: fixed;
        width: 100%;

    }
    .lib-nav {
        display: flex;
        align-items: center;
        gap: 10px; /* Space between links */
    }
    .lib-nav a {
        color: white;
        text-decoration: none;
        padding: 3px 6px;
        transition: background-color 0.3s;
    }
    .lib-nav a:hover {
        background-color: #106369;
        border-radius: 5px;
        transform: translateY(0);
        opacity: 1;
    }

    .lib-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .lib-dropdown-btn {
        background-color: #106369;
        border-radius: 5px;
        border:none;
        outline: none;
        cursor: pointer;
        padding: 5px 10px;
        transition: background-color 0.3s;
    }
    
    .lib-dropdown-content {
        display: none;
        position: absolute;
        background-color: #106369;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        right: 0; 
        top: 100%; 
    }
    
    .lib-dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    
    .lib-dropdown-content a:hover {
        background-color: #faec2a;
    }
    
    .lib-dropdown:hover .lib-dropdown-content {
        display: block; 
    }
    </style>
</head>
<body>
<header class="lib-header">
        <h2>Library Seat Availability & Booking System</h2>
        <nav class="lib-nav">
          <a href="staffdb.php">Homepage</a>
        </nav>
    </header>
    <!-- The report content will be injected here by the PHP code -->
</body>
</html>