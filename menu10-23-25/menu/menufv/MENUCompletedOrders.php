<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>MENU Seller Recent Orders</title>
		<link rel="stylesheet" href="styles4.css" />
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
		<a href="MENUSeller.php" class="header-button"><p class="header-logout">EDIT PRODUCTS</p></a>
		<a href="MENUOrders.php" class="header-button"><p class="header-logout">INCOMING ORDERS</p></a>
		<a href="MENUCompletedOrders.php" class="active-button"><p class="header-logout">RECENT ORDERS</p></a>
		<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
    </header>
	<center>
		<table border="1px">
			<tr>
				<th colspan=6>RECENT TRANSACTIONS</th>
			</tr>
			<?php
				include("connection.php");
				
				$limit = 5;  // Number of entries to show in a page.
				// Look for a GET variable page if not found default is 1.     
				if (isset($_GET["page"])) { 
				  $pagen  = $_GET["page"]; 
				} 
				else { 
				  $pagen=1; 
				};  

				$start = ($pagen-1) * $limit;  
				
				$sql = "SELECT * FROM orders WHERE Status = 'Completed' OR Status = 'Cancelled' LIMIT ".$start.",".$limit." ";
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
						
						if ($row['Status'] == 'Completed') {
								echo"<tr class='complete'>";
							} else {
								if ($row['Status'] == 'Cancelled') {
									echo"<tr class='cancelled'>";
								} else {
									echo"<tr class='pending'>";
								}
							}
						echo "
							<td><p class='prodname'>".strtoupper($product)."</p></td>
							<td><p class='orderdisp'>x".$row['Quantity']."</p></td>
							<td><p class='orderdisp'>".$rowC['FName']."</p></td>
							<td><p class='orderdisp'>₱".$row['OrderPrice']."</p></td>";
							if ($row['Status'] == 'Completed') {
								echo"<td><p class='orderdisp'>Order is ".$row['Status']."!</p></td>";
							} else {
								if ($row['Status'] == 'Cancelled') {
									echo"<td><p class='orderdisp'>Order is ".$row['Status']."!</p></td>";
								} else {
									echo"Order is ".$row['Status']."...</td>";
								}
							}
						echo"
							<td><button class='total-button' onclick='openModal()'>View Order</button></td>
						</tr>";
					}
				}
			?>
		</table>
		<br/>
		<div class="pagination">
			<?php  
				$sqlN = "SELECT COUNT(*) AS 'Total' FROM orders WHERE Status = 'Completed' OR Status = 'Cancelled'";  
				$resN = $con->query($sqlN); 
				$rowN = $resN->fetch_assoc();  
				$total_records = $rowN['Total']; 
				$limit = 10;
				
				// Number of pages required.
				$total_pages = ceil($total_records / $limit);  
				$pagLink = "";                        
				for ($i=1; $i<=$total_pages; $i++) {
				  if ($i==$pagen) {
					  $pagLink .= "<a class='active' href='MENUCompletedOrders.php?page=".$i."'>".$i."</a>";
				  }            
				  else  {
					  $pagLink .= "<a href='MENUCompletedOrders.php?page=".$i."'>".$i."</a>";  
				  }
				};  
				echo $pagLink;  
			?>
		</div>
		
		<div id="orderModal" class="modal">
			<div class="modal-content">
				<span class="close-modal">&times;</span>
					<h2>Viewing Order</h2>
					<form id="edit-product-form">
						<img style='height: 400px; width: 100%; object-fit: contain;' src='modals/Pancit Canton_r.png'>
						<table class="receipt">
							<tr>
								<td>Order ID:<td>
								<td>64</td>
							</tr>
							<tr>
								<td>Username:<td>
								<td>asdf</td>
							</tr>
							<tr>
								<td>Product Name:<td>
								<td>Pancit Canton</td>
							</tr>
							<tr>
								<td>Quantity:<td>
								<td>x2</td>
							</tr>
							<tr>
								<td>Total Price:<td>
								<td>₱60</td>
							</tr>
							<tr>
								<td>Product Name:<td>
								<td>Pancit Canton</td>
							</tr>
							<tr>
								<td>eTransaction Reference Number:<td>
								<td>
									<input type="checkbox" id="myCheckbox" checked disabled>
									<div class="content-to-toggle">
									<input type="text" placeholder="Reference number" value="1234 1234 1234" readonly>
									</div>
								</td>
							</tr>
							<tr>
								<td>Time Ordered:<td>
								<td>2025-09-23 12:43:28</td>
							</tr>
							<tr>
								<td>Time Completed:<td>
								<td>2025-09-23 12:47:36</td>
							</tr>
							<tr>
								<td>Status:<td>
								<td>Completed</td>
							</tr>
							<tr>
								<td>Priority Order:<td>
								<td>False</td>
							</tr>
						</table><br/>
						<p class='prodname'>Feedback</p>
						<textarea class="review" readonly>The food was great!</textarea><br/>
					</form>
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