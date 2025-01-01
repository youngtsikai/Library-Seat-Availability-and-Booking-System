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
            $successMessage = "Successfully booked seat $seat_number in $library_name for $duration hour(s).";
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for better contrast */
            font-family: 'Arial', sans-serif; /* Clean font */
        }

        .header {
            background-color: #343a40; /* Dark header */
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .container {
            margin-top: 20px;
        }

        .success-message {
            color: #28a745; /* Success message color */
            font-weight: bold;
        }

        .form-control {
            border-radius: 0.5rem; /* Rounded corners for inputs */
        }

        .btn-custom {
            background-color: #007bff; /* Custom button color */
            color: white;
            border-radius: 0.5rem ; /* Rounded corners for button */
        }

        .btn-custom:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Book a Seat in <?php echo htmlspecialchars($library['library_name']); ?></h1>
</div>

<div class="container">
    <form method="POST">
        <input type="hidden" name="library_id" value="<?php echo $library_id; ?>">
        
        <div class="form-group">
            <label for="regnumber">Registration Number:</label>
            <input type="text" id="regnumber" name="regnumber" class="form-control" value="<?php echo htmlspecialchars($regnumber); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="seat_number">Select Seat:</label>
            <select id="seat_number" name="seat_number" class="form-control" required>
                <?php foreach ($seats as $seat): ?>
                    <option value="<?php echo htmlspecialchars($seat['seat_number']); ?>">
                        <?php echo htmlspecialchars($seat['seat_number']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="duration">Duration (in hours):</label>
            <input type="number" id="duration" name="duration" class="form-control" min="1" max="4" required>
        </div>

        <button type="submit" class="btn btn-custom">Book Seat</button>
    </form>

    <?php if ($successMessage): ?>
        <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>