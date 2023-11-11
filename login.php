<?php
require_once('config.php');

// Session 
session_start();

if (!isset($_SESSION['lasttime'])) {
  $_SESSION['lasttime'] = 0;
}

$message = '';

if (isset($_POST['submit'])) {
  if ($_SESSION['lasttime'] > 3) {
    die("Too many invalid logins");
  }

  // Collect and sanitize input
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // Fetch user from database
  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $sql);
  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Verify password
    if (password_verify($password, $user['password'])) {
      $_SESSION['logged'] = 1;
      // Redirect to a different page for logged in users
      header('Location: index.php');
      exit;
    } else {
      $message = "Invalid username or password";
      $_SESSION['lasttime']++;
    }
  } else {
    $message = "Invalid username or password";
    $_SESSION['lasttime']++;
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>LogIn</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <h1 class="text-center my-4"><?php echo $storeName; ?></h1>

        <div class="card p-4">
          <h2 class="text-center">User Login</h2>
          <p>Login to access your account.</p>

          <?php if ($message != '') : ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="form-group">
              <label for="username">Username:</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Log In</button>
          </form>

          <div class="mt-3">
            <a href="register.php">Don't have an account? Register here</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Bootstrap JS, Popper.js, and jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>