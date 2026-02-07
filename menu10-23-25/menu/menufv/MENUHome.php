<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Food Menu</title>
    <link rel="stylesheet" href="styles3.css" />
    <style>
      .main-layout {
        display: flex;
        flex-direction: row;
        min-height: 80vh;
      }
      .store-sidebar {
        width: 100px;
        background-color: #f9f9f9;
        padding: 20px 10px;
        border-right: 2px solid #ddd;
        display: flex;
        flex-direction: column;
        gap: 15px;
        transition:
          width 0.3s ease,
          padding 0.3s ease;
        overflow: hidden;
        flex-shrink: 0;
      }
      .store-sidebar:hover {
        width: 380px;
        padding: 20px;
      }
      .store-btn-vertical {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #38b6ff;
        border: 3px solid #002557;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: left;
        font-family: Lufga, sans-serif;
        font-weight: bold;
        color: #002557;
        white-space: nowrap;
        overflow: hidden;
      }
      .store-sidebar:hover .store-btn-vertical {
        padding: 15px;
      }
      .store-btn-vertical span {
        opacity: 0;
        transition: opacity 0.2s ease;
      }
      .store-sidebar:hover .store-btn-vertical span {
        opacity: 1;
        transition-delay: 0.1s;
      }
      .store-btn-vertical:hover,
      .store-btn-vertical.active {
        background: #002557;
        color: #ffde59;
        border-color: #ffde59;
        transform: translateX(5px);
      }
      .store-logo-placeholder {
        width: 50px;
        height: 50px;
        background-color: #ccc;
        border-radius: 50%;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #555;
        flex-shrink: 0;
        border: 1px solid #000;
      }
      .content-area {
        flex-grow: 1;
        padding: 20px;
      }
      .food-banner {
        width: 100%;
        margin-bottom: 20px;
        border-radius: 10px;
        overflow: hidden;
      }
    </style>
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
        <div class="main-layout">
        <div class="store-sidebar">
          <div
            class="store-btn-vertical"
            onclick="setStore(0); console.log('Clicked: BULLDOGS CORNER', currentStoreIndex)"
            data-store="BULLDOGS CORNER"
          >
            <div class="store-logo-placeholder">LOGO</div>
            <span>BULLDOGS CORNER</span>
          </div>
          <div
            class="store-btn-vertical"
            onclick="setStore(1); console.log('Clicked: BULLDOGS PUNCHBOWL', currentStoreIndex)"
            data-store="BULLDOGS PUNCHBOWL"
          >
            <div class="store-logo-placeholder">LOGO</div>
            <span>BULLDOGS PUNCHBOWL</span>
          </div>
          <div
            class="store-btn-vertical"
            onclick="setStore(2); console.log('Clicked: BULLDOGS QUICKBITES', currentStoreIndex)"
            data-store="BULLDOGS QUICKBITES"
          >
            <div class="store-logo-placeholder">LOGO</div>
            <span>BULLDOGS QUICKBITES</span>
          </div>
          <div
            class="store-btn-vertical"
            onclick="setStore(3); console.log('Clicked: HUNGRY BULLDOGS', currentStoreIndex)"
            data-store="HUNGRY BULLDOGS"
          >
            <div class="store-logo-placeholder">LOGO</div>
            <span>HUNGRY BULLDOGS</span>
          </div>
        </div>

        <div class="content-area">
          <section class="food-banner">
            <div class="food-banner-overlay"></div>
            <div class="food-banner-content">
              <h2 class="banner-title">Craving Something Delicious?</h2>
              <p class="banner-text">
                Order from your favorite Bulldogs stall now!
              </p>
            </div>
          </section>
          <div class="grid-container" id="product-grid">
            <!-- Products will be loaded dynamically -->
          </div>
        </div>
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
			<h3 id="modal-title-text"></h3>

          </div>
          <div class="inner-modal">
            <p id="modal-description"></p>
            <form method="POST" action="">
              <input type="hidden" name="product_id" id="modal-prodId" />
              <input type="hidden" name="avail_items" id="modal-availItems" />
              <input type="hidden" name="product_price" id="modal-price" />
              <p id="modal-showAvail"></p>
              <label for="product_quantity">Quantity:</label><br />
			  <table>
				<tr>
					<td>
					  <input
						type="number"
						name="product_quantity"
						id="modal-quantity"
						value="1"
						min="1"
						style="width: 50%"
					  />
					</td>
					<td>
					  <button type="button" id="btnpos" onclick="changeQuantity(1)">
						+</button>
					  <button type="button" id="btnneg" onclick="changeQuantity(-1)">
						-</button>
					</td>
				</tr>
			  </table><br /><br />
				<button
				  type="button"
				  class="checkout-button"
				  onclick="
					addToCart(
					  document.getElementById('modal-prodId').value,
					  document.getElementById('modal-title').value,
					  document.getElementById('modal-availItems').value,
					  parseInt(document.getElementById('modal-quantity').value) || 1,
					  document.getElementById('modal-price').value
					)
				  "
				>
				  Order/Add to Tray
				</button>

            </form>
          </div>
      </div>
    </div>
	
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