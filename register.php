<?php
include 'config.php'; // Include your DB config file

$message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect and sanitize input
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // Hash the password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insert into database
  $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

  if (mysqli_query($conn, $sql)) {
    $message = "New record created successfully";
  } else {
    $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
  }

  // Close connection
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Register</title>
  <!-- Include Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <h2>Register</h2>
        <?php if ($message != '') : ?>
          <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post">
          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <div class="mt-3">
          <a href="login.php">Already have an account? Log in</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>