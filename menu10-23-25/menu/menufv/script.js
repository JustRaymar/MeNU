let currentStoreIndex = 0;

const storeMap = {
  "BULLDOGS CORNER": 1,
  "BULLDOGS PUNCHBOWL": 2,
  "BULLDOGS QUICKBITES": 3,
  "HUNGRY BULLDOGS": 4,
};

function setStore(index) {
  currentStoreIndex = index;
  updateCarousel();
}

function updateCarousel() {
  const buttons = document.querySelectorAll(".store-btn-vertical");
  if (!buttons.length) return;

  buttons.forEach((btn, i) => {
    btn.classList.toggle("active", i === currentStoreIndex);
  });

  const activeBtn = buttons[currentStoreIndex];
  if (!activeBtn) return;

  const storeName = activeBtn.getAttribute("data-store");
  const storeId = storeMap[storeName];

  if (storeId === undefined) {
    console.error("Invalid store:", storeName);
    return;
  }

  loadProducts(storeId);
}

function loadProducts(storeId) {
  const gridContainer = document.getElementById("product-grid");
  if (!gridContainer) return;

  fetch(`getProdSeller.php?store_id=${storeId}`)
    .then(res => res.json())
    .then(products => {
      console.log("Loaded products:", products);

      gridContainer.innerHTML = "";

      if (!products.length) {
        gridContainer.innerHTML = "<p>No available products.</p>";
        return;
      }

      products.forEach(product => {
		  const productDiv = document.createElement("div");
		  productDiv.className = "grid-item";

		  productDiv.onclick = () =>
			openModal(
			  product.product_name,
			  product.description,
			  product.product_id,
			  product.available_stock,
			  product.unit_price
			);

		  // Image
		  const img = document.createElement("img");
		  img.src = `modals/${product.product_name}.png`;
		  img.alt = product.product_name, product.unit_price;
		  img.onerror = function () {
			this.src = `https://placehold.co/200x200?text=${encodeURIComponent(
			  product.product_name
			)}`;
		  };

		  productDiv.append(img);
		  gridContainer.appendChild(productDiv);
		});
    })
    .catch(err => {
      console.error(err);
      gridContainer.innerHTML = "<p>Error loading products.</p>";
    });
}

function openModal(title, description, prodId, items, price) {
  document.getElementById("modal-title").value = title;
  document.getElementById("modal-title-text").innerText = title; // NEW

  document.getElementById("modal-description").innerText = description;
  document.getElementById("modal-prodId").value = prodId;
  document.getElementById("modal-availItems").value = items;
  document.getElementById("modal-showAvail").innerText =
    "Available items: " + items;
  document.getElementById("modal-price").value = price;

  document.getElementById("modal-image").src = `modals/${title}_r.png`;
  document.getElementById("modal").style.display = "block";
}


function changeQuantity(amount) {
  const input = document.getElementById("modal-quantity");
  input.value = Math.max(1, (parseInt(input.value) || 1) + amount);
}

function closeModal() {
  document.getElementById("modal").style.display = "none";
}

window.onclick = e => {
  if (e.target === document.getElementById("modal")) closeModal();
};

function addToCart(productId, productName, availItems, qty, price) {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

  const buttons = document.querySelectorAll(".store-btn-vertical");
  const activeBtn = buttons[currentStoreIndex];
  const storeName = activeBtn.getAttribute("data-store");
  const storeId = storeMap[storeName];

  if (qty > availItems) {
    alert(`Only ${availItems} items available.`);
    return;
  }

  // Enforce one-store-only rule
  if (cart.length > 0 && cart[0].store_id !== storeId) {
    alert("You can only order from one store per transaction.");
    return;
  }

  const existing = cart.find(i => i.id === productId);
  if (existing) {
    existing.quantity += Number(qty);
  } else {
    cart.push({
      id: productId,
      name: productName,
      quantity: Number(qty),
      price: Number(price),
      store_id: storeId
    });
  }

  sessionStorage.setItem("cart", JSON.stringify(cart));
  alert(`${qty}x ${productName} added to cart`);
  displayTotalPrice();
}

function calculateTotalPrice() {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
  let total = 0;

  cart.forEach(item => {
    total += item.price * item.quantity;
  });

  return total;
}

function displayTotalPrice() {
  const totalDisplay = document.getElementById("total-price");
  if (!totalDisplay) return;

  const total = calculateTotalPrice();
  totalDisplay.innerText = `₱${total}`;
}

document.addEventListener("DOMContentLoaded", () => {
  const orderBtn = document.getElementById("order-button");
  if (orderBtn) {
    orderBtn.addEventListener("click", e => {
      e.preventDefault();
      addToCart(
        document.getElementById("modal-prodId").value,
        document.getElementById("modal-title").value,
        document.getElementById("modal-availItems").value,
        document.getElementById("modal-quantity").value,
        document.getElementById("modal-price").value
      );
    });
  }

  updateCarousel();
});

function updateCartQuantity(productId, delta) {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

  cart = cart.map(item => {
    if (parseInt(item.id) === parseInt(productId)) {
      item.quantity += delta;
      if (item.quantity < 1) item.quantity = 1;
    }
    return item;
  });

  sessionStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}


function removeCartItem(productId) {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

  cart = cart.filter(item =>
    parseInt(item.id) !== parseInt(productId)
  );

  sessionStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}


function renderCart() {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cart-items");
  const cartDataInput = document.getElementById("cart_data");

  if (!cartContainer) return;

  if (cart.length === 0) {
    cartContainer.innerHTML = "<p>Your cart is empty.</p>";
    if (cartDataInput) cartDataInput.value = "";
    displayTotalPrice();
    return;
  }

  cartContainer.innerHTML = cart.map(item => `
    <table class="cartitem">
      <tr>
        <td class="citem">
          <img src="modals/${item.name}_r.png" class="cartimg" />
          <p class="prodname">${item.name.toUpperCase()}</p>
        </td>

        <td class="citemtxt">
		  <div class="qty-row">
			<p class="itemtext">x${item.quantity}</p>
			<div class="qty-buttons">
			  <button type="button" id="btnpos"
				onclick="updateCartQuantity(${item.id}, 1)">+</button>
			  <button type="button" id="btnneg"
				onclick="updateCartQuantity(${item.id}, -1)">−</button>
			</div>
		  </div>
		</td>

        <td class="citemtxt">
          <p class="itemtext">₱${item.price * item.quantity}</p>
          <button class="remove-btn"
            onclick="removeCartItem(${item.id})">Remove</button>
        </td>
      </tr>
    </table>
  `).join("");

  if (cartDataInput) {
    cartDataInput.value = JSON.stringify(cart);
  }

  displayTotalPrice();
}

