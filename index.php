<?php
error_reporting(0);
require_once('config.php');

//session 
session_start();
// Redirect to login page if not logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] != 1) {
	header('Location: login.php');
	exit;
}
//create empty array for cart
if (!isset($_SESSION['tedi'])) {
	$_SESSION['tedi'] = array();
}
//get current exchange rate
if (!isset($_SESSION['exr'])) {
	$url = "https://www.bitstamp.net/api/ticker/";
	$fgc = file_get_contents($url);
	$json = json_decode($fgc, TRUE);
	$price = (int)$json["last"];

	$_SESSION['exr'] = $price;
}

//count items in array
$cartItems = count($_SESSION['tedi']);
$cart = $_SESSION['tedi'];

//add to cart buttons
$queryProducts2 = "SELECT * FROM products WHERE in_stock > 0 ORDER BY id ASC";
$resultH2 = mysqli_query($conn, $queryProducts2) or die("database connection error check server log");
//loop through different product ids
while ($outputsH2 = mysqli_fetch_assoc($resultH2)) {
	if (isset($_POST[$outputsH2['id']])) {
		array_push($_SESSION['tedi'], $outputsH2['id']);
		$cartItems = count($_SESSION['tedi']);
		$cart = $_SESSION['tedi'];
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo $storeName; ?></title>
	<!-- Include Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<div class="container">
		<h1 class="text-center my-4"><?php echo $storeName; ?></h1>

		<div class="row">
			<div class="col-md-4">
				<div class="card mb-4">
					<div class="card-header">Your Cart</div>
					<div class="card-body">
						<?php
						$usdOwed = 0;
						for ($i = 0; $i < $cartItems; $i++) {
							$queryLoopCart = "SELECT * FROM products WHERE id = '$cart[$i]'";
							$doLoopCart = mysqli_query($conn, $queryLoopCart);
							$rowLoopCart = mysqli_fetch_assoc($doLoopCart);
							$loopName = $rowLoopCart['name'];
							$loopPrice = $rowLoopCart['price'];
							$usdOwed += $loopPrice;
							echo $loopName . "<span class='float-right'>$" . $loopPrice . "</span><br>";
						}
						echo "<hr>";
						echo "<strong>Total: $" . $usdOwed . "</strong>";
						?>
						<form action="cart.php" class="mt-3"><input type="submit" class="btn btn-primary" value="View Cart"></form>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<?php
				$queryProducts = "SELECT * FROM products WHERE in_stock > 0 ORDER BY id ASC";
				$resultH = mysqli_query($conn, $queryProducts) or die("error fetching products table");
				while ($outputsH = mysqli_fetch_assoc($resultH)) {
					echo "<div class='card mb-4'>";
					echo "<div class='row no-gutters'>";
					echo "<div class='col-md-4'>";
					echo "<img src='" . $outputsH['image'] . "' class='card-img' alt='" . $outputsH['name'] . "'>";
					echo "</div>";
					echo "<div class='col-md-8'>";
					echo "<div class='card-body'>";
					echo "<h5 class='card-title'>" . $outputsH['name'] . "</h5>";
					echo "<p class='card-text'>" . $outputsH['description'] . "</p>";
					echo "<p class='card-text'><small class='text-muted'>Price: $" . $outputsH['price'] . "</small></p>";
					echo "<form method='post'><input type='submit' class='btn btn-success' value='Add To Cart' name='" . $outputsH['id'] . "'></form>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
				}
				?>
			</div>
		</div>
	</div>

	<!-- Include Bootstrap JS, Popper.js, and jQuery -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>