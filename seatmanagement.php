<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the staff is logged in using username
if (!isset($_SESSION['username'])) {
    echo "You must be logged in as staff to view all seats.";
    exit;
}

// Handle seat release
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['release_seat_number'])) {
    $seat_number = $_POST['release_seat_number'];

    try {
        // Update the seat status back to 'Available'
        $stmt = $pdo->prepare("UPDATE Seats SET status = 'Available' WHERE seat_number = ?");
        $stmt->execute([$seat_number]);

        echo "Seat released successfully.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all seats
$stmt = $pdo->prepare("SELECT seat_number, library_id, status FROM Seats");
$stmt->execute();
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Seats - Staff View</title>
    <script>
    setInterval(function() {
        location.reload(); // Reload the page every 10 seconds
    }, 10000); // 10000 milliseconds = 10 seconds
</script>
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
            width: 80%; /* Adjust the width of the container */
            background-color: rgba(255,255,255,0.13);
            margin-top: 140px;
            margin-left: auto;
            margin-right: auto;
            padding: 20px; /* Padding inside the container */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional shadow for aesthetics */
            border-radius: 8px; /* Rounded corners */
        }

        .table-wrapper {
            overflow-x: auto; /* Enable horizontal scrolling */
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

        button {
            background-color: red; /* Blue background for buttons */
            color: white; /* White text color */
            border: none; /* Remove default border */
            padding: 8px 12px; /* Add padding */
            border-radius: 4px; /* Rounded corners for buttons */
            cursor: pointer; /* Change cursor to pointer on hover */
        }

        button:disabled {
            background-color: #ccc; /* Grey background for disabled buttons */
            cursor: not-allowed; /* Change cursor to not-allowed for disabled buttons */
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
    <div class="container">
        <h2>All Seats</h2>
        <div class="table-wrapper"> <!-- Scrollable container for the table -->
            <table>
                <thead>
                    <tr>
                        <th>Seat Number</th>
                        <th>Library ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($seats as $seat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($seat['seat_number']); ?></td>
                            <td><?php echo htmlspecialchars($seat['library_id']); ?></td>
                            <td><?php echo htmlspecialchars($seat['status']); ?></td>
                            
                            <td>
                            <?php if ($seat['status'] === 'Reserved'): ?>
                            <form method="POST" style="display:inline;">
                              <input type="hidden" name="release_seat_number" value="<?php echo htmlspecialchars($seat['seat_number']); ?>">
                              <button type="submit">Release Seat</button>
                            </form>
                            <?php else: ?>
                              <button disabled>Release Seat</button>
                            <?php endif; ?>
                            </td>
 </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>