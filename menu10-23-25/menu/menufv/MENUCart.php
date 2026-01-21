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
					document.addEventListener("DOMContentLoaded", function () {
						let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
						let cartContainer = document.getElementById("cart-items");
						let cartDataInput = document.getElementById("cart_data");

						// Display the cart items correctly
						if (cart.length === 0) {
							cartContainer.innerHTML = "<p>Your cart is empty.</p>";
						} else {
							cartContainer.innerHTML = cart.map(item => 
								`
								<table class="cartitem">
									<tr>
										<td class="citem"><img src="modals/${item.name}_r.png" class="cartimg" /><p class='prodname'>${item.name.toUpperCase()}</p></td>
										<td class="citemtxt">
											<button type="button" id="btnpos" onclick="changeQuantity(1)">+</button>
											<p class="itemtext">x${item.quantity}</p>
											<button type="button" id="btnneg" onclick="changeQuantity(-1)">-</button>
										</td>
										<td class="citemtxt"><p class="itemtext">₱${item.price*item.quantity}</p></td>
									</tr>
								</table>
								`
							).join("");

							// Store cart data in the hidden input
							cartDataInput.value = JSON.stringify(cart);
						}
				
				
					/*document.addEventListener("DOMContentLoaded", function () {
						let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
						let cartContainer = document.getElementById("cart-items");
						let cartDataInput = document.getElementById("cart_data");

						// Display the cart items correctly
						if (cart.length === 0) {
							cartContainer.innerHTML = "<p>Your cart is empty.</p>";
						} else {
							cartContainer.innerHTML = cart.map(item =>
							`<div>
								<img src="modals/${item.name}.png" alt="${item.name}" />
								<p>${item.name} - ${item.quantity}x</p>
							</div>`;
							).join("");

							// Store cart data in the hidden input
							cartDataInput.value = JSON.stringify(cart);
						}*/

						// Handle form submission and ensure cart data is sent
						document.getElementById("checkout").addEventListener("submit", function (event) {
							if (cart.length === 0) {
								alert("Cart is empty!"); 
								event.preventDefault(); // Prevent form submission
							} else {
								// Ensure the hidden field has the cart data before submission
								cartDataInput.value = JSON.stringify(cart);
							}
						});
					});

					function clearCart() {
						sessionStorage.removeItem("cart");
						alert("Cart cleared!");
						location.reload();
					}
					
					function displayTotalPrice() {
						const totalPrice = calculateTotalPrice();
						const totalDisplay = document.getElementById("total-price");

						if (totalDisplay) {
							totalDisplay.innerText = `₱${totalPrice}`;
						} else {
							console.warn("No element with ID 'total-price' found to display total price.");
						}
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
  </body>
</html>

<?php
	include("connection.php");

	$totalPrice = 0; // Initialize total price

	if (isset($_POST['btn_checkout'])) {
		if (!empty($_POST['cart_data'])) {
			$cartData = json_decode($_POST['cart_data'], true);

			if (!empty($cartData)) {
				foreach ($cartData as $item) {
					$productId = $item['id'];
					$quantity = $item['quantity'];
					$userId = $_SESSION['user_id'];

					// Get the price of the product from the database
					$sql = "SELECT * FROM products WHERE ProductID = ".$productId;
					$result = $con->query($sql);
					$row = $result->fetch_assoc();
					$productPrice = $row['Price'];
					$productName = $row['ProductName'];
					
					// Get the priority value of the customer
					$sql2 = "SELECT Priority FROM customers WHERE UserID = ".$userId;
					$result2 = $con->query($sql2);
					$row2 = $result2->fetch_assoc();
					$priority = $row2['Priority'];

					// Calculate the total price
					$orderPrice = ($productPrice * $quantity);
					$totalPrice += ($productPrice * $quantity);
					
					// Final fetch request for available items, if failed, then the cart will clear the item and notify the customer.
					$sqlCheck = "SELECT Quantity FROM products WHERE ProductID = ".$productId;
					$resCheck = $con->query($sqlCheck);
					$rowCheck = $resCheck->fetch_assoc();
					$availability = $rowCheck['Quantity'];
					
					if ($availability >= $quantity) {
						$updQuant = $availability - $quantity;
						// Insert the order into the database
						$insertSQL = "INSERT INTO orders (ProductID, ProductName, Quantity, UserID, Status, OrderPrice, TimeOrdered, Priority) VALUES (".$productId.", '".$productName."', ".$quantity.",
						".$userId.", 'Pending', ".$orderPrice.", '".date('Y-m-d h:i:sa')."', ".$priority.")";
						$updateSQL = "UPDATE products SET Quantity =".$updQuant." WHERE ProductID =".$productId;
						$con->query($insertSQL);
						$con->query($updateSQL);
					} else {
						$totalPrice -= $orderPrice;
						echo "<script>
							alert('Apologies! It seems that the order for ".$quantity."x ".$productName." has failed due to not enough items. The order for this item has been canceled.');
						</script>";
					}
				}
				echo "<script>
					alert('Order placed successfully! Total: ₱".$totalPrice."');
					sessionStorage.clear();
					window.location.href='MENUCart.php';
					</script>";
			} else {
				echo "<script>alert('Cart is empty!');</script>";
			}
		} else {
			echo "<script>alert('Cart is empty!');</script>";
		}
	}
?>

