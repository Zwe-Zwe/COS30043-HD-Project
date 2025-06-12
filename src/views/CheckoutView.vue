<template>
  <div class="container py-4">
    <h2 class="mb-4">Checkout</h2>

    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Preparing checkout...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>
    
    <div v-else-if="!checkoutItems.length" class="text-center py-5">
      <p>No items to checkout.</p>
      <router-link to="/cart" class="btn btn-primary mt-3">Return to Cart</router-link>
    </div>
    
    <div v-else class="row">
      <!-- Order summary column -->
      <div class="col-md-4 order-md-2 mb-4">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0">Order Summary</h4>
          </div>
          <div class="card-body">
            <div v-for="item in checkoutItems" :key="item.bookId" class="d-flex mb-3 border-bottom pb-2">
              <img :src="getImageUrl(item.imageLink)" class="checkout-img me-2" alt="Book cover" />
              <div class="flex-grow-1">
                <h6 class="my-0">{{ item.title }}</h6>
                <small class="text-muted">{{ item.author }}</small>
                <div class="d-flex justify-content-between">
                  <span>${{ item.price.toFixed(2) }} × {{ item.quantity }}</span>
                  <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-between mt-3">
              <span>Subtotal</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            
            <div class="d-flex justify-content-between">
              <span>Shipping</span>
              <span>${{ shipping.toFixed(2) }}</span>
            </div>
            
            <div class="d-flex justify-content-between mt-3 pt-3 border-top">
              <strong>Total</strong>
              <strong>${{ orderTotal.toFixed(2) }}</strong>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Checkout form column -->
      <div class="col-md-8 order-md-1">
        <form @submit.prevent="placeOrder">
          <!-- Shipping Information -->
          <div class="card mb-4">
            <div class="card-header">
              <h4 class="mb-0">Shipping Information</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="firstName" class="form-label">First name</label>
                  <input type="text" class="form-control" id="firstName" v-model="shippingInfo.firstName" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="lastName" class="form-label">Last name</label>
                  <input type="text" class="form-control" id="lastName" v-model="shippingInfo.lastName" required>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" v-model="shippingInfo.email" required>
              </div>
              
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" v-model="shippingInfo.address" required>
              </div>
              
              <div class="row">
                <div class="col-md-5 mb-3">
                  <label for="city" class="form-label">City</label>
                  <input type="text" class="form-control" id="city" v-model="shippingInfo.city" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="state" class="form-label">State</label>
                  <input type="text" class="form-control" id="state" v-model="shippingInfo.state" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="zip" class="form-label">Zip</label>
                  <input type="text" class="form-control" id="zip" v-model="shippingInfo.zip" required>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="phone" v-model="shippingInfo.phone" required>
              </div>
            </div>
          </div>
          
          <!-- Payment Information -->
          <div class="card mb-4">
            <div class="card-header">
              <h4 class="mb-0">Payment Method</h4>
            </div>
            <div class="card-body">
              <!-- PayPal Mock System -->
              <div class="payment-methods">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" id="paypal" value="paypal" v-model="paymentMethod" checked>
                  <label class="form-check-label" for="paypal">
                    <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal" height="23">
                    PayPal
                  </label>
                </div>
                
                <div v-if="paymentMethod === 'paypal'" class="paypal-container mt-3 p-3 border rounded">
                  <div class="text-center mb-3">
                    <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal Logo" height="26">
                    <p class="text-muted small mt-2">The safer, easier way to pay</p>
                  </div>
                  
                  <div class="mb-3">
                    <label for="paypalEmail" class="form-label">PayPal Email Address</label>
                    <input type="email" class="form-control" id="paypalEmail" v-model="paymentInfo.email" required placeholder="email@example.com">
                  </div>
                  
                  <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-2"></i>
                    This is a mock PayPal integration for demonstration purposes. No real payments will be processed.
                  </div>
                </div>
                
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" id="creditCard" value="credit-card" v-model="paymentMethod">
                  <label class="form-check-label" for="creditCard">
                    <i class="bi bi-credit-card me-1"></i>
                    Credit Card
                  </label>
                </div>
                
                <div v-if="paymentMethod === 'credit-card'" class="credit-card-container">
                  <div class="mb-3">
                    <label for="cardName" class="form-label">Name on card</label>
                    <input type="text" class="form-control" id="cardName" v-model="paymentInfo.cardName" required>
                  </div>
                  
                  <div class="mb-3">
                    <label for="cardNumber" class="form-label">Card number</label>
                    <input type="text" class="form-control" id="cardNumber" v-model="paymentInfo.cardNumber" required>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="expiry" class="form-label">Expiration</label>
                      <input type="text" class="form-control" id="expiry" placeholder="MM/YY" v-model="paymentInfo.expiry" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="cvv" class="form-label">CVV</label>
                      <input type="text" class="form-control" id="cvv" v-model="paymentInfo.cvv" required>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center mb-4">
            <router-link to="/cart" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Return to Cart
            </router-link>
            <button type="submit" class="btn btn-success" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ isSubmitting ? 'Processing...' : paymentButtonText }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { isLoggedIn, getUser } from '@/utils/auth';
import CartService from '@/services/CartService';
import OrderService from '@/services/OrderService'; // Import OrderService

export default {
  name: 'CheckoutView',
  data() {
    return {
      isLoading: true,
      isSubmitting: false,
      error: null,
      checkoutItems: [],
      shipping: 5.99,
      paymentMethod: 'paypal', // Set default to PayPal
      shippingInfo: {
        firstName: '',
        lastName: '',
        email: '',
        address: '',
        city: '',
        state: '',
        zip: '',
        phone: ''
      },
      paymentInfo: {
        cardName: '',
        cardNumber: '',
        expiry: '',
        cvv: '',
        email: '' // Add email for PayPal
      }
    };
  },
  computed: {
    subtotal() {
      return this.checkoutItems.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    orderTotal() {
      return this.subtotal + this.shipping;
    },
    paymentButtonText() {
      return this.paymentMethod === 'paypal' ? 'Pay with PayPal' : 'Place Order';
    }
  },
  async created() {
    // Check if user is logged in
    if (!isLoggedIn()) {
      this.$router.push({ 
        path: '/login', 
        query: { redirect: '/checkout' } 
      });
      return;
    }
    
    // Get user details to pre-fill shipping info
    const user = getUser();
    if (!user || !user.id) {
      this.error = 'Unable to identify your account. Please log in again.';
      this.isLoading = false;
      return;
    }
    
    // Load checkout items from session storage
    try {
      const checkoutItemsJson = sessionStorage.getItem('checkoutItems');
      if (checkoutItemsJson) {
        this.checkoutItems = JSON.parse(checkoutItemsJson);
      } else {
        this.error = 'No items found for checkout. Please return to your cart.';
      }
      
      // Pre-fill user information if available
      if (user.email) {
        this.shippingInfo.email = user.email;
      }
      
    } catch (error) {
      console.error('Error loading checkout items:', error);
      this.error = 'Failed to prepare checkout. Please try again.';
    } finally {
      this.isLoading = false;
    }
  },
  methods: {
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    
    async placeOrder() {
      this.isSubmitting = true;
      this.error = null; // Clear any previous errors
      
      try {
        let paymentDetails = {};
        if (this.paymentMethod === 'paypal') {
          paymentDetails = {
            method: 'paypal',
            email: this.paymentInfo.email,
            transactionId: 'PP-' + Math.random().toString(36).substr(2, 9).toUpperCase()
          };
        } else {
          paymentDetails = {
            method: 'credit-card',
            cardLast4: this.paymentInfo.cardNumber.slice(-4)
          };
        }
        
        const orderData = {
          items: this.checkoutItems,
          shipping: this.shippingInfo,
          payment: paymentDetails,
          subtotal: this.subtotal,
          shippingCost: this.shipping,
          total: this.orderTotal
        };
        
        console.log('Submitting order:', orderData);
        
        // Save order to database
        const result = await OrderService.createOrder(orderData);
        
        console.log('Order result:', result);
        
        if (result && result.success) {
          // Clear items from cart that were purchased
          const bookIds = this.checkoutItems.map(item => item.bookId);
          for (const id of bookIds) {
            await CartService.removeFromCart(id);
          }
          
          // Clear checkout items from session storage
          sessionStorage.removeItem('checkoutItems');
          
          // Navigate to order confirmation
          this.$router.push({
            path: '/order-confirmation',
            query: { orderId: result.orderId }
          });
        } else {
          throw new Error('Failed to create order: ' + (result?.message || 'Unknown error'));
        }
      } catch (error) {
        console.error('Error placing order:', error);
        this.error = 'Failed to place order: ' + (error.message || 'Please try again later.');
        window.scrollTo(0, 0);
      } finally {
        this.isSubmitting = false;
      }
    }
  }
};
</script>

<style scoped>
.checkout-img {
  width: 60px;
  height: 80px;
  object-fit: contain;
}

@media (max-width: 767.98px) {
  .order-md-1 {
    order: 2;
  }
  
  .order-md-2 {
    order: 1;
    margin-bottom: 1.5rem;
  }
}

.paypal-container {
  background-color: #f8f9fa;
  border-color: #ddd !important;
}

.payment-methods {
  margin-bottom: 1rem;
}

.payment-methods .form-check {
  padding: 0.5rem;
  border-radius: 0.25rem;
  transition: background-color 0.2s;
}

.payment-methods .form-check:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.payment-methods .form-check-input:checked + .form-check-label {
  font-weight: bold;
}
</style>
