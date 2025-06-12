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
            <div>
              <span class="badge" :class="getStatusBadgeClass(order.status)">
                {{ formatStatus(order.status) }}
              </span>
            </div>
          </div>
          
          <div class="card-body">
            <div class="row">
              <!-- Order Items -->
              <div class="col-md-8">
                <h6 class="mb-3">Items</h6>
                <div v-for="item in order.items" :key="item.id" class="d-flex mb-3 border-bottom pb-3">
                  <img :src="getImageUrl(item.imageLink)" class="order-item-img me-3" alt="Book cover" />
                  <div>
                    <h6 class="mb-1">{{ item.title || 'Book #' + item.book_json_id }}</h6>
                    <p class="text-muted mb-1">{{ item.author || 'Unknown author' }}</p>
                    <p class="mb-0">${{ parseFloat(item.price).toFixed(2) }} × {{ item.quantity }}</p>
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
  </div>
</template>

<script>
import OrderService from '@/services/OrderService';
import { isLoggedIn } from '@/utils/auth';

export default {
  name: 'OrderHistoryView',
  data() {
    return {
      orders: [],
      isLoading: true,
      error: null
    };
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
    
    await this.loadOrders();
  },
  methods: {
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
    
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        // Use the correct image path format
        if (imageLink.startsWith('/')) {
          return `http://localhost:3000${imageLink}`;
        } else {
          return `http://localhost:3000/${imageLink}`;
        }
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    
    viewOrderDetails(orderId) {
      this.$router.push({
        path: '/order-confirmation',
        query: { orderId: orderId }
      });
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
