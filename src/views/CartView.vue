<template>
  <div class="container py-4">
    <h2 class="mb-4">Shopping Cart</h2>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading your cart...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="cart.length === 0" class="text-center py-5">
      <p>Your cart is empty.</p>
      <router-link to="/books" class="btn btn-primary mt-3">Browse Books</router-link>
    </div>

    <div v-else>
      <!-- Selection controls -->
      <div class="d-flex justify-content-between mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" v-model="selectAll" @change="toggleSelectAll" id="selectAllItems">
          <label class="form-check-label" for="selectAllItems">
            Select All Items
          </label>
        </div>
        <div>
          <span class="me-2">{{ selectedItems.length }} items selected</span>
        </div>
      </div>

      <!-- Mobile cart items -->
      <div class="d-block d-md-none">
        <div v-for="item in cart" :key="item.id" class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-3">
              <div class="form-check me-2 d-flex align-items-center">
                <input class="form-check-input" type="checkbox" v-model="selectedItems" :value="item.id" :id="`item-mobile-${item.id}`">
              </div>
              <img :src="getImageUrl(item.imageLink)" class="cart-img-mobile me-3" alt="Book cover" />
              <div>
                <h5 class="card-title">{{ item.title }}</h5>
                <p class="card-text mb-1">by {{ item.author }}</p>
                <p class="text-primary fw-bold">${{ item.price.toFixed(2) }}</p>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="quantity-controls d-flex align-items-center">
                <button class="btn btn-sm btn-outline-secondary" @click="decreaseQty(item.id)">-</button>
                <span class="mx-2">{{ item.quantity }}</span>
                <button class="btn btn-sm btn-outline-secondary" @click="increaseQty(item.id)">+</button>
              </div>
              <div>
                <span class="fw-bold">${{ (item.price * item.quantity).toFixed(2) }}</span>
                <button class="btn btn-danger btn-sm ms-2" @click="removeItem(item.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop/tablet cart items -->
      <div class="d-none d-md-block">
        <div
          class="row border rounded p-3 mb-3 align-items-center"
          v-for="item in cart"
          :key="item.id"
        >
          <div class="col-md-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" v-model="selectedItems" :value="item.id" :id="`item-${item.id}`">
            </div>
          </div>
          <div class="col-md-2">
            <img :src="getImageUrl(item.imageLink)" class="img-fluid" alt="Book cover" />
          </div>
          <div class="col-md-3">
            <h5>{{ item.title }}</h5>
            <p>by {{ item.author }}</p>
            <p class="text-muted small">{{ item.genre }} | {{ item.language }}</p>
          </div>
          <div class="col-md-3">
            <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-outline-secondary" @click="decreaseQty(item.id)">-</button>
              <span class="mx-2">{{ item.quantity }}</span>
              <button class="btn btn-sm btn-outline-secondary" @click="increaseQty(item.id)">+</button>
            </div>
          </div>
          <div class="col-md-2">
            <p class="fw-bold">${{ (item.price * item.quantity).toFixed(2) }}</p>
            <p class="text-muted small">${{ item.price.toFixed(2) }} each</p>
          </div>
          <div class="col-md-1 text-end">
            <button class="btn btn-danger btn-sm" @click="removeItem(item.id)">✕</button>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-6">
          <router-link to="/books" class="btn btn-outline-primary">
            Continue Shopping
          </router-link>
        </div>
        <div class="col-6 text-end">
          <h4>Total: ${{ selectedTotal.toFixed(2) }}</h4>
          <p class="text-muted small mb-2">{{ selectedItems.length }} of {{ cart.length }} items selected</p>
          <button class="btn btn-success mt-2" @click="checkout" :disabled="selectedItems.length === 0">
            Checkout Selected Items
          </button>
        </div>
      </div>  
    </div>
  </div>
</template>

<script>
import CartService from '@/services/CartService';
import { isLoggedIn, getUser } from '@/utils/auth';

export default {
  name: 'CartView',
  data() {
    return {
      cart: [],
      isLoading: true,
      error: null,
      cartTotal: 0,
      isAuthenticated: false,
      selectedItems: [], // Array to store selected item ids
      selectAll: false
    };
  },
  computed: {
    selectedTotal() {
      return this.cart
        .filter(item => this.selectedItems.includes(item.id))
        .reduce((total, item) => total + (item.price * item.quantity), 0);
    }
  },
  async created() {
    // Check if user is logged in
    this.isAuthenticated = isLoggedIn();
    
    // If not authenticated, redirect to login
    if (!this.isAuthenticated) {
      this.$router.push({ 
        path: '/login', 
        query: { redirect: '/cart' } 
      });
      return;
    }
    
    // Verify we have user ID
    const user = getUser();
    console.log("Current user:", user);
    
    if (!user || !user.id) {
      this.error = 'Unable to identify your account. Please log in again.';
      this.isLoading = false;
      return;
    }
    
    await this.loadCart();
  },
  methods: {
    async loadCart() {
      this.isLoading = true;
      try {
        // Get cart with full book details
        this.cart = await CartService.getCartWithDetails();
        console.log("Cart data loaded:", this.cart);
        
        if (this.cart.length === 0) {
          console.log("Cart is empty. Checking raw cart data...");
          const rawCart = await CartService.getCart();
          console.log("Raw cart data:", rawCart);
        }
        
        this.calculateTotal();
      } catch (error) {
        console.error('Error loading cart:', error);
        this.error = 'Failed to load your cart. Please try again later.';
      } finally {
        this.isLoading = false;
      }
    },
    
    toggleSelectAll() {
      if (this.selectAll) {
        // Select all items
        this.selectedItems = this.cart.map(item => item.id);
      } else {
        // Deselect all items
        this.selectedItems = [];
      }
    },
    
    calculateTotal() {
      this.cartTotal = this.cart.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },
    
    async increaseQty(bookId) {
      const item = this.cart.find(item => item.id === bookId);
      if (item) {
        item.quantity += 1;
        this.calculateTotal();
        await CartService.updateQuantity(bookId, item.quantity);
      }
    },
    
    async decreaseQty(bookId) {
      const item = this.cart.find(item => item.id === bookId);
      if (item && item.quantity > 1) {
        item.quantity -= 1;
        this.calculateTotal();
        await CartService.updateQuantity(bookId, item.quantity);
      }
    },
    
    async removeItem(bookId) {
      if (confirm('Are you sure you want to remove this item from your cart?')) {
        const success = await CartService.removeFromCart(bookId);
        if (success) {
          this.cart = this.cart.filter(item => item.id !== bookId);
          this.calculateTotal();
        }
      }
    },
    
    getImageUrl(imageLink) {
      // Check if image path is relative or absolute
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    
    checkout() {
      if (!this.isAuthenticated) {
        // Redirect to login if not logged in
        this.$router.push({ 
          path: '/login', 
          query: { redirect: '/checkout' } 
        });
        return;
      }
      
      if (this.selectedItems.length === 0) {
        alert('Please select at least one item to checkout.');
        return;
      }
      
      // Store selected items for checkout
      const selectedItemsData = this.cart.filter(item => 
        this.selectedItems.includes(item.id)
      ).map(item => ({
        bookId: item.id,
        title: item.title,
        author: item.author,
        imageLink: item.imageLink,
        price: item.price,
        quantity: item.quantity
      }));
      
      // Store selected items in sessionStorage for checkout page
      sessionStorage.setItem('checkoutItems', JSON.stringify(selectedItemsData));
      
      // Proceed to checkout
      this.$router.push('/checkout');
    }
  },
  watch: {
    // Watch selectedItems to update selectAll state
    selectedItems(newVal) {
      this.selectAll = newVal.length === this.cart.length;
    },
    // Watch cart to reset selections when cart changes
    cart() {
      this.selectedItems = this.cart.map(item => item.id); // Default select all items
      this.selectAll = true;
    }
  }
};
</script>

<style scoped>
.img-fluid {
  max-height: 150px;
  object-fit: contain;
}

.cart-img-mobile {
  width: 80px;
  height: 80px;
  object-fit: contain;
}

/* Tablet styles */
@media (min-width: 768px) and (max-width: 991.98px) {
  .img-fluid {
    max-height: 120px;
  }
}

/* Mobile styles */
@media (max-width: 767.98px) {
  .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
  }
  
  h4 {
    font-size: 1.25rem;
  }
}

.form-check-input {
  cursor: pointer;
}
</style>

