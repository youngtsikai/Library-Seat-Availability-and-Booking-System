<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the staff is logged in using username
if (!isset($_SESSION['username'])) {
    echo "You must be logged in as staff to view all bookings.";
    exit;
}

// Handle booking cancellation
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $booking_id = $_POST['cancel_booking_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Fetch the seat number and library_id to update the seats
        $stmt = $pdo->prepare("SELECT seat_number, library_id FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            // Update the seat status back to 'Available'
            $stmt = $pdo->prepare("UPDATE Seats SET status = 'Available' WHERE library_id = ? AND seat_number = ?");
            $stmt->execute([$booking['library_id'], $booking['seat_number']]);

            // Update the booking status to 'cancelled'
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$booking_id]);

            // Commit transaction
            $pdo->commit();
            $message = "Booking cancelled successfully.";
        } else {
            $message = "Booking not found.";
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Handle booking confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_id'])) {
    $booking_id = $_POST['confirm_booking_id'];

    try {
        // Update the booking status to 'confirmed'
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$booking_id]);

        $message = "Booking confirmed successfully.";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle fines
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fine_booking_id'])) {
    $booking_id = $_POST['fine_booking_id'];
    $fine_amount = 1.00; // Set a fixed fine amount

    try {
        // Update the booking status to 'cancelled', set the fine amount, and update fine_status
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled', fine_status = 'fined', fine_amount = ? WHERE id = ?");
        $stmt->execute([$fine_amount, $booking_id]);

        $message = "Booking ID: " . htmlspecialchars($booking_id) . " has been fined and cancelled. Fine amount: $" . htmlspecialchars($fine_amount);
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all bookings based on the selected library
$library_id = isset($_GET['library_id']) ? $_GET['library_id'] : null;

if ($library_id) {
    $stmt = $pdo->prepare("SELECT b.id, b.seat_number, l.library_name, b.booking_date, b.duration, b.status, b.regnumber, b.created_at, b.fine_amount 
                            FROM bookings b 
                            JOIN library l ON b.library_id = l.library_id 
                            WHERE b.library_id = ?");
    $stmt->execute([$library_id]);
} else {
    // Fetch all bookings if no library ID is selected
    $stmt = $pdo->prepare("SELECT b.id, b.seat_number, l.library_name, b.booking_date, b.duration, b.status, b.regnumber, b.created_at, b.fine_amount 
                            FROM bookings b 
                            JOIN library l ON b.library_id = l.library_id");
    $stmt->execute();
}

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Staff View</title>
    <script>
        window.onload = function() {
            <?php if ($message): ?>
                alert("<?php echo addslashes($message); ?>");
            <?php endif; ?>
        };
    </script>
        <style>
        .fine-button {
            display: none; /* Initially hide the fine button */
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
        
        body {
            background-color: #2d4452;
            font-family: 'Poppins', sans-serif;
            color: beige;
        }

        .container {
            width: 95%;
            background-color: rgba(255, 255, 255, 0.13);
            margin: 140px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #106369;
        }

        tr:hover {
            background-color: #faec2a;
        }

        .button {
            background-color: blue;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<header class="lib-header">
    <h2>Library Seat Availability & Booking System</h2>
    <nav class="lib-nav">
        <a href="staffdb.php">Homepage</a>
        <div class="lib-dropdown">
            <button class="lib-dropdown-btn">
                <img src="library.png" alt="library icon" width="26" height="26">
            </button>
            <div class="lib-dropdown-content">
                <a href="?library_id=3">GSBL</a>
                <a href="?library_id=2">Batanai</a>
                <a href="?library_id=1">Main</a>
            </div>
        </div>
    </nav>
</header>
<div class="container">
    <h1>All Bookings</h1>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Seat Number</th>
                <th>Library Name</th>
                <th>Booking Date</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Registration Number</th>
                <th>Fine Amount</th>
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
                <td><?php echo htmlspecialchars($booking['regnumber']); ?></td>
                <td><?php echo htmlspecialchars($booking['fine_amount']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="cancel_booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                        <button type="submit" class="button">Cancel</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="confirm_booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                        <button type="submit" class="button">Confirm</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="fine_booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                        <button type="submit" class="button fine-button">Fine</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>