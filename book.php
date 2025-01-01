<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the user is logged in and has a regnumber
if (!isset($_SESSION['regnumber'])) {
    echo "You must be logged in to book a seat.";
    exit;
}

// Get the regnumber from the session
$regnumber = $_SESSION['regnumber'];

$successMessage = ''; // Initialize variable for success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $library_id = $_POST['library_id'];
    $seat_number = $_POST['seat_number'];
    $duration = $_POST['duration']; // Get the duration from the form

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Update seat status to 'Reserved'
        $stmt = $pdo->prepare("UPDATE Seats SET status = 'Reserved' WHERE library_id = ? AND seat_number = ?");
        $stmt->execute([$library_id, $seat_number]);

        // Check if seat update was successful
        if ($stmt->rowCount() === 0) {
            throw new Exception("Seat could not be reserved. It may not exist or is already reserved.");
        }

        // Update empty seats count
        $stmt = $pdo->prepare("UPDATE library SET empty_seats = empty_seats - 1 WHERE library_id = ?");
        $stmt->execute([$library_id]);

        // Insert booking into bookings table, including duration
        $booking_date = date('Y-m-d');
        $booking_time = date('H:i:s');
        $stmt = $pdo->prepare("INSERT INTO bookings (regnumber, library_id, seat_number, booking_date, booking_time, duration, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$regnumber, $library_id, $seat_number, $booking_date, $booking_time, $duration]);

        // Check if booking was successful
        if ($stmt->rowCount() === 0) {
            throw new Exception("Booking could not be created.");
        }

        // Commit transaction
        $pdo->commit();

        // Fetch library name for success message
        $library_stmt = $pdo->prepare("SELECT library_name FROM library WHERE library_id = ?");
        $library_stmt->execute([$library_id]);
        $library = $library_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($library) {
            $library_name = htmlspecialchars($library['library_name']);
            $successMessage = "Successfully booked seat $seat_number in $library_name for $duration hour(s). Session begins in 30 minutes.";
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Fetch library ID from GET request
$library_id = $_GET['library_id'];
$stmt = $pdo->prepare("SELECT * FROM Seats WHERE library_id = ? AND status = 'Available'");
$stmt->execute([$library_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch library details
$library_stmt = $pdo->prepare("SELECT * FROM library WHERE library_id = ?");
$library_stmt->execute([$library_id]);
$library = $library_stmt->fetch(PDO::FETCH_ASSOC);

// Check if library details were found
if (!$library) {
    echo "Library not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Seat - <?php echo htmlspecialchars($library['library_name']); ?></title>
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
            background: #2d4452;
            color: beige;
            text-align: center;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 40px;
            margin-left: auto;
            margin-right: auto;
            max-width: 600px;
            padding: 20px;
            background-color: rgba(255,255,255,0.13);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        h1 {
            margin-top: 120px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 500;
            text-align: left;
 }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
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
            <a href="Contact.html">Contact Us</a>
            <div class="lib-dropdown">
                <button class="lib-dropdown-btn"><img src="user.png" alt="user icon" width="26" height="26"></button>
                <div class="lib-dropdown-content">
                    <a href="mybookings.php">Active Bookings</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
    </header>
<h1>Book a Seat</h1>
<?php if ($successMessage): ?>
    <p><?php echo $successMessage; ?></p>
<?php endif; ?>

<div class="form-container">
    <form method="POST">
        <input type="hidden" name="library_id" value="<?php echo $library_id; ?>">
        
        <label for="regnumber">Registration Number:</label>
        <input type="text" id="regnumber" name="regnumber" value="<?php echo htmlspecialchars($regnumber); ?>" readonly>

        <label for="seat_number">Select Seat:</label>
        <select id="seat_number" name="seat_number" required>
            <?php foreach ($seats as $seat): ?>
                <option value="<?php echo htmlspecialchars($seat['seat_number']); ?>">
                    <?php echo htmlspecialchars($seat['seat_number']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="duration">Duration (in hours):</label>
        <input type="number" id="duration" name="duration" min="1" max="4" required>

        <button type="submit">Book Seat</button>
    </form>
</div>

</body>
</html>