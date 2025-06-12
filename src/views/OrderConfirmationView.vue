<template>
  <div class="confirmation-view">
    <!-- Loading state -->
    <div v-if="isLoading" class="loading-container">
      <div class="spinner"></div>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="error-container container">
      <div class="error-card">
        <i class="bi bi-exclamation-circle"></i>
        <p>{{ error }}</p>
        <router-link to="/order-history" class="btn btn-outline">
          View Order History
        </router-link>
      </div>
    </div>
    
    <!-- Success state -->
    <div v-else class="container">
      <!-- Success header -->
      <div class="success-header">
        <svg class="checkmark" viewBox="0 0 52 52">
          <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
          <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
        <h1>Thank you for your order!</h1>
        <p>Your order has been received.</p>
      </div>

      <!-- Order details -->
      <div class="order-card">
        <!-- Order header with ID and status -->
        <div class="order-header">
          <div>
            <h2>Order #{{ order.id }}</h2>
            <p>{{ formatDate(order.order_date) }}</p>
          </div>
          <div class="status-badge" :class="getStatusClass(order.status)">
            {{ formatStatus(order.status) }}
          </div>
        </div>
        
        <hr />

        <!-- Items list -->
        <div class="items-list">
          <div class="item" v-for="item in order.items" :key="item.id">
            <div class="item-image">
              <img :src="getImageUrl(item.imageLink)" :alt="item.title" />
            </div>
            <div class="item-details">
              <h3>{{ item.title }}</h3>
              <p>{{ item.author || 'Unknown author' }}</p>
              <div class="item-meta">
                <span>${{ parseFloat(item.price).toFixed(2) }} × {{ item.quantity }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <hr />

        <!-- Order information -->
        <div class="order-info">
          <div class="info-column">
            <h3>Shipping</h3>
            <div class="info-box">
              <p class="whitespace-pre-line">{{ order.shipping_address }}</p>
              <p v-if="order.tracking_number" class="tracking">
                Tracking: {{ order.tracking_number }}
              </p>
            </div>
          </div>
          
          <div class="info-column">
            <h3>Payment</h3>
            <div class="info-box">
              <p>
                <span v-if="order.payment_method === 'paypal'">
                  <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" height="20" class="paypal-logo">
                </span>
                <span v-else>{{ formatPaymentMethod(order.payment_method) }}</span>
              </p>
              <p class="payment-status" :class="getPaymentStatusClass(order.payment_status)">
                {{ formatPaymentStatus(order.payment_status) }}
              </p>
            </div>
            
            <!-- Order Summary -->
            <h3>Summary</h3>
            <div class="summary">
              <div class="summary-row">
                <span>Subtotal:</span>
                <span>${{ getSubtotal().toFixed(2) }}</span>
              </div>
              <div class="summary-row">
                <span>Shipping:</span>
                <span>${{ getShippingCost().toFixed(2) }}</span>
              </div>
              <div class="summary-row">
                <span>Tax:</span>
                <span>${{ getTaxAmount().toFixed(2) }}</span>
              </div>
              <div class="summary-row total">
                <span>Total:</span>
                <span>${{ parseFloat(order.total_amount).toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action buttons -->
        <div class="order-actions">
          <router-link to="/books" class="btn btn-primary">Continue Shopping</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OrderService from '@/services/OrderService';

export default {
  name: 'OrderConfirmationView',
  data() {
    return {
      isLoading: true,
      error: null,
      order: null,
      orderId: null
    };
  },
  created() {
    this.orderId = this.$route.query.orderId;
    if (!this.orderId) {
      this.error = 'No order ID provided';
      this.isLoading = false;
      return;
    }
    
    this.loadOrder();
  },
  methods: {
    async loadOrder() {
      try {
        // Fetch order from database
        this.order = await OrderService.getOrderById(this.orderId);
        
        // Log the order data for debugging
        console.log('Order data:', this.order);
      } catch (error) {
        console.error('Error loading order:', error);
        this.error = 'Failed to load order details';
      } finally {
        this.isLoading = false;
      }
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
    
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
      };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    
    formatShortDate(dateString) {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    
    formatPaymentMethod(method) {
      if (!method) return 'N/A';
      
      const methodMap = {
        'credit-card': 'Credit Card',
        'paypal': 'PayPal',
        'bank-transfer': 'Bank Transfer',
        'cash': 'Cash on Delivery'
      };
      
      return methodMap[method] || method.charAt(0).toUpperCase() + method.slice(1);
    },
    
    formatPaymentStatus(status) {
      if (!status) return 'Unknown';
      
      const statusMap = {
        'paid': 'Payment Completed',
        'unpaid': 'Payment Pending',
        'refunded': 'Refunded',
        'failed': 'Payment Failed'
      };
      
      return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
    },
    
    getPaymentStatusClass(status) {
      if (!status) return '';
      
      const classMap = {
        'paid': 'text-success',
        'unpaid': 'text-warning',
        'refunded': 'text-info',
        'failed': 'text-danger'
      };
      
      return classMap[status] || '';
    },
    
    getPaymentStatusIcon(status) {
      if (!status) return 'bi bi-question-circle';
      
      const iconMap = {
        'paid': 'bi bi-check-circle',
        'unpaid': 'bi bi-hourglass-split',
        'refunded': 'bi bi-arrow-repeat',
        'failed': 'bi bi-x-circle'
      };
      
      return iconMap[status] || 'bi bi-circle';
    },
    
    formatStatus(status) {
      if (!status) return 'Processing';
      
      const statusMap = {
        'pending': 'Order Received',
        'processing': 'Processing',
        'shipped': 'Shipped',
        'delivered': 'Delivered',
        'cancelled': 'Cancelled'
      };
      
      return statusMap[status] || status.charAt(0).toUpperCase() + status.slice(1);
    },
    
    getStatusClass(status) {
      if (!status) return 'status-processing';
      
      const classMap = {
        'pending': 'status-pending',
        'processing': 'status-processing',
        'shipped': 'status-shipped',
        'delivered': 'status-delivered',
        'cancelled': 'status-cancelled'
      };
      
      return classMap[status] || 'status-processing';
    },
    
    getTotalItems() {
      if (!this.order || !this.order.items) return 0;
      
      return this.order.items.reduce((total, item) => {
        return total + (parseInt(item.quantity) || 0);
      }, 0);
    },
    
    getSubtotal() {
      if (!this.order || !this.order.items) return 0;
      
      return this.order.items.reduce((total, item) => {
        return total + (parseFloat(item.price) * parseInt(item.quantity));
      }, 0);
    },
    
    getShippingCost() {
      return this.getSubtotal() > 50 ? 0 : 5.99;
    },
    
    getTaxAmount() {
      return this.getSubtotal() * 0.1;
    },
    
  
  }
};
</script>

<style scoped>
/* Base styles */
.confirmation-view {
  --success-color: #22c55e;
  --primary-color: #0d6efd;
  --neutral-200: #e5e7eb;
  --neutral-300: #d1d5db;
  --neutral-400: #9ca3af;
  --neutral-500: #6b7280;
  --neutral-600: #4b5563;
  --neutral-800: #1f2937;
  --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --radius: 12px;
  
  min-height: 70vh;
  padding: 2rem 0;
}

/* Loading */
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 70vh;
}

.spinner {
  border: 4px solid var(--neutral-200);
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error */
.error-container {
  display: flex;
  justify-content: center;
  padding: 3rem 1rem;
}

.error-card {
  text-align: center;
  max-width: 400px;
  padding: 2rem;
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

.error-card i {
  font-size: 3rem;
  color: #ef4444;
  margin-bottom: 1rem;
}

/* Success Header */
.success-header {
  text-align: center;
  margin-bottom: 2rem;
}

.success-header h1 {
  margin: 1.5rem 0 0.5rem;
  font-size: 1.75rem;
  font-weight: 600;
}

.success-header p {
  color: var(--neutral-500);
}

/* Checkmark Animation */
.checkmark {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: block;
  margin: 0 auto;
}

.checkmark__circle {
  stroke-width: 2;
  stroke: var(--success-color);
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  fill: none;
  animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
  transform-origin: 50% 50%;
  stroke-width: 2;
  stroke: var(--success-color);
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
  100% {
    stroke-dashoffset: 0;
  }
}

/* Order Card */
.order-card {
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 2rem;
  margin-bottom: 2rem;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.order-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.order-header p {
  color: var(--neutral-500);
  margin: 0.25rem 0 0;
  font-size: 0.9rem;
}

.status-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-pending {
  background: #fff8e1;
  color: #f59e0b;
}

.status-processing {
  background: #e8f4fd;
  color: #3b82f6;
}

.status-shipped {
  background: #ecfdf5;
  color: #10b981;
}

.status-delivered {
  background: #d1fae5;
  color: #059669;
}

.status-cancelled {
  background: #fee2e2;
  color: #ef4444;
}

hr {
  border: 0;
  height: 1px;
  background-color: var(--neutral-200);
  margin: 1.5rem 0;
}

/* Items List */
.items-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.item {
  display: flex;
  gap: 1rem;
  padding: 0.5rem;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.item:hover {
  background-color: #f9fafb;
}

.item-image {
  width: 60px;
  height: 90px;
  flex-shrink: 0;
  background: #f9fafb;
  border-radius: 4px;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.item-details {
  flex-grow: 1;
}

.item-details h3 {
  font-size: 1rem;
  margin: 0 0 0.25rem;
  font-weight: 500;
}

.item-details p {
  color: var(--neutral-500);
  font-size: 0.85rem;
  margin: 0 0 0.5rem;
}

.item-meta {
  font-size: 0.85rem;
  color: var(--neutral-600);
}

/* Order Info */
.order-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.info-column h3 {
  font-size: 1rem;
  margin: 0 0 0.75rem;
  font-weight: 500;
}

.info-box {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.info-box p {
  margin: 0.25rem 0;
  font-size: 0.9rem;
}

.tracking {
  color: var(--primary-color);
  font-weight: 500;
  margin-top: 0.5rem !important;
}

.paypal-logo {
  vertical-align: middle;
  margin-right: 0.5rem;
}

.payment-status {
  font-weight: 500;
  margin-top: 0.5rem !important;
}

.text-success { color: #22c55e; }
.text-warning { color: #f59e0b; }
.text-info { color: #0ea5e9; }
.text-danger { color: #ef4444; }

/* Summary */
.summary {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.25rem 0;
  font-size: 0.9rem;
}

.summary-row.total {
  border-top: 1px solid var(--neutral-200);
  margin-top: 0.5rem;
  padding-top: 0.75rem;
  font-weight: 600;
  font-size: 1rem;
  color: var(--neutral-800);
}

/* Order Actions */
.order-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.5rem;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.btn i {
  font-size: 1rem;
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--neutral-300);
  color: var(--neutral-800);
}

.btn-outline:hover {
  background: #f9fafb;
}

.btn-primary {
  background: var(--primary-color);
  border: 1px solid var(--primary-color);
  color: white;
}

.btn-primary:hover {
  opacity: 0.9;
}

.whitespace-pre-line {
  white-space: pre-line;
}

/* Responsive */
@media (max-width: 768px) {
  .order-info {
    grid-template-columns: 1fr;
  }
  
  .order-header {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .order-card {
    padding: 1.25rem;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
