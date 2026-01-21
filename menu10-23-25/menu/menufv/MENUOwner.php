<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MENU Owner Menu</title>
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
		<a href="MENUOwner.php" class="active-button"><p class="header-logout">MY STORE PRODUCTS</p></a>
		<a href="MENUOwnerIncoming.php" class="header-button"><p class="header-logout">INCOMING ORDERS</p></a>
		<a href="MENUOwnerHistory.php" class="header-button"><p class="header-logout">ORDER HISTORY</p></a>
		<a href="MENUOwnerStats.php" class="header-button"><p class="header-logout">SALES REPORTS</p></a>
		<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
    </header>
      <div class="grid-container" id="product-grid">
        <!-- Add Product Tile -->
        <div class="grid-item add-product-tile" onclick="openAddProductModal()">
          <img src="plus.png" alt="Add Product" />
          <h2>Add New Product</h2>
        </div>
        <!-- Product tiles will be dynamically added here -->
      </div>
    </main>

    <!-- Add Product Modal -->
    <div id="add-product-modal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Product</h2>
        <form id="add-product-form" method="POST">
			<label for="product-name">Product Name:</label>
			<input type="text" id="product-name" required />
			
			<label for="product-amount">Available Stock:</label>
			<table>
				<tr>
					<td rowspan=2><input type="number" id="product-amount" required /></td>
					<td><button type="button" id="btnpos" onclick="changeQuantity(1)">+</button></td>
				</tr>
				<tr>
					<td><button type="button" id="btnneg" onclick="changeQuantity(-1)">-</button></td>
				</tr>
			</table>

			<label for="product-price">Price:</label>
			<input type="number" id="product-price" step="0.01" required />

			<label for="product-description">Description:</label>
			<textarea id="product-description" required></textarea>

			<button type="submit" name="btn_newProduct">Add Product</button>
        </form>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="edit-product-modal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Product</h2>
        <form id="edit-product-form">
			<input type="hidden" id="edit-product-id" />
		
			<label for="edit-product-name">Product Name:</label>
			<input type="text" id="edit-product-name" required />
			
			<label for="edit-product-amount">Available Stock:</label>
			<table>
				<tr>
					<td rowspan=2><input type="number" id="edit-product-amount" required /></td>
					<td><button type="button" id="btnpos" onclick="changeEditQuantity(1)">+</button></td>
				</tr>
				<tr>
					<td><button type="button" id="btnneg" onclick="changeEditQuantity(-1)">-</button></td>
				</tr>
			</table>

			<label for="edit-product-price">Price:</label>
			<input type="number" id="edit-product-price" step="0.01" required />

			<label for="edit-product-description">Description:</label>
			<textarea id="edit-product-description" required></textarea>

			<button type="submit">Save Changes</button>
        </form>
      </div>
    </div>

    <script src="seller.js"></script>
	<script>
        document.addEventListener("DOMContentLoaded", function() {
            loadProducts(); // Fetch and display products when the page loads
        });
    </script>
  </body>
</html>