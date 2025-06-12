<template>
  <div class="container py-5">
    <div class="text-center mb-5">
      <div class="success-checkmark">
        <div class="check-icon">
          <span class="icon-line line-tip"></span>
          <span class="icon-line line-long"></span>
          <div class="icon-circle"></div>
          <div class="icon-fix"></div>
        </div>
      </div>
      <h2 class="mb-3">Order Confirmation</h2>
      <p class="lead">Thank you for your order!</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mb-4">
          <div class="card-header bg-light">
            <h4 class="mb-0">Order Details</h4>
          </div>
          <div class="card-body">
            <div v-if="isLoading" class="text-center py-3">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
            <div v-else>
              <div class="mb-4">
                <h5>Order #{{ order.id }}</h5>
                <p class="text-muted">Placed on {{ formatDate(order.order_date) }}</p>
              </div>

              <div class="border-bottom mb-4">
                <h5>Items</h5>
                <div v-for="item in order.items" :key="item.id" class="d-flex mb-3">
                  <img :src="getImageUrl(item.imageLink)" class="confirmation-img me-3" alt="Book cover" />
                  <div>
                    <h6 class="mb-0">{{ item.title }}</h6>
                    <p class="text-muted">{{ item.author || 'Unknown author' }}</p>
                    <p>${{ parseFloat(item.price).toFixed(2) }} × {{ item.quantity }}</p>
                  </div>
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-6 border-end">
                  <h5>Shipping Address</h5>
                  <p class="mb-0 whitespace-pre-line">{{ order.shipping_address }}</p>
                </div>
                <div class="col-md-6">
                  <h5>Payment Information</h5>
                  <p class="mb-0" v-if="order.payment_method === 'paypal'">
                    <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" height="20" class="me-1">
                    PayPal
                  </p>
                  <p v-else>{{ formatPaymentMethod(order.payment_method) }}</p>
                  <p>Status: {{ formatPaymentStatus(order.payment_status) }}</p>
                </div>
              </div>

              <div class="d-flex justify-content-between mt-2">
                <span class="h5 mb-0">Total:</span>
                <span class="h5 mb-0">${{ parseFloat(order.total_amount).toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center">
          <router-link to="/books" class="btn btn-primary me-2">Continue Shopping</router-link>
          <router-link to="/order-history" class="btn btn-outline-secondary">View Order History</router-link>
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
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
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
        return 'Awaiting Payment';
      }
      return status;
    }
  }
};
</script>

<style scoped>
.confirmation-img {
  width: 70px;
  height: 100px;
  object-fit: contain;
}

.success-checkmark {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  position: relative;
}
.success-checkmark .check-icon {
  width: 80px;
  height: 80px;
  position: relative;
  border-radius: 50%;
  box-sizing: content-box;
  border: 4px solid #4CAF50;
}
.success-checkmark .check-icon::before {
  top: 3px;
  left: -2px;
  width: 30px;
  transform-origin: 100% 50%;
  border-radius: 100px 0 0 100px;
}
.success-checkmark .check-icon::after {
  top: 0;
  left: 30px;
  width: 60px;
  transform-origin: 0 50%;
  border-radius: 0 100px 100px 0;
  animation: rotate-circle 4.25s ease-in;
}
.success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
  content: '';
  height: 100px;
  position: absolute;
  background: #FFFFFF;
  transform: rotate(-45deg);
}
.success-checkmark .check-icon .icon-line {
  height: 5px;
  background-color: #4CAF50;
  display: block;
  border-radius: 2px;
  position: absolute;
  z-index: 10;
}
.success-checkmark .check-icon .icon-line.line-tip {
  top: 46px;
  left: 14px;
  width: 25px;
  transform: rotate(45deg);
  animation: icon-line-tip 0.75s;
}
.success-checkmark .check-icon .icon-line.line-long {
  top: 38px;
  right: 8px;
  width: 47px;
  transform: rotate(-45deg);
  animation: icon-line-long 0.75s;
}
.success-checkmark .check-icon .icon-circle {
  top: -4px;
  left: -4px;
  z-index: 10;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  position: absolute;
  box-sizing: content-box;
  border: 4px solid rgba(76, 175, 80, .5);
}
.success-checkmark .check-icon .icon-fix {
  top: 8px;
  width: 5px;
  left: 26px;
  z-index: 1;
  height: 85px;
  position: absolute;
  transform: rotate(-45deg);
  background-color: #FFFFFF;
}

@keyframes rotate-circle {
  0% {
    transform: rotate(-45deg);
  }
  5% {
    transform: rotate(-45deg);
  }
  12% {
    transform: rotate(-405deg);
  }
  100% {
    transform: rotate(-405deg);
  }
}

@keyframes icon-line-tip {
  0% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  54% {
    width: 0;
    left: 1px;
    top: 19px;
  }
  70% {
    width: 50px;
    left: -8px;
    top: 37px;
  }
  84% {
    width: 17px;
    left: 21px;
    top: 48px;
  }
  100% {
    width: 25px;
    left: 14px;
    top: 46px;
  }
}

@keyframes icon-line-long {
  0% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  65% {
    width: 0;
    right: 46px;
    top: 54px;
  }
  84% {
    width: 55px;
    right: 0px;
    top: 35px;
  }
  100% {
    width: 47px;
    right: 8px;
    top: 38px;
  }
}

.whitespace-pre-line {
  white-space: pre-line;
}
</style>
