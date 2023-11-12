<?php
require_once('config.php');

session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["logged"]) || $_SESSION["logged"] !== 1) {
  header("Location: login.php");
  exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect and sanitize input
  $storeName = mysqli_real_escape_string($conn, $_POST['storeName']);
  $storeDescription = mysqli_real_escape_string($conn, $_POST['storeDescription']);
  // ... Include any other store settings you wish to collect

  // SQL to insert new store details
  $sql = "INSERT INTO store (name, description) VALUES ('$storeName', '$storeDescription')";
  // Replace 'store' with your actual table name

  if (mysqli_query($conn, $sql)) {
    $message = "Store setup successful!";
  } else {
    $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Setup Store</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><?php echo $storeName; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="setup_store.php">Setup Store</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin.php">Home</a>
        </li>
        <li class="nav-item">
          <form method="post">
            <button type="submit" class="btn btn-link nav-link" name="logout">Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </nav>
  <div class="container">
    <h2>Setup Your Store</h2>
    <?php if ($message != '') : ?>
      <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="setup_store.php" method="post">
      <div class="form-group">
        <label for="storeName">Store Name:</label>
        <input type="text" class="form-control" id="storeName" name="storeName" required>
      </div>
      <div class="form-group">
        <label for="storeDescription">Store Description:</label>
        <textarea class="form-control" id="storeDescription" name="storeDescription" required></textarea>
      </div>
      <!-- Include any other input fields for additional settings here -->
      <button type="submit" class="btn btn-primary">Setup Store</button>
    </form>
  </div>

  <!-- Include Bootstrap JS, Popper.js, and jQuery -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>