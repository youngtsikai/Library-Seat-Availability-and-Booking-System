<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['regnumber'])) {
    echo "You must be logged in to view your bookings.";
    exit;
}

// Get the regnumber from the session
$regnumber = $_SESSION['regnumber'];

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = $_POST['cancel_booking_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Fetch the seat number and library_id to update the seats
        $stmt = $pdo->prepare("SELECT seat_number, library_id FROM bookings WHERE id = ? AND regnumber = ?");
        $stmt->execute([$booking_id, $regnumber]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            
            // Update the booking status to 'cancelled'
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND regnumber = ?");
            $stmt->execute([$booking_id, $regnumber]);
            // Commit transaction
            $pdo->commit();
            echo "Booking cancelled successfully.";
            
            // Update the empty seats count in the library table
            $stmt = $pdo->prepare("UPDATE library SET empty_seats = empty_seats + 1 WHERE library_id = ?");
            $stmt->execute([$booking['library_id']]);
        } else {
            echo "Booking not found or you do not have permission to cancel.";
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Fetch user bookings based on regnumber
$stmt = $pdo->prepare("SELECT b.id, b.seat_number, l.library_name, b.booking_date, b.duration, b.status FROM bookings b JOIN library l ON b.library_id = l.library_id WHERE b.regnumber = ?");
$stmt->execute([$regnumber]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings</title>
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
        color: beige;
    }

    *:before,
    *:after {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }

    body {
        text-align: center;
        background-color: #2d4452;
    }

    .container {
        margin-top: 160px;
        margin-left: auto;
        margin-right: auto;
        width: 80%; /* Adjust the width of the container */
        background-color: rgba(255, 255, 255, 0.13);
        padding: 20px; /* Padding inside the container */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional shadow for aesthetics */
        border-radius: 8px; /* Rounded corners */
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
        color: white; /* Header text color */
    }

    tr:nth-child(even) {
            background-color: #106369; /* Zebra striping for rows */
        }

    tr:hover {
        background-color: #ddd; /* Highlight row on hover */
    }

    .button {
        background-color: #28a745; /* Button background color */
        color: white; /* Button text color */
        padding: 8px 12px; /* Button padding */
        border: none; /* Remove border */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor on hover */
    }

    .button:hover {
        background-color: #218838; /* Darker shade on hover */
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
        <a href="homepage.php">Homepage</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact Us</a>
        <div class="lib-dropdown">
            <button class="lib-dropdown-btn">
                <img src="user.png" alt="user icon" width="26" height="26">
            </button>
            <div class="lib-dropdown-content">
                <a href="mybookings.php">Active Bookings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</header>
<div class="container">
    <h1>Your Bookings</h1>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Seat Number</th>
                <th>Library Name</th>
                <th>Booking Date</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['seat_number']); ?></td>
                <td><?php echo htmlspecialchars($booking['library_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['duration']); ?></td>
                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                <td>
                    <?php if ($booking['status'] !== 'cancelled'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="cancel_booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                        <button type="submit" class="button">Cancel</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>