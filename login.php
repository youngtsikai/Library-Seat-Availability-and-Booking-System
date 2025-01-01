<?php
session_start(); // Start the session

// Assume you have already set the user's registration number somewhere after login
// For this example, we will manually set it. In a real application, this would come from your login process.
if (!isset($_SESSION['regnumber'])) {
    $_SESSION['regnumber'] = '12345678'; // Example regnumber (replace with actual login logic)
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['regnumber']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Registration Number</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        #regnumber {
            display: none; /* Initially hide the regnumber */
            margin-top: 20px;
            font-size: 24px;
        }
    </style>
</head>
<body>

<h1>User Registration Number</h1>
<?php if ($isLoggedIn): ?>
    <button id="showRegNumberButton">Show Registration Number</button>
    <div id="regnumber"><?php echo htmlspecialchars($_SESSION['regnumber']); ?></div>
<?php else: ?>
    <p>You are not logged in.</p>
<?php endif; ?>

<script>
    document.getElementById('showRegNumberButton').onclick = function() {
        var regNumberDiv = document.getElementById('regnumber');
        regNumberDiv.style.display = regNumberDiv.style.display === 'none' ? 'block' : 'none';
    };
</script>

</body>
</html>