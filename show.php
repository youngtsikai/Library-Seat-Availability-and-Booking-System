<?php
include 'db.php';

// Fetch libraries
$stmt = $pdo->query("SELECT * FROM library");
$libraries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Seat Booking</title>
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

.lib-container {
    display: flex; /* Use Flexbox */
    flex-wrap: wrap; /* Allow wrapping for smaller screens */
    justify-content: center; /* Center items horizontally */
    margin-left: 45px;
}

.library {
    background-color: rgba(255,255,255,0.13);
    border: 2px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    transition: transform 0.3s;
    width: 300px; /* Set a fixed width for each library */
    margin: 10px; /* Add margin for spacing */
}

.library:hover {
    transform: scale(1.05);
}

.library-img {
    width: 100%; /* Make the image responsive */
    height: auto;
    border-radius: 5px; /* Round the corners */
}

    </style>
</head>
<body>
    <header class="lib-header">
        <h2>Library Seat Availability & Booking System</h2>
        <nav class="lib-nav">
            <a href="homepage.html">Homepage</a>
            <a href="about.html">About Us</a>
            <a href="Contact.html">Contact Us</a>
            <div class="lib-dropdown">
                <button class="lib-dropdown-btn"><img src="user.png" alt="user icon" width="26" height="26"></button>
                <div class="lib-dropdown-content">
                    <a href="mybookings.html">Active Bookings</a>
                    <a href="studentdb.html">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
    </header>
    </header>
    <div class="lib-container">
        <?php foreach ($libraries as $library): ?>
            <form action="book.php" method="GET" style="cursor: pointer; flex: 1; margin: 10px;">
                <input type="hidden" name="library_id" value="<?php echo htmlspecialchars($library['library_id']); ?>">
                <div class="library" onclick="this.closest('form').submit();">
                    <img src="<?php echo htmlspecialchars($library['image_path']); ?>" alt="<?php echo htmlspecialchars($library['library_name']); ?>" class="library-img">
                    <h2><?php echo htmlspecialchars($library['library_name']); ?></h2>
                    <p>Empty Seats: <?php echo $library['empty_seats']; ?> out of <?php echo $library['total_seats']; ?></p>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>