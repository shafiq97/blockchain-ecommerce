<?php
require_once('config.php');

// Session 
session_start();

if (!isset($_SESSION["logged"])) {
	header("Location: index.php");
	exit();
}

// Fetch stores from database
$storeQuery = "SELECT id, name FROM store"; // Adjust this query based on your database schema
$storeResult = mysqli_query($conn, $storeQuery) or die(mysqli_error($conn));
$stores = mysqli_fetch_all($storeResult, MYSQLI_ASSOC);

if (isset($_POST['add'])) {
	$name = mysqli_real_escape_string($conn, $_POST['pname']);
	$cost = mysqli_real_escape_string($conn, $_POST['price']);
	$desc = mysqli_real_escape_string($conn, $_POST['description']);
	$image = mysqli_real_escape_string($conn, $_POST['image']);
	$storeId = mysqli_real_escape_string($conn, $_POST['storeId']); // Get the store ID from the form
	$instock = 1;

	// Ensure cost is not less than 0.01
	if ($cost < 0.01) {
		$cost = 0.01;
	}

	$queryAdd = "INSERT INTO products (name, price, description, image, in_stock, storeId) VALUES ('$name', '$cost', '$desc', '$image', '$instock', '$storeId')";
	$doAdd = mysqli_query($conn, $queryAdd) or die(mysqli_error($conn));
	$message = "New Item Added";
}


$queryOrders2 = "SELECT * FROM orders ORDER BY time DESC LIMIT 10";
$doOrders2 = mysqli_query($conn, $queryOrders2) or die(mysqli_error($conn));
while ($loopOrders2 = mysqli_fetch_assoc($doOrders2)) {
	if (isset($_POST[$loopOrders2['orderid']])) {
		$order_num = $loopOrders2['orderid'];
		$address = $loopOrders2['payto'];
		$getBalance = file_get_contents("https://blockchain.info/q/addressbalance/" . $address . "?confirmations=1");
		$getUnconfirmed = file_get_contents("https://blockchain.info/q/addressbalance/" . $address . "?confirmations=0");
		if ($getBalance > 0) {
			$queryUpdate = "UPDATE orders SET paid = 1, recd = $getBalance WHERE orderid = '$order_num'";
			$doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
			header("Location: admin.php");
		} elseif ($getUnconfirmed > 0) {
			$utxConvert = $getUnconfirmed / 100000000;
			$utxConvert = number_format($utxConvert, 8);
			$message = "Unconfirmed payment pending: " . $utxConvert . "BTC";
		} else {
			$message = "No Payment Yet";
		}
	}
}

if (isset($_POST['logout'])) {
	session_destroy();
	header("Location: login.php");
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>Admin Panel</title>
	<!-- Include Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<!-- Top Navbar -->
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
					<form method="post">
						<button type="submit" class="btn btn-link nav-link" name="logout">Logout</button>
					</form>
				</li>
			</ul>
		</div>
	</nav>

	<div class="container mt-4">
		<h1><?php echo $storeName; ?></h1>
		<?php if (isset($message)) {
			echo "<center>" . $message . "</center>";
		} ?>
		<div id="viewCart">
			<form method="post"><input type="submit" id="logout" name="logout" value="Logout"></form>
			<span id="viewTitle">Recent Orders</span><a href="orders.php">(view all)</a><br>
			<table class="productTable">
				<tr>
					<td class="tableHeader">Order ID</td>
					<td class="tableHeader">Items</td>
					<td class="tableHeader">Amount</td>
					<td class="tableHeader">Paid?</td>
					<td class="tableHeader">Completed?</td>
				</tr>
				<?php
				$queryOrders = "SELECT * FROM orders ORDER BY time DESC LIMIT 10";
				$doOrders = mysqli_query($conn, $queryOrders) or die(mysqli_error($conn));
				while ($loopOrders = mysqli_fetch_assoc($doOrders)) {
					echo "<tr>";
					echo "<td><a href='order.php?id=" . $loopOrders['orderid'] . "'>" . $loopOrders['orderid'] . "</a><form method='post'><input class='checkPmt' type='submit' value='Check For Payment' name='" . $loopOrders['orderid'] . "'></form></td>";
					echo "<td>" . $loopOrders['items'] . "</td>";
					echo "<td>" . $loopOrders['cost'] . "</td>";
					if ($loopOrders['paid'] == 1) {
						$loopPaid = "Yes";
					} else {
						$loopPaid = "No";
					}
					echo "<td>" . $loopPaid . "</td>";
					if ($loopOrders['complete'] == 1) {
						$loopComplete = "Yes";
					} else {
						$loopComplete = "No";
					}
					echo "<td>" . $loopComplete . "</td>";
					echo "</tr>";
				}
				?>
			</table><br><br>
			<span id="viewTitle">Manage Inventory</span><br>
			<table class="productTable">
				<tr>
					<td class="tableHeader">Product ID</td>
					<td class="tableHeader">Name</td>
					<td class="tableHeader">Price</td>
					<td class="tableHeader">Description</td>
					<td class="tableHeader">Image</td>
					<td class="tableHeader">In Stock</td>
					<td class="tableHeader">Manage</td>
				</tr>
				<?php
				$queryProducts = "SELECT * FROM products ORDER BY id ASC";
				$doProducts = mysqli_query($conn, $queryProducts) or die(mysqli_error($conn));
				while ($loopProducts = mysqli_fetch_assoc($doProducts)) {
					echo "<tr>";
					echo "<td>" . $loopProducts['id'] . "</td>";
					echo "<td>" . $loopProducts['name'] . "</td>";
					echo "<td>$" . $loopProducts['price'] . "</td>";
					echo "<td>" . substr($loopProducts['description'], 0, 250) . "</td>";
					echo "<td><img src='" . htmlspecialchars($loopProducts['image']) . "' alt='Product Image' style='width:100px; height:auto;'></td>";
					if ($loopProducts['in_stock'] == 1) {
						$loopStock = "Yes";
					} else {
						$loopStock = "No";
					}
					echo "<td>" . $loopStock . "</td>";
					echo "<td><a href='product.php?item=" . $loopProducts['id'] . "'>Edit/Remove</a></td>";
					echo "<tr>";
				}
				?>
			</table>
			<br><br>
			<span id="viewTitle">Add Item</span><br>
			<b>Product Name</b><br>
			<form method="post">
				<div class="form-group">
					<label for="pname">Product Name</label>
					<input type="text" class="form-control" id="pname" name="pname" required>
				</div>

				<div class="form-group">
					<label for="storeId">Store</label>
					<select name="storeId" class="form-control" id="storeId">
						<?php foreach ($stores as $store) : ?>
							<option value="<?php echo $store['id']; ?>">
								<?php echo htmlspecialchars($store['name']); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-group">
					<label for="price">Price MYR</label>
					<input type="text" class="form-control" id="price" name="price" required>
				</div>

				<div class="form-group">
					<label for="description">Description</label>
					<textarea class="form-control" id="description" name="description" required></textarea>
				</div>

				<div class="form-group">
					<label for="image">Image Link</label>
					<input type="url" class="form-control" id="image" name="image" required>
					<small class="form-text text-muted">Example: http://i.stack.imgur.com/m9uaE.png</small>
				</div>

				<button type="submit" class="btn btn-primary" id="add" name="add">Add Product</button>
			</form>

			<br><br>
		</div>
		<br>
	</div>

	<!-- Include Bootstrap JS, Popper.js, and jQuery -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>