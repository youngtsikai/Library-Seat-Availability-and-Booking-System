<!DOCTYPE html>
<html>
<head>
  <title>Library Seat Availability</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background-color: #2d4452;
    }

    header {
      background-color: rgba(255,255,255,0.13);
      color: #fff;
      padding: 0px 10px;
      display: flex;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }

    .logo {
      max-width: 180px; /* Adjust logo size */
      margin-right: auto; /* Pushes the title to the center */
    }

    .title {
      flex-grow: 1; /* Allows title to take up available space */
      text-align: center;
    }

    .main-content {
      padding: 120px 40px 40px;
      text-align: center;
      color: white;
    }

    .cta-buttons {
      margin-top: 30px;
      display: flex;
      justify-content: center;
    }

    .cta-button {
      background-color: #FFC107;
      border: none;
      color: #333;
      padding: 12px 24px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 0 10px;
      cursor: pointer;
    }

    footer {
      background-color: rgba(255,255,255,0.13);
      color: #fff;
      padding: 10px;
      text-align: center;
      font-size: 14px;
      width: 100%;
      position: absolute;
      bottom: 0;
    }
  </style>
</head>
<body>
  <header>
    <img src="xmain-logo.png" alt="Midlands State Universtiy" class="logo">
    <div class="title"><h1>Library Seat Availability And Booking System</h1></div>
  </header>

  <div class="main-content">
    <h2>Welcome to the Library Seat Booking System</h2>
    <p>Check available seats and make your reservation now.</p>

    <div class="cta-buttons">
      <?php
        // Login as Student button
        echo '<a href="studentlogin.php?role=student" class="cta-button"><b>Login as Student</b></a>';
        
        // Login as Librarian button
        echo '<a href="stafflogin.php?role=librarian" class="cta-button"><b>Login as Librarian</b></a>';
      ?>
    </div>
  </div>

  <footer>
    &copy; 2024 Library Seat Availability And Booking System. All rights reserved.
  </footer>
</body>
</html>