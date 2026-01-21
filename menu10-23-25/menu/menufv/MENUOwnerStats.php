<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>MENU Owner Order Statistics</title>
		<link rel="stylesheet" href="styles5.css" />
	</head>
	<body>
	<?php
		// ---------- existing session + connection (kept as you requested) ----------
		session_start();
		include("connection.php");
		if (!isset($_SESSION['user_id'])) {
			header("Location: MENULogin.php");
			exit();
		}
		$sqlId = "SELECT * FROM sellers WHERE UserID = ".$_SESSION['user_id'];
		$resId = $con->query($sqlId);
		$rowId = $resId->fetch_assoc();

		// -----------------------------
		// Initialize dashboard variables
		// -----------------------------
		// Make sure selectedProduct exists before HTML uses it
		$selectedProduct = isset($_GET['product']) ? $_GET['product'] : "All";

		// Determine product image path
		if ($selectedProduct === "All") {
			$productImage = "menu.png";
		} else {
			$productImage = "modals/".$selectedProduct."_r.png";
		}

		// Temporary/mock values for UI — replace with SQL integration later if desired.
		$totalOrders = 0;
		$totalRevenue = 0;
		$stockRemaining = "-";
		$percentDiffVal = 0.0; // numeric value for comparisons
		$percentDiffDisplay = "+0%"; // formatted for display

		switch ($selectedProduct) {
			case "Pancit Canton":
				$totalOrders = 32; $totalRevenue = 1560; $stockRemaining = 40; $percentDiffVal = 12; break;
			case "Pancit Canton w Egg":
				$totalOrders = 21; $totalRevenue = 1575; $stockRemaining = 25; $percentDiffVal = -7; break;
			case "Shawarma Rice":
				$totalOrders = 18; $totalRevenue = 1620; $stockRemaining = 30; $percentDiffVal = 4; break;
			case "Siomai Rice":
				$totalOrders = 26; $totalRevenue = 1560; $stockRemaining = 50; $percentDiffVal = 9; break;
			case "Buttered Cheese Corn":
				$totalOrders = 40; $totalRevenue = 2000; $stockRemaining = 35; $percentDiffVal = 22; break;
			case "Ham and Cheese Sandwhich":
				$totalOrders = 17; $totalRevenue = 1020; $stockRemaining = 15; $percentDiffVal = -3; break;
			default:
				// All products
				$totalOrders = 154; $totalRevenue = 9335; $stockRemaining = 230; $percentDiffVal = 15; break;
		}

		// Format percent display string
		$percentDiffDisplay = ($percentDiffVal >= 0 ? "+" : "") . number_format($percentDiffVal, 2) . "%";
	?>
    <main>
	<header>
		<img src="MenuLOGO.png" alt="Header Image" class="header-image" />
		<a href="MENUOwner.php" class="header-button"><p class="header-logout">MY STORE PRODUCTS</p></a>
		<a href="MENUOwnerIncoming.php" class="header-button"><p class="header-logout">INCOMING ORDERS</p></a>
		<a href="MENUOwnerHistory.php" class="header-button"><p class="header-logout">ORDER HISTORY</p></a>
		<a href="MENUOwnerStats.php" class="active-button"><p class="header-logout">SALES REPORTS</p></a>
		<a href="logout.php" class="header-button"><p class="header-logout">LOGOUT</p></a>
    </header>

	<!-- === DASHBOARD CONTENT (kept under your header exactly) === -->
	<div class="dashboard-wrapper">
		<div class="dashboard-content">
			<h2 class="dashboard-title">Sales Overview</h1>

			<!-- PRODUCT PANEL -->
			<form method="GET">
				<div class="product-panel">
					<select name="product" class="product-select" onchange="this.form.submit()">
						<!-- explicit value attributes prevent undefined-index surprises -->
						<option value="All" <?= $selectedProduct === "All" ? "selected" : "" ?>>All</option>
						<option value="Pancit Canton" <?= $selectedProduct === "Pancit Canton" ? "selected" : "" ?>>Pancit Canton</option>
						<option value="Pancit Canton w Egg" <?= $selectedProduct === "Pancit Canton w Egg" ? "selected" : "" ?>>Pancit Canton w Egg</option>
						<option value="Shawarma Rice" <?= $selectedProduct === "Shawarma Rice" ? "selected" : "" ?>>Shawarma Rice</option>
						<option value="Siomai Rice" <?= $selectedProduct === "Siomai Rice" ? "selected" : "" ?>>Siomai Rice</option>
						<option value="Buttered Cheese Corn" <?= $selectedProduct === "Buttered Cheese Corn" ? "selected" : "" ?>>Buttered Cheese Corn</option>
						<option value="Ham and Cheese Sandwhich" <?= $selectedProduct === "Ham and Cheese Sandwhich" ? "selected" : "" ?>>Ham and Cheese Sandwhich</option>
					</select>
				</div>
			</form>

			<div class="stats-grid">
				<!-- TOP 4 CARDS -->
				<div class="stats-card">
					<h3>Total Orders for Today,</h3>
					<p class="stat-date">November 12th, 2025</p>
					<div class="stats-value"><?= number_format($totalOrders) ?></div>
				</div>

				<div class="stats-card">
					<h3>Total Revenue for Today,</h3>
					<p class="stat-date">November 12th, 2025</p>
					<div class="stats-value">₱<?= number_format($totalRevenue, 2) ?></div>
				</div>

				<!-- STOCK AT CRITICAL LEVELS CARD (HOVERABLE) -->
				<div class="stats-card critical-hover-card">
					<h3>Stock at Critical Levels</h3>
					<div class="stat-value">
						<span style="font-size: 40px; font-weight: bold; color: #BF3636;">3</span>
					</div>
					<p style="color: #666; font-size: 14px; margin-top: 10px;">Hover to view</p>

					<!-- HOVER POPUP -->
					<div class="critical-popup">
						<h4>Critical Stock Items</h4>
						<ul>
							<li>Pancit Canton — 3 left</li>
							<li>Siomai Rice — 5 left</li>
							<li>Ham and Cheese Sandwich — 2 left</li>
						</ul>
					</div>
				</div>

				<div class="stats-card">
					<h3>Monthly Growth</h3><br/>
					<div class="stats-value <?= ($percentDiffVal >= 0) ? 'growth-positive' : 'growth-negative' ?>">
						<?= $percentDiffDisplay ?>
					</div>
				</div>

				<!-- PRODUCT IMAGE CARD (150% WIDTH) -->
				<div class="product-image-card">
					<h3>Selected Product</h3>
					<img src="<?= htmlspecialchars($productImage) ?>" alt="Product Image" class="product-image-large">
				</div>

				<!-- CALENDAR CARD (RIGHT) -->
				<div class="calendar-card">
					<div class="calendar-header">
						<button class="month-nav" onclick="prevMonth()">&lt;</button>
						<span class="calendar-month">November 2025</span>
						<button class="month-nav" onclick="nextMonth()">&gt;</button>
					</div>
					<div class="calendar-grid">
						<div class="calendar-day">Sun</div>
						<div class="calendar-day">Mon</div>
						<div class="calendar-day">Tue</div>
						<div class="calendar-day">Wed</div>
						<div class="calendar-day">Thu</div>
						<div class="calendar-day">Fri</div>
						<div class="calendar-day">Sat</div>
						<!-- Placeholder dates -->
						<?php for($i=1;$i<=30;$i++): ?>
							<div class="calendar-date"><?= $i ?></div>
						<?php endfor; ?>
						<script>
							function prevMonth() {
								alert("Previous month clicked (UI only)");
							}

							function nextMonth() {
								alert("Next month clicked (UI only)");
							}
						</script>

					</div>
				</div>

			</div> <!-- end stats-grid -->

		</div> <!-- end dashboard-content -->
	</div> <!-- end dashboard-wrapper -->

	</main>
	</body>
</html>
