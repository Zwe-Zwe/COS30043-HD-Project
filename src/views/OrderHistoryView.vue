<template>
  <div class="container py-4">
    <h2 class="mb-4">Order History</h2>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading your orders...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-else-if="orders.length === 0" class="text-center py-5">
      <p>You haven't placed any orders yet.</p>
      <router-link to="/books" class="btn btn-primary mt-3">Browse Books</router-link>
    </div>

    <div v-else>
      <div class="mb-4">
        <div class="card mb-4" v-for="order in orders" :key="order.id">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">Order #{{ order.id }}</h5>
              <p class="text-muted small mb-0">{{ formatDate(order.order_date) }}</p>
            </div>
            <div class="d-flex align-items-center">
              <div class="dropdown me-2">
                <button
                  class="btn btn-sm btn-outline-primary dropdown-toggle"
                  type="button"
                  :id="'editDropdown-' + order.id"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  <i class="bi bi-pencil-square"></i> Edit
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="'editDropdown-' + order.id">
                  <li><h6 class="dropdown-header">Status</h6></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="updateOrderStatus(order.id, 'pending')">Set to Pending</a></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="updateOrderStatus(order.id, 'processing')">Set to Processing</a></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="updateOrderStatus(order.id, 'shipped')">Set to Shipped</a></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="updateOrderStatus(order.id, 'delivered')">Set to Delivered</a></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="updateOrderStatus(order.id, 'cancelled')">Set to Cancelled</a></li>
                  <li><a class="dropdown-item" href="#" @click.stop.prevent="openAddItemModal(order.id)">Add Item</a></li>
                </ul>
              </div>
              <span class="badge" :class="getStatusBadgeClass(order.status)">
                {{ formatStatus(order.status) }}
              </span>
            </div>
          </div>
          
          <div class="card-body">
            <div class="row">
              <!-- Order Items -->
              <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0">Items</h6>
                  <button class="btn btn-sm btn-outline-primary" @click="openAddItemModal(order.id)">
                    Add Item
                  </button>
                </div>
                <div v-for="item in order.items" :key="item.id" class="d-flex mb-3 border-bottom pb-3">
                  <img :src="getImageUrl(item)" class="order-item-img me-3" alt="Book cover" />
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ item.title || getBookById(item.book_json_id)?.title || 'Book #' + item.book_json_id }}</h6>
                    <p class="text-muted mb-1">{{ item.author || getBookById(item.book_json_id)?.author || 'Unknown author' }}</p>
                    <p class="mb-0">${{ parseFloat(item.price).toFixed(2) }} </p>
                  </div>
                  <div class="d-flex align-items-center">
                    <div class="input-group input-group-sm me-2" style="width: 120px;">
                      <button class="btn btn-outline-secondary" type="button" 
                        @click="decreaseQuantity(item)" :disabled="item.quantity <= 1">-</button>
                      <input type="number" class="form-control text-center" v-model="item.quantity" min="1"
                        @change="updateItemQuantity(item)">
                      <button class="btn btn-outline-secondary" type="button" 
                        @click="increaseQuantity(item)">+</button>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" @click="removeOrderItem(order.id, item.id)">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Order Details -->
              <div class="col-md-4">
                <h6 class="mb-3">Order Details</h6>
                <p class="mb-2">
                  <strong>Total:</strong> ${{ parseFloat(order.total_amount).toFixed(2) }}
                </p>
                <p class="mb-2">
                  <strong>Payment Method:</strong> {{ formatPaymentMethod(order.payment_method) }}
                </p>
                <p class="mb-2">
                  <strong>Payment Status:</strong> {{ formatPaymentStatus(order.payment_status) }}
                </p>
                
                <div class="mt-3">
                  <button
                    class="btn btn-sm btn-outline-primary"
                    @click="viewOrderDetails(order.id)">
                    View Details
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addItemModalLabel">Add Item to Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="loadingBooks" class="text-center py-2">
              <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading books...</p>
            </div>
            <div v-else>
              <div class="mb-3">
                <label for="bookSelect" class="form-label">Select Book</label>
                <select class="form-select" id="bookSelect" v-model="newItem.bookId">
                  <option value="" disabled selected>Choose a book</option>
                  <option v-for="book in availableBooks" :key="book.id" :value="book.id">
                    {{ book.title }} - ${{ parseFloat(book.price).toFixed(2) }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="quantityInput" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantityInput" v-model="newItem.quantity" min="1">
              </div>
              <div v-if="selectedBook" class="card p-3 mb-3">
                <div class="d-flex">
                  <img :src="getBookImageUrl(selectedBook)" class="order-item-img me-3" alt="Book cover" />
                  <div>
                    <h6 class="mb-1">{{ selectedBook.title }}</h6>
                    <p class="text-muted mb-1">{{ selectedBook.author }}</p>
                    <p class="mb-0"><strong>Price:</strong> ${{ parseFloat(selectedBook.price).toFixed(2) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="addItemToOrder" 
              :disabled="!newItem.bookId || newItem.quantity < 1">Add Item</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import OrderService from '@/services/OrderService';
import OrderManagementService from '@/services/OrderManagementService';
import { isLoggedIn } from '@/utils/auth';
import { Modal, Dropdown } from 'bootstrap';

export default {
  name: 'OrderHistoryView',
  data() {
    return {
      orders: [],
      isLoading: true,
      error: null,
      availableBooks: [],
      loadingBooks: false,
      addItemModal: null,
      newItem: {
        orderId: null,
        bookId: '',
        quantity: 1
      },
      allBooks: []
    };
  },
  computed: {
    selectedBook() {
      if (!this.newItem.bookId) return null;
      return this.availableBooks.find(book => book.id === this.newItem.bookId);
    }
  },
  async created() {
    // Check if user is logged in
    if (!isLoggedIn()) {
      this.$router.push({ 
        path: '/login', 
        query: { redirect: '/order-history' } 
      });
      return;
    }
    
    // Load all books first for reference
    try {
      this.allBooks = await OrderManagementService.getAvailableBooks();
      console.log('All books loaded:', this.allBooks);
    } catch (error) {
      console.error('Error loading books:', error);
    }
    
    await this.loadOrders();
  },
  mounted() {
    // Initialize Bootstrap components
    this.addItemModal = new Modal(document.getElementById('addItemModal'));
    this.initializeDropdowns();
    
    // Re-initialize dropdowns when the DOM updates
    this.$nextTick(() => {
      window.setTimeout(() => {
        this.initializeDropdowns();
      }, 500);
    });
  },
  updated() {
    // Re-initialize dropdowns after component updates
    this.$nextTick(() => {
      this.initializeDropdowns();
    });
  },
  methods: {
    initializeDropdowns() {
      // Find all dropdown toggles
      const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
      dropdownElementList.forEach(dropdownToggleEl => {
        // Remove disabled attribute if present (fixes greyed out)
        dropdownToggleEl.removeAttribute('disabled');
        
        try {
          // Force destroy any existing dropdown instance
          if (dropdownToggleEl._dropdown) {
            dropdownToggleEl._dropdown.dispose();
          }
          
          // Create new dropdown instance
          new Dropdown(dropdownToggleEl);
        } catch (e) {
          console.error('Error initializing dropdown:', e);
        }
      });
      console.log('Dropdowns initialized:', dropdownElementList.length);
    },
    
    async loadOrders() {
      this.isLoading = true;
      try {
        this.orders = await OrderService.getUserOrders();
        console.log('User orders loaded:', this.orders);
      } catch (error) {
        console.error('Error loading orders:', error);
        this.error = 'Failed to load your order history. Please try again later.';
      } finally {
        this.isLoading = false;
      }
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    formatStatus(status) {
      return status.charAt(0).toUpperCase() + status.slice(1);
    },
    
    getStatusBadgeClass(status) {
      switch (status?.toLowerCase()) {
        case 'processing':
          return 'bg-warning text-dark';
        case 'shipped':
          return 'bg-info text-dark';
        case 'delivered':
          return 'bg-success';
        case 'cancelled':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    
    formatPaymentMethod(method) {
      if (method === 'credit-card') {
        return 'Credit Card';
      } else if (method === 'paypal') {
        return 'PayPal';
      }
      return method;
    },
    
    formatPaymentStatus(status) {
      if (status === 'paid') {
        return 'Paid';
      } else if (status === 'unpaid') {
        return 'Unpaid';
      }
      return status;
    },
    
    // Find a book by its ID
    getBookById(bookId) {
      return this.allBooks.find(book => book.id === bookId);
    },
    
    getImageUrl(item) {
      // Try to get image from item directly
      if (item.imageLink) {
        if (item.imageLink.startsWith('http')) {
          return item.imageLink;
        } else if (item.imageLink.startsWith('/')) {
          return `${window.location.origin}${item.imageLink}`;
        } else {
          return `${window.location.origin}/${item.imageLink}`;
        }
      }
      
      // Try to get image from stored books
      const book = this.getBookById(item.book_json_id);
      if (book && book.imageLink) {
        if (book.imageLink.startsWith('http')) {
          return book.imageLink;
        } else if (book.imageLink.startsWith('/')) {
          return `${window.location.origin}${book.imageLink}`;
        } else {
          return `${window.location.origin}/${book.imageLink}`;
        }
      }
      
      // Fallback to placeholder
      return 'https://via.placeholder.com/150x200?text=No+Image';
    },
    
    viewOrderDetails(orderId) {
      this.$router.push({
        path: '/order-confirmation',
        query: { orderId: orderId }
      });
    },
    
    async updateOrderStatus(orderId, status) {
      try {
        console.log(`Requesting status update for order ${orderId} to ${status}`);
        
        // Create FormData for the request
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', status);
        
        // Use direct axios call
        const response = await axios.post(
          'http://localhost/COS30043-HD-Project/api/update_order_status.php', 
          formData
        );
        
        console.log('Status update response:', response.data);
        
        if (response.data && response.data.success) {
          // Update the local order status
          const orderIndex = this.orders.findIndex(order => order.id === orderId);
          if (orderIndex !== -1) {
            this.orders[orderIndex].status = status;
          }
          // Show success message
          alert(`Order #${orderId} status updated to ${status}`);
        } else {
          alert('Failed to update status: ' + ((response.data && response.data.message) || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error updating order status:', error);
        alert('Failed to update order status. Please try again.');
      }
    },
    
    async openAddItemModal(orderId) {
      this.newItem.orderId = orderId;
      this.newItem.bookId = '';
      this.newItem.quantity = 1;
      
      // Load available books
      this.loadingBooks = true;
      try {
        // Get books directly using fetch for better compatibility with current books.php
        const response = await fetch('http://localhost/COS30043-HD-Project/api/books.php');
        const data = await response.json();
        
        // Handle different response formats
        if (Array.isArray(data)) {
          this.availableBooks = data;
        } else if (data.books && Array.isArray(data.books)) {
          this.availableBooks = data.books;
        } else {
          // Find first array that looks like a books array
          const possibleArrays = Object.values(data).filter(val => 
            Array.isArray(val) && val.length > 0 && typeof val[0] === 'object'
          );
          this.availableBooks = possibleArrays.length > 0 ? possibleArrays[0] : [];
        }
        
        console.log('Available books loaded:', this.availableBooks);
        
        // If no books were found, use hardcoded example books for testing
        if (!this.availableBooks || this.availableBooks.length === 0) {
          console.warn('No books found from API, using example books');
          this.availableBooks = [
            { id: 1, title: 'The Great Gatsby', author: 'F. Scott Fitzgerald', price: 12.99 },
            { id: 2, title: 'To Kill a Mockingbird', author: 'Harper Lee', price: 14.99 },
            { id: 3, title: '1984', author: 'George Orwell', price: 11.99 }
          ];
        }
      } catch (error) {
        console.error('Error loading books:', error);
        // Use hardcoded example books if loading fails
        this.availableBooks = [
          { id: 1, title: 'The Great Gatsby', author: 'F. Scott Fitzgerald', price: 12.99 },
          { id: 2, title: 'To Kill a Mockingbird', author: 'Harper Lee', price: 14.99 },
          { id: 3, title: '1984', author: 'George Orwell', price: 11.99 }
        ];
      } finally {
        this.loadingBooks = false;
      }
      
      this.addItemModal.show();
    },
    
    async addItemToOrder() {
      if (!this.newItem.orderId || !this.newItem.bookId || this.newItem.quantity < 1) return;
      
      try {
        const book = this.availableBooks.find(b => b.id === this.newItem.bookId);
        if (!book) {
          throw new Error('Selected book not found');
        }
        
        await OrderManagementService.addItemToOrder(
          this.newItem.orderId, 
          this.newItem.bookId, 
          this.newItem.quantity, 
          book.price
        );
        
        // Close modal
        this.addItemModal.hide();
        
        // Reload orders to reflect changes
        await this.loadOrders();
        
        // Show success message
        alert('Item added to order successfully');
      } catch (error) {
        console.error('Error adding item to order:', error);
        alert('Failed to add item to order. Please try again.');
      }
    },
    
    async removeOrderItem(orderId, itemId) {
      if (confirm('Are you sure you want to remove this item from the order?')) {
        try {
          await OrderManagementService.removeOrderItem(itemId);
          
          // Find the order and remove the item locally
          const orderIndex = this.orders.findIndex(order => order.id === orderId);
          if (orderIndex !== -1) {
            const order = this.orders[orderIndex];
            // Find item to get its price and quantity for total update
            const item = order.items.find(item => item.id === itemId);
            if (item) {
              // Update order total
              order.total_amount = parseFloat(order.total_amount) - (parseFloat(item.price) * item.quantity);
              // Remove item from order
              order.items = order.items.filter(item => item.id !== itemId);
            }
          }
          
          alert('Item removed from order successfully');
        } catch (error) {
          console.error('Error removing order item:', error);
          alert('Failed to remove item from order. Please try again.');
          // Reload orders to ensure data consistency
          await this.loadOrders();
        }
      }
    },
    
    increaseQuantity(item) {
      item.quantity++;
      this.updateItemQuantity(item);
    },
    
    decreaseQuantity(item) {
      if (item.quantity > 1) {
        item.quantity--;
        this.updateItemQuantity(item);
      }
    },
    
    async updateItemQuantity(item) {
      try {
        console.log(`Sending update for item ${item.id} with quantity ${item.quantity}`);
        
        // First convert quantity to a number if it's not already
        item.quantity = parseInt(item.quantity, 10);
        
        await OrderManagementService.updateOrderItem(item.id, item.quantity);
        
        // Update order total in local state
        const orderIndex = this.orders.findIndex(order => {
          return order.items.some(orderItem => orderItem.id === item.id);
        });
        
        if (orderIndex !== -1) {
          // Recalculate total by summing all items
          const order = this.orders[orderIndex];
          order.total_amount = order.items.reduce((total, item) => {
            return total + (parseFloat(item.price) * item.quantity);
          }, 0);
        }
      } catch (error) {
        console.error('Error updating item quantity:', error);
        alert('Failed to update item quantity. Please try again.');
        // Reload orders to ensure data consistency
        await this.loadOrders();
      }
    },
    
    // New method specifically for getting book images in the modal
    getBookImageUrl(book) {
      if (!book) {
        return 'https://via.placeholder.com/150x200?text=No+Image';
      }
      
      // Try to get image from book
      if (book.imageLink) {
        if (book.imageLink.startsWith('http')) {
          return book.imageLink;
        } else if (book.imageLink.startsWith('/')) {
          return `${window.location.origin}${book.imageLink}`;
        } else {
          return `${window.location.origin}/${book.imageLink}`;
        }
      }
      
      // Fallback to placeholder
      return 'https://via.placeholder.com/150x200?text=No+Image';
    }
  }
};
</script>

<style scoped>
.order-item-img {
  width: 60px;
  height: 80px;
  object-fit: contain;
}

.dropdown-toggle {
  background-color: #fff !important;
  color: #007bff !important;
  border-color: #007bff !important;
  cursor: pointer;
}

.dropdown-toggle:hover {
  background-color: #007bff !important;
  color: #fff !important;
}

.dropdown-header {
  font-weight: 600;
  color: #6c757d;
}

/* Ensure dropdown is visible with higher z-index */
.dropdown-menu {
  z-index: 9999 !important;
}

/* Fix dropdown visibility */
.dropdown {
  position: relative !important;
}

/* Make dropdown button more obvious */
.btn-outline-primary {
  font-weight: 500;
}

@media (max-width: 767.98px) {
  .card-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .badge {
    margin-top: 0.5rem;
  }
}
</style>
