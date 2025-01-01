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
    <title>LSBS | Homepage</title>
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
    margin-top: 130px;
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
        <script>
        // Refresh the page every 20 seconds
        setTimeout(function() {
            location.reload();
        }, 20000); // 20000 milliseconds = 20 seconds
    </script>
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