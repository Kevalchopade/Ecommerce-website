// products.js
let allProducts = [];
let currentProducts = [];
let currentPage = 1;
const productsPerPage = 12; // Increased for better display

// Fetch products from CSV file
async function fetchProducts() {
  try {
    const response = await fetch('/assets/SmartWear.csv');
    const data = await response.text();
    
    // Parse CSV
    const rows = data.split('\n').slice(1); // Skip header row
    
    allProducts = rows.map(row => {
      const columns = row.split(',');
      return {
        id: columns[0],
        name: columns[1],
        price: parseFloat(columns[2]), // Ensure price is a number
        category: columns[3],
        subcategory: columns[4], // Match this with CSV's "sub-category"
        images: columns[5],
        image2: columns[6],
        brand: columns[7],
        description: columns[8],
        size: columns[9]
      };
    }).filter(product => product.id && product.name); // More robust filtering
    
    // Initialize with all products
    currentProducts = [...allProducts];
    
    // Set up filter event listeners
    setupFilterEventListeners();
    
    // Initialize category filter dropdown (completely replace options)
    populateCategoryFilter();
    
    // Display initial products
    displayProducts();
    
    // Initialize infinite scroll
    initInfiniteScroll();
  } catch (error) {
    console.error('Error fetching products:', error);
    alert('Failed to load products. Please try again.');
  }
}

// Populate category filter dropdown
function populateCategoryFilter() {
  const categoryFilter = document.getElementById('category-filter');
  const subcategoryFilter = document.getElementById('subcategory-filter');
  
  // Clear ALL existing options
  categoryFilter.innerHTML = '';
  
  // Add 'All' option first
  const allOption = document.createElement('option');
  allOption.value = 'all';
  allOption.textContent = 'All';
  categoryFilter.appendChild(allOption);
  
  // Get unique categories
  const uniqueCategories = [...new Set(allProducts.map(product => product.category))];
  
  // Add unique categories to dropdown
  uniqueCategories.forEach(category => {
    if (category) { // Check if category is not empty
      const option = document.createElement('option');
      option.value = category;
      option.textContent = category;
      categoryFilter.appendChild(option);
    }
  });
  
  // Reset subcategory filter
  subcategoryFilter.innerHTML = '';
  const allSubOption = document.createElement('option');
  allSubOption.value = 'all';
  allSubOption.textContent = 'All';
  subcategoryFilter.appendChild(allSubOption);
  
  // Populate all available subcategories for "All" category
  const allSubcategories = [...new Set(allProducts.map(product => product.subcategory))];
  allSubcategories.forEach(subcategory => {
    if (subcategory) { // Check if subcategory is not empty
      const option = document.createElement('option');
      option.value = subcategory;
      option.textContent = subcategory;
      subcategoryFilter.appendChild(option);
    }
  });
}

// Set up filter event listeners
function setupFilterEventListeners() {
  const categoryFilter = document.getElementById('category-filter');
  const subcategoryFilter = document.getElementById('subcategory-filter');
  
  // Category filter change event
  categoryFilter.addEventListener('change', (e) => {
    const selectedCategory = e.target.value;
    
    // Update subcategory options based on selected category
    updateSubcategoryOptions(selectedCategory);
    
    // Apply filters
    applyFilters(selectedCategory, subcategoryFilter.value);
  });
  
  // Subcategory filter change event
  subcategoryFilter.addEventListener('change', (e) => {
    const selectedCategory = categoryFilter.value;
    const selectedSubcategory = e.target.value;
    
    // Apply filters
    applyFilters(selectedCategory, selectedSubcategory);
  });
}

// Update subcategory options
function updateSubcategoryOptions(category) {
  const subcategoryFilter = document.getElementById('subcategory-filter');
  
  // Clear existing options
  subcategoryFilter.innerHTML = '';
  
  // Add 'All' option first
  const allOption = document.createElement('option');
  allOption.value = 'all';
  allOption.textContent = 'All';
  subcategoryFilter.appendChild(allOption);
  
  // Get subcategories for selected category
  let relevantSubcategories;
  if (category === 'all') {
    relevantSubcategories = [...new Set(allProducts.map(product => product.subcategory))];
  } else {
    relevantSubcategories = [...new Set(
      allProducts
        .filter(product => product.category === category)
        .map(product => product.subcategory)
    )];
  }
  
  // Add subcategory options
  relevantSubcategories.forEach(subcategory => {
    if (subcategory) { // Check if subcategory is not empty
      const option = document.createElement('option');
      option.value = subcategory;
      option.textContent = subcategory;
      subcategoryFilter.appendChild(option);
    }
  });
}

// Apply filters with improved logic
function applyFilters(category = 'all', subcategory = 'all') {
  // Reset current page
  currentPage = 1;
  
  // Filter products
  if (category === 'all' && subcategory === 'all') {
    // If both are "all", show all products
    currentProducts = [...allProducts];
  } else {
    currentProducts = allProducts.filter(product => {
      const categoryMatch = category === 'all' || product.category === category;
      const subcategoryMatch = subcategory === 'all' || product.subcategory === subcategory;
      
      return categoryMatch && subcategoryMatch;
    });
  }
  
  // If no products found, show a message and all products
  if (currentProducts.length === 0) {
    alert('No products found for the selected filters. Showing all products.');
    currentProducts = [...allProducts];
  }
  
  // Display filtered products
  displayProducts(false); // false means don't append, replace all products
  
  // Reinitialize infinite scroll
  initInfiniteScroll();
}

// Remove conflicting event listener from pagination.js
function removeConflictingEventListeners() {
  const categoryFilter = document.getElementById('category-filter');
  const oldListeners = categoryFilter.cloneNode(true);
  categoryFilter.parentNode.replaceChild(oldListeners, categoryFilter);
  
  // Re-add our listeners
  setupFilterEventListeners();
}

// Load products when page loads
document.addEventListener('DOMContentLoaded', () => {
  fetchProducts();
  // Remove conflicting listeners after a short delay
  setTimeout(removeConflictingEventListeners, 100);
});