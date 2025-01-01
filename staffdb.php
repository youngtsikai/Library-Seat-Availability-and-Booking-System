<?php
// Database configuration
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "librarybooking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total bookings
$totalBookingsQuery = "SELECT COUNT(*) as total FROM bookings";
$totalBookingsResult = $conn->query($totalBookingsQuery);
$totalBookings = $totalBookingsResult->fetch_assoc()['total'];

// Fetch total and available seats
$totalSeatsQuery = "SELECT SUM(total_seats) as total, SUM(empty_seats) as available FROM library";
$totalSeatsResult = $conn->query($totalSeatsQuery);
$totalSeatsData = $totalSeatsResult->fetch_assoc();
$totalSeats = $totalSeatsData['total'];
$availableSeats = $totalSeatsData['available'];


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
    setInterval(function() {
        location.reload(); // Reload the page every 10 seconds
    }, 10000); // 10000 milliseconds = 10 seconds
</script>
    <style>
        /* Ensure the sidebar takes full height */
        .sidebar {
            height: 100vh; /* Full height of the viewport */
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%; /* Ensure the body takes full height */
        }

        /* Additional styles for the sidebar if needed */
        .sidebar-sticky {
            position: sticky;
            top: 0; /* Stick to the top */
            overflow-y: auto; /* Allow scrolling if content overflows */
        }
    /* Change text color of nav links to white */
    .sidebar .nav-link {
        color: white;
    }

    /* Add margin and border to space out items and add separators */
    .sidebar .nav-item {
        margin-top: 3rem;
        margin-bottom: 1rem; /* Adjust spacing between items */
        border-bottom: 4px solid whitesmoke; /* Add separator line */
    }

    /* Optional: Change the color of the separator line */
    .sidebar .nav-item:last-child {
        border-bottom: none; /* Remove border from the last item */
    }

    /* Optional: Change the active link color to distinguish it */
    .sidebar .nav-link.active {
        color: #00d4b2; /* Customize active link color */
    }

    /* Optional: Change the hover color of links */
    .sidebar .nav-link:hover {
        color: #ff9900; /* Hover effect */
    }

    /* Optional: Adjust font size and weight for a cleaner look */
    .sidebar .nav-link {
        font-size: 16px;
        font-weight: 500;
    }

    h1 {
    margin-top: 50px;
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
    <div class="container-fluid">
        <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="seatmanagement.php">
                    <i class="fas fa-chair"></i> Seat Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="bookingmanagement.php">
                    <i class="fas fa-calendar-check"></i> Booking Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    </nav>
            <main role="main "class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1>Dashboard</h1>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">Total Bookings</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $totalBookings; ?></h5>
                                <p class="card-text">Total number of bookings made.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-header">Available Seats</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $availableSeats; ?></h5>
                                <p class="card-text">Number of seats currently available.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Total Seats</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $totalSeats; ?></h5>
                                <p class="card-text">Total number of seats in the library.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>