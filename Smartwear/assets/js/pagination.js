// pagination.js
let isLoading = false;
let hasMoreProducts = true;
let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

function displayProducts(append = false) {
  const productsContainer = document.getElementById("products-container");

  // Clear container only if not appending
  if (!append) {
    productsContainer.innerHTML = "";
  }

  // Calculate start and end indices for the current page
  const startIndex = (currentPage - 1) * productsPerPage;
  const endIndex = Math.min(
    startIndex + productsPerPage,
    currentProducts.length
  );

  // Check if we've reached the end of products
  hasMoreProducts = endIndex < currentProducts.length;

  // Get current page products
  const pageProducts = currentProducts.slice(startIndex, endIndex);

  // Create product cards
  pageProducts.forEach((product) => {
    const productCard = document.createElement("div");
    productCard.className = "product-card";

    // Get second image URL by replacing F with B in the image filename
    const primaryImage = product.images;
    const secondaryImage = primaryImage.replace(/F(\d+)/, "B$1");

    // Check if product is in wishlist
    const isInWishlist = wishlist.some((item) => item.id === product.id);
    const wishlistBtnClass = isInWishlist ? "in-wishlist" : "";

    productCard.innerHTML = `
      <div class="product-image-container">
        <div class="image-wrapper">
          <img src="${primaryImage}" alt="${product.name}" class="product-image primary-image">
          <img src="${secondaryImage}" alt="${product.name}" class="product-image secondary-image">
        </div>
        <div class="product-buttons">
          <button class="circular-btn wishlist-btn ${wishlistBtnClass}" data-product-id="${product.id}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
          </button>
          <button class="circular-btn shop-btn" data-product-id="${product.id}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="product-info">
        <h3 class="product-title">${product.name}</h3>
        <p class="product-price">$${product.price}</p>
      </div>
    `;

    productsContainer.appendChild(productCard);

    // Add event listeners for the buttons
    const wishlistBtn = productCard.querySelector(".wishlist-btn");
    const shopBtn = productCard.querySelector(".shop-btn");

    wishlistBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      toggleWishlist(product, this);
    });

    shopBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      goToProductDetails(product.id);
    });

    // Make the whole card clickable to go to product details
    productCard.addEventListener("click", function () {
      goToProductDetails(product.id);
    });
  });

  isLoading = false;
}

// Function to toggle wishlist status
function toggleWishlist(product, buttonElement) {
  const productIndex = wishlist.findIndex((item) => item.id === product.id);

  if (productIndex === -1) {
    // Add to wishlist
    wishlist.push({
      id: product.id,
      name: product.name,
      price: product.price,
      image: product.images,
    });
    buttonElement.classList.add("in-wishlist");
    showNotification(`${product.name} added to wishlist!`);
  } else {
    // Remove from wishlist
    wishlist.splice(productIndex, 1);
    buttonElement.classList.remove("in-wishlist");
    showNotification(`${product.name} removed from wishlist!`);
  }

  // Save to local storage
  localStorage.setItem("wishlist", JSON.stringify(wishlist));
}

// Function to navigate to product details page
function goToProductDetails(productId) {
  window.location.href = `/pages/product.html?id=${productId}`;
}

// Function to show notification
function showNotification(message) {
  // Create notification element if it doesn't exist
  let notification = document.getElementById("notification");
  if (!notification) {
    notification = document.createElement("div");
    notification.id = "notification";
    document.body.appendChild(notification);
  }

  // Set message and show
  notification.textContent = message;
  notification.classList.add("show");

  // Hide after 3 seconds
  setTimeout(() => {
    notification.classList.remove("show");
  }, 3000);
}

// Initialize intersection observer for infinite scroll
function initInfiniteScroll() {
  // Create a loading indicator
  const loadingIndicator = document.createElement("div");
  loadingIndicator.id = "loading-indicator";
  loadingIndicator.innerHTML = "<p>Loading more products...</p>";
  loadingIndicator.style.textAlign = "center";
  loadingIndicator.style.padding = "20px";
  loadingIndicator.style.display = "none";

  // Append loading indicator after products container
  const productsContainer = document.getElementById("products-container");
  productsContainer.parentNode.insertBefore(
    loadingIndicator,
    productsContainer.nextSibling
  );

  // Set up Intersection Observer
  const observer = new IntersectionObserver(
    (entries) => {
      // If we're at the bottom of the page and not currently loading
      if (entries[0].isIntersecting && !isLoading && hasMoreProducts) {
        loadMoreProducts();
      }
    },
    {
      // Trigger when the last product card is 100px from viewport
      rootMargin: "0px 0px 100px 0px",
    }
  );

  // Observe the loading indicator
  observer.observe(loadingIndicator);

  // Make the loading indicator visible
  loadingIndicator.style.display = "block";
}

// Function to load more products when scrolling
function loadMoreProducts() {
  if (isLoading || !hasMoreProducts) return;

  isLoading = true;
  document.getElementById("loading-indicator").style.display = "block";

  // Simulate network delay
  setTimeout(() => {
    const totalPages = Math.ceil(currentProducts.length / productsPerPage);

    if (currentPage < totalPages) {
      currentPage++;
      displayProducts(true); // Append new products
    } else {
      hasMoreProducts = false;
      document.getElementById("loading-indicator").innerHTML =
        "<p>No more products to load</p>";
    }

    document.getElementById("loading-indicator").style.display = hasMoreProducts
      ? "block"
      : "none";
  }, 500); // Small delay to show loading indicator
}

// Filter products by category (modified version)
document.getElementById("category-filter").addEventListener("change", (e) => {
  filterProductsByCategory(e.target.value);
});
