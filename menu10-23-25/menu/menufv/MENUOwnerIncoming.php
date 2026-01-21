<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>MENU Owner Incoming Order</title>
		<link rel="stylesheet" href="styles5.css" />
	</head>
	<body>
	<?php
		session_start();
		include("connection.php");
		if (!isset($_SESSION['user_id'])) {
			header("Location: MENULogin.php");
			exit();
		}
		//echo '<script>alert("Successfully logged in as '.$_SESSION['user_id'].'!");</script>';
		$sqlId = "SELECT * FROM sellers WHERE UserID = ".$_SESSION['user_id'];
		$resId = $con->query($sqlId);
		$rowId = $resId->fetch_assoc();
	?>
    <main>
	<header>
		<img src="MenuLOGO.png" alt="Header Image" class="header-image" />
		<a href="MENUOwner.php" class="header-button"><p class="header-logout">MY STORE PRODUCTS</p></a>
		<a href="MENUOwnerIncoming.php" class="active-button"><p class="header-logout">INCOMING ORDERS</p></a>
		<a href="MENUOwnerHistory.php" class="header-button"><p class="header-logout">ORDER HISTORY</p></a>
		<a href="MENUOwnerStats.php" class="header-button"><p class="header-logout">SALES REPORTS</p></a>
		<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
    </header>
	<br/>
	<center>
		<table border="1px">
			<tr>
				<th colspan=2>INCOMING ORDERS</th>
				<th><button class="closebtn">Close orders</button></th>
			</tr>
			<?php
				include("connection.php");
				
				$sql = "SELECT * FROM orders WHERE Status = 'Pending' ORDER BY Priority DESC, OrderID LIMIT 10";
				$res = $con->query($sql);
				if ($res->num_rows>0) {
					while ($row=$res->fetch_assoc()) {
						$sqlC = "SELECT FName FROM customers WHERE UserID = ".$row['UserID'];
						$resC = $con->query($sqlC);
						$rowC = $resC->fetch_assoc();
						$customer = $rowC['FName'];
						
						$sqlP = "SELECT ProductName FROM products WHERE ProductID = ".$row['ProductID'];
						$resP = $con->query($sqlP);
						$rowP = $resP->fetch_assoc();
						$product = $rowP['ProductName'];
						
						echo "
						<tr class='pending'>
							<td><img style='height: 200px; width: 100%; object-fit: contain;' src='modals/".$product."_r.png'><p class='prodname'>".strtoupper($product)."</p></td>
							<td style='font-size: 30px;'>Order #".$row['OrderID']."<br/>
								x".$row['Quantity']." ₱".$row['OrderPrice']."<br/>
							<td><button class='checkout-button' onclick='openModal()'>Process Order</button></td>
						</tr>";					
					}
				}
			?>
		</table>
		
		<div id="orderModal" class="modal">
			<div class="modal-content">
				<span class="close-modal">&times;</span>
				<div class="order-content">
					<h2>Processing Order...</h2>
					<form id="edit-product-form">
						<img style='height: 400px; width: 100%; object-fit: contain;' src='modals/Pancit Canton_r.png'>
						<table class="receipt">
							<tr><td>Order ID:</td><td>64</td></tr>
							<tr><td>Username:</td><td>asdf</td></tr>
							<tr><td>Product Name:</td><td>Pancit Canton</td></tr>
							<tr><td>Quantity:</td><td>x2</td></tr>
							<tr><td>Total Price:</td><td>₱60</td></tr>
							<tr>
								<td>Non-cash payment</td>
								<td>
									<input type="checkbox" id="myCheckbox">
									<div class="content-to-toggle">
										<input type="text" placeholder="Reference number">
									</div>
								</td>
							</tr>
							<tr>
								<td><button type="button" class="clear-button">Cancel the order</button></td>
								<td><button type="button" class="checkout-button">Submit the order</button></td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
		
		<script>
			// open modal
			function openModal() {
				document.getElementById("orderModal").style.display = "block";
			}

			// close modal
			function closeModal() {
				document.getElementById("orderModal").style.display = "none";
			}

			// close when clicking X
			document.querySelector(".close-modal").onclick = closeModal;

			// close when clicking outside modal
			window.onclick = function(event) {
				const modal = document.getElementById("orderModal");
				if (event.target === modal) {
					closeModal();
				}
			};
		</script>
		
	</center>
	</main>
	</body>
</html>
<?php
	include("connection.php");
	
	if(isset($_POST['btn_updateOrd'])) {
		$oid = $_POST['txt_oid'];
		$sql = "UPDATE orders SET Status = 'Completed', OrderCompleted = '".date('Y-m-d h:i:sa')."' WHERE OrderID =".$oid;
		$res = $con->query($sql);
		if ($con->query($sql) == TRUE) {
			echo "<script>
			alert('Order ".$oid." was completed successfully!');
			window.location.href='MENUOwnerIncoming.php';
			</script>";
		} else {
			echo "<script>
			alert('Order completion error!');
			window.location.href='MENUOwnerIncoming.php';
			</script>";
		}
	}
	
	if(isset($_POST['btn_cancelOrd'])) {
		$oid = $_POST['txt_oid'];
		$sqlX = "UPDATE orders SET Status = 'Cancelled', OrderCompleted = '".date('Y-m-d h:i:sa')."' WHERE OrderID =".$oid;
		$resX = $con->query($sqlX);
		if ($con->query($sqlX) == TRUE) {
			$sqlA = "SELECT Quantity, ProductID FROM orders WHERE OrderID =".$oid;
			$resA = $con->query($sqlA);
			$rowA = $resA->fetch_assoc();
			$quantityA = $rowA['Quantity'];
			$prodId = $rowA['ProductID'];
			
			$sqlB = "SELECT Quantity FROM products WHERE ProductID =".$prodId;
			$resB = $con->query($sqlB);
			$rowB = $resB->fetch_assoc();
			$quantityB = $rowB['Quantity'];
			
			$finalAmount = $quantityA + $quantityB;
			
			$sqlU = "UPDATE products SET Quantity = ".$finalAmount." WHERE ProductID =".$prodId;
			$resU = $con->query($sqlU);
			if ($con->query($sqlU) == TRUE) {
				echo "<script>
				alert('Order ".$oid." was cancelled successfully');
				window.location.href='MENUOwnerIncoming.php';
				</script>";
			} else {
				echo "<script>
				alert('Order cancellation error!');
				window.location.href='MENUOwnerIncoming.php';
				</script>";
			}
		} else {
			echo "<script>
			alert('Order cancellation error!');
			window.location.href='MENUOwnerIncoming.php';
			</script>";
		}
	}
?>