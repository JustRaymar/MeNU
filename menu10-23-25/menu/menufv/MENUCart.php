<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MENU Cart</title>
    <link rel="stylesheet" href="styles3.css" />
  </head>
  <body>
	<?php
		session_start();
		if (!isset($_SESSION['user_id'])) {
			header("Location: MENULogin.php");
			exit();
		}
	?>
    <header>
		<img src="MenuLOGO.png" alt="Header Image" class="header-image" />
		<a class="header-button" href="MENUHome.php"><p class="header-logout">VIEW MENU</p></a>
		<a href="MENUCart.php" class="active-button"><p class="header-logout">VIEW CART</p></a>
		<a href="MENUCustomerOrders.php" class="header-button"><p class="header-logout">MY ORDERS</p></a>
		<a href="MENUCustomerHistory.php" class="header-button"><p class="header-logout">ORDER HISTORY</p></a>
		<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
    </header>
    <main>
		<center>
			<div class="cart-cart">
				<div id="cart-items"></div>
				<script>
					function clearCart() {
					  sessionStorage.removeItem("cart");
					  renderCart();
					  alert("Cart cleared!");
					}
				</script>
			</div>
			<div class="centralize">
				<div id="cart-items" class="floatl"><h2>Total :</h2><p id="total-price"></p></div>
				<div class="floatr">
					<div class="cart-total">
						<button class="clear-button" id="bigger-button" onclick="clearCart()">Clear Cart</button>
						<form method="POST" id="checkout">
							<input type="hidden" name="cart_data" id="cart_data">
							<button name="btn_checkout" id="bigger-button" class="checkout-button">Proceed To Checkout</button>
						</form>
					</div>
				</div>
			</div>
		</center>
    </main>
    <script src="script.js"></script>
	<script>
		console.log("TOTAL:", calculateTotalPrice());
		document.addEventListener("DOMContentLoaded", function () {
			displayTotalPrice();
		});
	</script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			renderCart();
		});
	</script>

  </body>
</html>

<?php
include("connection.php");

	if (isset($_POST['btn_checkout'])) {
		if (empty($_POST['cart_data'])) {
			echo "<script>alert('Cart is empty!');</script>";
			exit;
		}

		$cartData = json_decode($_POST['cart_data'], true);
		if (empty($cartData)) {
			echo "<script>alert('Cart is empty!');</script>";
			exit;
		}

		$customerId = $_SESSION['user_id'];
		$storeId = $cartData[0]['store_id'];
		$totalPrice = 0;

		// 1️⃣ Create cart
		$cartSQL = "
			INSERT INTO cart (customer_id, total_price, status)
			VALUES ($customerId, 0, 'Pending')
		";
		$con->query($cartSQL);
		$cartId = $con->insert_id;

		foreach ($cartData as $item) {
			$productId = (int)$item['id'];
			$quantity = (int)$item['quantity'];

			// Fetch product safely
			$prodSQL = "
				SELECT unit_price, available_stock
				FROM product
				WHERE product_id = $productId AND store_id = $storeId
			";
			$prodRes = $con->query($prodSQL);
			$product = $prodRes->fetch_assoc();

			if (!$product || $product['available_stock'] < $quantity) {
				echo "<script>alert('Some items are no longer available.');</script>";
				exit;
			}

			$unitPrice = $product['unit_price'];
			$lineTotal = $unitPrice * $quantity;
			$totalPrice += $lineTotal;

			// 2️⃣ Insert cart details
			$detailsSQL = "
				INSERT INTO cart_details
				(cart_id, product_id, quantity, unit_price, total_price, datetime, store_id)
				VALUES
				($cartId, $productId, $quantity, $unitPrice, $lineTotal, NOW(), $storeId)
			";
			$con->query($detailsSQL);

			// 3️⃣ Update stock
			$newStock = $product['available_stock'] - $quantity;
			$updateStockSQL = "
				UPDATE product
				SET available_stock = $newStock
				WHERE product_id = $productId
			";
			$con->query($updateStockSQL);
		}

		// 4️⃣ Update cart total
		$updateCartSQL = "
			UPDATE cart
			SET total_price = $totalPrice
			WHERE cart_id = $cartId
		";
		$con->query($updateCartSQL);

		echo "<script>
			alert('Order placed successfully! Total: ₱$totalPrice');
			sessionStorage.clear();
			window.location.href='MENUCustomerOrders.php';
		</script>";
	}
?>