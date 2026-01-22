<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Food Menu</title>
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
    <main>
	<header>
		<!--<div class="head">-->
			<img src="MenuLOGO.png" alt="Header Image" class="header-image" />
			<a class="active-button" href="MENUHome.php"><p class="header-logout">VIEW MENU</p></a>
			<a href="MENUCart.php" class="header-button"><p class="header-logout">VIEW CART</p></a>
			<a href="MENUCustomerOrders.php" class="header-button"><p class="header-logout">MY ORDERS</p></a>
			<a href="MENUCustomerHistory.php" class="header-button"><p class="header-logout">ORDER HISTORY</p></a>
			<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
		<!--</div>-->
    </header>
	
	<center>
        <section class="food-banner">
          <div class="food-banner-overlay"></div>
          <div class="food-banner-content">
            <h2 class="banner-title">Craving Something Delicious?</h2>
            <p class="banner-text">
              Order from your favorite Bulldogs stall now!
            </p>
          </div>
        </section>

        <div class="store-carousel">
          <button class="carousel-btn prev-btn" onclick="slideStore(-1)">
            &#10094;
          </button>
          <div class="store-track">
            <div
              class="store-slide"
              data-store="BULLDOGS CORNER"
              onclick="setStore(1)"
            >
              BULLDOGS CORNER
            </div>
            <div
              class="store-slide"
              data-store="BULLDOGS PUNCHBOWL"
              onclick="setStore(2)"
            >
              BULLDOGS PUNCHBOWL
            </div>
            <div
              class="store-slide"
              data-store="BULLDOGS QUICKBITES"
              onclick="setStore(3)"
            >
              BULLDOGS QUICKBITES
            </div>
            <div
              class="store-slide"
              data-store="HUNGRY BULLDOGS"
              onclick="setStore(4)"
            >
              HUNGRY BULLDOGS
            </div>
          </div>
          <button class="carousel-btn next-btn" onclick="slideStore(1)">
            &#10095;
          </button>
        </div>
      </center>
      <div class="grid-container" id="product-grid">
        <!-- Products will be loaded dynamically -->
      </div>
    </main>

    <div id="modal" class="modal">
      <div class="modal-content">
        <center>
          <span class="close" onclick="closeModal()">&times;</span>
          <div class="modal-img">
            <img
              id="modal-image"
              src="../"
              alt="Product Image"
              style="max-width: 80%; height: auto; margin-bottom: 0"
            />
            <input type="hidden" id="modal-title" />
          </div>
          <div class="inner-modal">
            <p id="modal-description"></p>
            <form method="POST" action="">
              <input type="hidden" name="product_id" id="modal-prodId" />
              <input type="hidden" name="avail_items" id="modal-availItems" />
              <input type="hidden" name="product_price" id="modal-price" />
              <p id="modal-showAvail"></p>
              <label for="product_quantity">Quantity:</label><br />
              <button type="button" id="btnneg" onclick="changeQuantity(-1)">
                -
              </button>
              <input
                type="number"
                name="product_quantity"
                id="modal-quantity"
                value="1"
                min="1"
                style="width: 50%"
              />
              <button type="button" id="btnpos" onclick="changeQuantity(1)">
                +</button
              ><br /><br />
              <button
                type="button"
                class="checkout-button"
                onclick="
                  addToCart(
                    document.getElementById('modal-prodId').value,
                    document.getElementById('modal-title').innerText,
                    document.getElementById('modal-availItems').value,
                    parseInt(document.getElementById('modal-quantity').value) ||
                      1,
                    document.getElementById('modal-price').value,
                  )
                "
              >
                Order/Add to Tray
              </button>
            </form>
          </div>
        </center>
      </div>
    </div>
	
	<!--<center>

	<section class="food-banner">
	  <div class="food-banner-overlay"></div>
	  <div class="food-banner-content">
		<h2 class="banner-title">Craving Something Delicious?</h2>
		<p class="banner-text">Order from your favorite Bulldogs stall now!</p>
	  </div>
	</section>
	
	<div class="store">
		<a href="MENUHome.php">BULLDOGS CORNER</a>
		<a href="MENUHome.php">BULLDOGS PUNCHBOWL</a>
		<a href="MENUHome.php">BULLDOGS QUICKBITES</a>
		<a href="MENUHome.php" class='active'>HUNGRY BULLDOGS</a>
	</div>
	</center>
		<div class="grid-container">
			<?php
				/*include("connection.php");
				
				$sql = "SELECT * FROM products WHERE Status = 'ACTIVE'";
				$res = $con->query($sql);
				
				if($res->num_rows>0) {
					while ($row=$res->fetch_assoc()) {
						$productName = addslashes($row['ProductName']);
						$productDesc = addslashes($row['ProductDesc']);
						$productId = addslashes($row['ProductID']);
						$productQuant = addslashes($row['Quantity']);
						$productPrice = addslashes($row['Price']);
						
						echo "
						<div class='grid-item' onclick='openModal(\"$productName\", \"$productDesc\", \"$row[ProductID]\", \"$productQuant\", \"$productPrice\")'>
							<img src='modals/".$row['ProductName'].".png' alt='productimg'/>
							<!-- <h2>".$row['ProductName']."</h2>
							<p>₱".$row['Price'].", ".$row['Status']."<p> -->
						</div>
						";
					}
				}*/
			?>
		</div>
    </main>

    <div id="modal" class="modal">
		<div class="modal-content">
			<center>
				<span class='close' onclick='closeModal()'>&times;</span>
				<div class="modal-img">
					<img id="modal-image" src="" alt="Product Image" style="max-width: 80%; height: auto; margin-bottom:0;">
					<input type="hidden" id="modal-title" />
				</div>
				<div class="inner-modal">
					<p id="modal-description"></p>
					<form method="POST" action="">
						<input type="hidden" name="product_id" id="modal-prodId">
						<input type="hidden" name="avail_items" id="modal-availItems">
						<input type="hidden" name="product_price" id="modal-price">
						<p id="modal-showAvail"></p>
						<label for="product_quantity">Quantity:</label><br/>
						<table>
						<tr>
						<td rowspan=2><input type="number" name="product_quantity" id="modal-quantity" value="1" min="1" style="width: 50%;"></td>
						<td><button type="button" id="btnpos" onclick="changeQuantity(1)">+</button>
						</tr>
						<tr>
						<td><button type="button" id="btnneg" onclick="changeQuantity(-1)">-</button></td>
						</tr>
						</table><br/>
						<button type="button" class="checkout-button" onclick="addToCart(
							document.getElementById('modal-prodId').value,
							document.getElementById('modal-title').innerText,
							document.getElementById('modal-availItems').value,
							parseInt(document.getElementById('modal-quantity').value) || 1,
							document.getElementById('modal-price').value
						)">Order/Add to Tray</button>
					</form>
				</div>
			</center>
		</div>
    </div>-->
	
    <script src="script.js"></script>
  </body>
</html>

<?php
	include("connection.php");
	
	if(isset($_POST['order-product'])) {
		if(isset($_POST['product_id']) && !empty($_POST['product_id']) && $_POST['']) {
			$num = $_POST['product_quantity'];
			$id = $_POST['product_id'];
				
			echo "<script>alert('Order for Product ".$id." ".$num."x received!');</script>";
		} else {
			echo "<script>alert('No Product ID received.');</script>";
		}
	}
?>