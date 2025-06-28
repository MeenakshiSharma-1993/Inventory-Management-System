<?php 
  include './public/Database/login.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IMS - Inventory Management System</title>
  <link rel="stylesheet" href="public/css/style.css">
  <link 
rel="stylesheet" 
href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/css/bootstrap.min.css">
</head>

<body id="loginBody">
  <div class="overlay">
    <div class="header">
      <h1 class="title">IMS</h1>
      <h2 class="subtitle">INVENTORY MANAGEMENT SYSTEM</h2>
    </div>

    <div class="login-box">
      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="username"><strong>USERNAME</strong></label>
          <input type="text" id="username" name="username" placeholder="username" required class="form-control">
        </div>

        <div class="form-group">
          <label for="password"><strong>PASSWORD</strong></label>
          <input type="password" id="password" name="password" placeholder="password" required class="form-control">
        </div>

        <input type="submit" value="Submit" class="btn btn-primary">
        <?php 
        if ($error != "") 
        { 
          ?>
          <div id="errMsg"><?php 
          echo $error 
          ?></div>
        <?php 
      }
       ?>
      </form>
    </div>
  </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/js/bootstrap.min.js"></script>


<!-- <body id="loginBody">
  <div class="overlay">
    <div class="header">
      <h1 class="title">IMS</h1>
      <h2 class="subtitle">INVENTORY MANAGEMENT SYSTEM</h2>
    </div>

    <div class="login-box">
      <form action="login.php" method="POST">
        <label for="username"><strong>USERNAME</strong></label>
        <input type="text" id="username" name="username" placeholder="username" required>

        <label for="password"><strong>PASSWORD</strong></label>
        <input type="password" id="password" name="password" placeholder="password" required>

        <input type="submit" value="Submit">
        <?php 
        // if ($error != "") 
        // { 
          ?>
          <div id="errMsg"><?php 
          // echo $error 
           ?></div>
        <?php 
      // }
       ?>
      </form>

    </div>
  </div>

  <script src="public/js/dashboard.js"></script>
</body> -->

</html>