let currentStoreIndex = 0;

const storeSellerMap = {
  "BULLDOGS CORNER": 1,
  "BULLDOGS PUNCHBOWL": 2,
  "BULLDOGS QUICKBITES": 3,
  "HUNGRY BULLDOGS": 4,
};

function slideStore(direction) {
  const slides = document.querySelectorAll(".store-slide");
  if (!slides.length) return;
  currentStoreIndex =
    (currentStoreIndex + direction + slides.length) % slides.length;
  updateCarousel();
}

function setStore(index) {
  currentStoreIndex = index;
  updateCarousel();
}

function updateCarousel() {
  const track = document.querySelector(".store-track");
  const slides = document.querySelectorAll(".store-slide");
  const container = document.querySelector(".store-carousel");

  if (!track || !slides.length || !container) return;

  slides.forEach((slide, i) => {
    if (i === currentStoreIndex) {
      slide.classList.add("active");
    } else {
      slide.classList.remove("active");
    }
  });

  // Calculate position to center the active slide
  const activeSlide = slides[currentStoreIndex];
  const slideCenter = activeSlide.offsetLeft + activeSlide.offsetWidth / 2;
  const containerWidth = container.offsetWidth;
  const position = containerWidth / 2 - slideCenter;

  track.style.transform = `translateX(${position}px)`;

  const storeName = activeSlide.getAttribute("data-store");
  loadProducts(storeName);
}

function loadProducts(storeName) {
  const gridContainer = document.getElementById("product-grid");
  if (!gridContainer) return;

  const sellerId = storeSellerMap[storeName];
  if (!sellerId) {
    gridContainer.innerHTML = "<p>No products found.</p>";
    return;
  }

  fetch(`getProdSeller.php?seller_id=${sellerId}`)
    .then((res) => res.json())
    .then((products) => {
      gridContainer.innerHTML = "";

      if (products.length === 0) {
        gridContainer.innerHTML = "<p>No available products.</p>";
        return;
      }

      products.forEach((product) => {
        const productDiv = document.createElement("div");
        productDiv.className = "grid-item";
        productDiv.onclick = () =>
          openModal(
            product.ProductName,
            product.ProductDesc,
            product.ProductID,
            product.Quantity,
            product.Price
          );

        const img = document.createElement("img");
        img.src = `modals/${product.ProductName}.png`;
        img.alt = product.ProductName;
        img.onerror = function () {
          this.src = `https://placehold.co/200x200?text=${encodeURIComponent(
            product.ProductName
          )}`;
        };

        const name = document.createElement("div");
        name.innerText = product.ProductName;
        name.className = "product-name";

        const price = document.createElement("div");
        price.innerText = "₱" + product.Price;
        price.className = "product-price";

        productDiv.appendChild(img);
        productDiv.appendChild(name);
        productDiv.appendChild(price);
        gridContainer.appendChild(productDiv);
      });
    })
    .catch((err) => {
      console.error(err);
      gridContainer.innerHTML = "<p>Error loading products.</p>";
    });
}

console.log("Loading products for SellerID:", sellerId);

function openModal(title, description, prodId, items, price) {
  document.getElementById("modal-title").innerText = title;
  document.getElementById("modal-description").innerText = description;
  const inputField = document.getElementById("modal-prodId");
  inputField.value = prodId;
  document.getElementById("modal-showAvail").innerText =
    "Available items: " + items;
  const inputField2 = document.getElementById("modal-availItems");
  inputField2.value = items;

  const inputField3 = document.getElementById("modal-price");
  inputField3.value = price;

  const imageElement = document.getElementById("modal-image");
  const imageFileName = title + ".png";
  imageElement.src = "../modals/" + imageFileName;

  console.log("Input field value set to:", inputField.value);
  document.getElementById("modal").style.display = "block";
}

function changeQuantity(amount) {
  const input = document.getElementById("modal-quantity");
  let currentValue = parseInt(input.value) || 1;
  currentValue += amount;
  if (currentValue < 1) currentValue = 1;
  input.value = currentValue;
}

function closeModal() {
  document.getElementById("modal").style.display = "none";
}

window.onclick = function (event) {
  const modal = document.getElementById("modal");
  if (event.target === modal) {
    closeModal();
  }
};

function removeItem(itemId) {
  const item = document.getElementById(itemId);
  if (item) {
    item.remove();
  }
}

// Function to add item to session storage
function addToCart(
  productId,
  productName,
  availItems,
  productQuantity,
  productPrice,
) {
  let cart = JSON.parse(sessionStorage.getItem("cart")) || [];

  if (productQuantity > availItems) {
    alert(
      `${productQuantity} is past the available amount of ${productName}, which is ${availItems}`,
    );
  } else {
    let cartItem = {
      id: productId,
      name: productName,
      quantity: Number(productQuantity),
      price: Number(productPrice),
    };

    let existingItem = cart.find((item) => item.id === productId);
    if (existingItem) {
      existingItem.quantity += Number(productQuantity);
    } else {
      cart.push(cartItem);
    }

    sessionStorage.setItem("cart", JSON.stringify(cart));
    alert(`${productQuantity}x ${productName} added to cart!`);
    displayTotalPrice();
  }
}

function getCart() {
  return JSON.parse(sessionStorage.getItem("cart")) || [];
}

// calculates total price of all cart items
function calculateTotalPrice() {
  let cart = getCart();
  let total = 0;
  cart.forEach((item) => {
    total += item.price * item.quantity;
  });
  return total.toFixed(2);
}

// Attach event listener to the order button
document.addEventListener("DOMContentLoaded", function () {
  const orderButton = document.getElementById("order-button");
  if (orderButton) {
    orderButton.addEventListener("click", function (event) {
      event.preventDefault();
      let productId = document.getElementById("modal-prodId").value;
      let productName = document.getElementById("modal-title").innerText;
      let quantity =
        document.querySelector("input[name='product_quantity']").value || 1;
      let productPrice = document.getElementById("modal-price")?.value || 0; // assume there's a hidden price field
      addToCart(productId, productName, 999, quantity, productPrice); // replace 999 with actual available stock if needed
    });
  }
  
  displayTotalPrice(); // Update total on load

  const slides = document.querySelectorAll(".store-slide");
  if (slides.length > 0) {
    updateCarousel();
    window.addEventListener("resize", updateCarousel);
  }
});
