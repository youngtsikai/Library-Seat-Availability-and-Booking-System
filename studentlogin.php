<?php
session_start();

// Database connection
$servername = "localhost"; // Change as necessary
$username = "root"; // Change as necessary
$password = ""; // Change as necessary
$dbname = "librarybooking"; // Change as necessary

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regnumber = $_POST['regnumber'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users WHERE regnumber = ? AND password = ?");
    $stmt->bind_param("ss", $regnumber, $password);
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        // User found
        $_SESSION['regnumber'] = $regnumber;
        header("Location: homepage.php"); // Redirect to a welcome page
        exit();
    } else {
        // User not found
        echo "<script>alert('Invalid Credentials!!!');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        button {
            padding: 10px;
            width: 100%;
            height: 40px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #eba336;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form id="StudentLogin" method="POST" action="">
        <label for="regnumber">Registration Number</label>
        <input type="text" id="regnumber" name="regnumber" placeholder="Registration Number" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <br>
        <div class="commands">
        <button type="submit" name="login">Login</button>
        </div>
    </form>
</body>
</html>
