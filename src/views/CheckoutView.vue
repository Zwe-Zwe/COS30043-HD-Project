<template>
  <div class="checkout-page">
    <!-- Header section with progress indicator -->
    <div class="checkout-header">
      <div class="container">
        <h1>Checkout</h1>
        <div class="checkout-progress">
          <div class="progress-step completed">
            <div class="step-indicator">1</div>
            <div class="step-label">Cart</div>
          </div>
          <div class="progress-line completed"></div>
          <div class="progress-step active">
            <div class="step-indicator">2</div>
            <div class="step-label">Checkout</div>
          </div>
          <div class="progress-line"></div>
          <div class="progress-step">
            <div class="step-indicator">3</div>
            <div class="step-label">Confirmation</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main checkout content -->
    <div class="container checkout-container">
      <!-- Loading state -->
      <div v-if="isLoading" class="checkout-loader">
        <div class="loader-spinner"></div>
        <p>Preparing your checkout...</p>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="checkout-error">
        <div class="error-icon">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h2>Oops! Something went wrong</h2>
        <p>{{ error }}</p>
        <router-link to="/cart" class="btn-outline">Return to Cart</router-link>
      </div>
      
      <!-- Empty cart state -->
      <div v-else-if="!checkoutItems.length" class="checkout-empty">
        <div class="empty-icon">
          <i class="bi bi-cart-x"></i>
        </div>
        <h2>Your cart is empty</h2>
        <p>Add some books to your cart first</p>
        <router-link to="/cart" class="btn-primary">Return to Cart</router-link>
      </div>
      
      <!-- Checkout content -->
      <div v-else class="checkout-content">
        <div class="checkout-main">
          <div class="checkout-section shipping-section">
            <div class="section-header">
              <h2><i class="bi bi-geo-alt"></i> Shipping Information</h2>
            </div>
            <div class="section-content">
              <form @submit.prevent>
                <div class="form-row">
                  <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" v-model="shippingInfo.firstName" required>
                  </div>
                  <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" v-model="shippingInfo.lastName" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" v-model="shippingInfo.email" required>
                </div>
                
                <div class="form-group">
                  <label for="address">Street Address</label>
                  <input type="text" id="address" v-model="shippingInfo.address" required>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" v-model="shippingInfo.city" required>
                  </div>
                  <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" v-model="shippingInfo.state" required>
                  </div>
                  <div class="form-group">
                    <label for="zip">ZIP Code</label>
                    <input type="text" id="zip" v-model="shippingInfo.zip" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" id="phone" v-model="shippingInfo.phone" required>
                </div>
              </form>
            </div>
          </div>
          
          <div class="checkout-section payment-section">
            <div class="section-header">
              <h2><i class="bi bi-credit-card"></i> Payment Method</h2>
            </div>
            <div class="section-content">
              <div class="payment-methods">
                <label class="payment-method" :class="{ active: paymentMethod === 'paypal' }">
                  <input type="radio" name="payment" value="paypal" v-model="paymentMethod" class="sr-only">
                  <div class="payment-method-content">
                    <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" alt="PayPal" class="payment-logo">
                    <span>PayPal</span>
                  </div>
                </label>
                
                <label class="payment-method" :class="{ active: paymentMethod === 'credit-card' }">
                  <input type="radio" name="payment" value="credit-card" v-model="paymentMethod" class="sr-only">
                  <div class="payment-method-content">
                    <i class="bi bi-credit-card payment-icon"></i>
                    <span>Credit Card</span>
                  </div>
                </label>
              </div>
              
              <!-- PayPal Details -->
              <div v-if="paymentMethod === 'paypal'" class="payment-details">
                <div class="paypal-info">
                  <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" class="paypal-logo">
                  <p class="payment-tagline">The safer, easier way to pay</p>
                </div>
                
                <div class="form-group">
                  <label for="paypalEmail">PayPal Email</label>
                  <input type="email" id="paypalEmail" v-model="paymentInfo.email" placeholder="email@example.com" required>
                </div>
                
                <div class="info-note">
                  <i class="bi bi-info-circle"></i>
                  <p>This is a demo checkout. No actual payment will be processed.</p>
                </div>
              </div>
              
              <!-- Credit Card Details -->
              <div v-if="paymentMethod === 'credit-card'" class="payment-details">
                <div class="form-group">
                  <label for="cardName">Name on Card</label>
                  <input type="text" id="cardName" v-model="paymentInfo.cardName" required>
                </div>
                
                <div class="form-group card-number-group">
                  <label for="cardNumber">Card Number</label>
                  <input type="text" id="cardNumber" v-model="paymentInfo.cardNumber" placeholder="1234 5678 9012 3456" required>
                  <span class="card-type-icon">
                    <i class="bi bi-credit-card"></i>
                  </span>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="expiry">Expiration Date</label>
                    <input type="text" id="expiry" v-model="paymentInfo.expiry" placeholder="MM/YY" required>
                  </div>
                  <div class="form-group">
                    <label for="cvv">Security Code</label>
                    <input type="text" id="cvv" v-model="paymentInfo.cvv" placeholder="CVV" required>
                    <span class="cvv-hint">3 or 4 digits on card back</span>
                  </div>
                </div>
                
                <div class="info-note">
                  <i class="bi bi-shield-lock"></i>
                  <p>Your payment information is secure and encrypted</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Order summary -->
        <div class="order-summary">
          <div class="summary-header">
            <h2>Order Summary</h2>
          </div>
          
          <div class="summary-content">
            <div class="summary-items">
              <div v-for="item in checkoutItems" :key="item.bookId" class="summary-item">
                <div class="item-image">
                  <img :src="getImageUrl(item.imageLink)" :alt="item.title">
                </div>
                <div class="item-details">
                  <h3 class="item-title">{{ item.title }}</h3>
                  <p class="item-author">{{ item.author }}</p>
                  <div class="item-price">
                    <span class="price">${{ item.price.toFixed(2) }}</span>
                    <span class="quantity">× {{ item.quantity }}</span>
                    <span class="total">${{ (item.price * item.quantity).toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="summary-totals">
              <div class="summary-row">
                <span>Subtotal</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="summary-row">
                <span>Shipping</span>
                <span>${{ shipping.toFixed(2) }}</span>
              </div>
              <div class="summary-row total">
                <span>Order Total</span>
                <span>${{ orderTotal.toFixed(2) }}</span>
              </div>
            </div>
            
            <div class="summary-actions">
              <router-link to="/cart" class="btn-link">
                <i class="bi bi-arrow-left"></i> Back to Cart
              </router-link>
              
              <button 
                class="btn-primary place-order" 
                @click="placeOrder"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting" class="btn-spinner"></span>
                <span>{{ isSubmitting ? 'Processing...' : paymentButtonText }}</span>
              </button>
            </div>
            
            <div class="secure-checkout">
              <i class="bi bi-lock-fill"></i>
              <span>Secure Checkout</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { isLoggedIn, getUser } from '@/utils/auth';
import CartService from '@/services/CartService';
import OrderService from '@/services/OrderService';

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
:root {
  --primary: #0d6efd;
  --primary-light: #e6f0ff;
  --primary-dark: #0b5ed7;
  --success: #198754;
  --danger: #dc3545;
  --warning: #ffc107;
  --text-dark: #212529;
  --text-muted: #6c757d;
  --border-color: #dee2e6;
  --bg-light: #f8f9fa;
  --bg-white: #ffffff;
  --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  --radius: 0.375rem;
}

/* Page Layout */
.checkout-page {
  background-color: #f8f9fa;
  min-height: 100vh;
}

.checkout-header {
  background: #ffffff;
  padding: 2rem 0;
  border-bottom: 1px solid #e9ecef;
  margin-bottom: 2rem;
}

.checkout-header h1 {
  margin: 0 0 1.5rem;
  font-weight: 600;
  font-size: 1.75rem;
  text-align: center;
}

.checkout-container {
  padding-bottom: 3rem;
}

.checkout-progress {
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 500px;
  margin: 0 auto;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.step-indicator {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: #e9ecef;
  border: 2px solid #ced4da;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.step-label {
  font-size: 0.85rem;
  color: #6c757d;
}

.progress-step.active .step-indicator {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: #ffffff;
}

.progress-step.active .step-label {
  color: #0d6efd;
  font-weight: 500;
}

.progress-step.completed .step-indicator {
  background-color: #198754;
  border-color: #198754;
  color: #ffffff;
}

.progress-line {
  flex: 1;
  height: 2px;
  background-color: #e9ecef;
  margin: 0 10px;
  position: relative;
  top: -15px;
}

.progress-line.completed {
  background-color: #198754;
}

/* Checkout Content Layout */
.checkout-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 1.5rem;
  align-items: start;
}

.checkout-main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Checkout Sections */
.checkout-section {
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  overflow: hidden;
}

.section-header {
  padding: 1rem 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.section-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-header h2 i {
  color: #0d6efd;
}

.section-content {
  padding: 1.5rem;
}

/* Form Elements */
.form-group {
  margin-bottom: 1.25rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  margin-bottom: 0;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
}

input {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

input:focus {
  color: #495057;
  background-color: #fff;
  border-color: #86b7fe;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.cvv-hint {
  display: block;
  font-size: 0.75rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.payment-method {
  flex: 1;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  padding: 0;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
  overflow: hidden;
}

.payment-method-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  gap: 0.75rem;
}

.payment-method:hover {
  border-color: #86b7fe;
}

.payment-method.active {
  border-color: #0d6efd;
  background-color: rgba(13, 110, 253, 0.05);
}

.payment-logo {
  height: 25px;
}

.payment-icon {
  font-size: 1.75rem;
  color: #0d6efd;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.payment-details {
  background: #f8f9fa;
  padding: 1.25rem;
  border-radius: 0.25rem;
  margin-top: 1rem;
}

.paypal-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 1.25rem;
}

.paypal-logo {
  height: 26px;
  margin-bottom: 0.5rem;
}

.payment-tagline {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0;
}

.info-note {
  display: flex;
  align-items: flex-start;
  padding: 0.75rem;
  background: rgba(13, 110, 253, 0.1);
  border-radius: 0.25rem;
  margin-top: 1.25rem;
  color: #084298;
}

.info-note i {
  margin-right: 0.75rem;
  font-size: 1.1rem;
  flex-shrink: 0;
  margin-top: 0.1rem;
}

.info-note p {
  margin: 0;
  font-size: 0.875rem;
  line-height: 1.5;
}

.card-number-group {
  position: relative;
}

.card-type-icon {
  position: absolute;
  right: 0.75rem;
  top: 2.5rem;
  color: #6c757d;
}

/* Order Summary */
.order-summary {
  position: sticky;
  top: 1.5rem;
  background: #ffffff;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  overflow: hidden;
}

.summary-header {
  padding: 1rem 1.5rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.summary-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.summary-content {
  padding: 1.5rem;
}

.summary-items {
  max-height: 350px;
  overflow-y: auto;
  margin-bottom: 1.5rem;
  padding-right: 0.5rem;
}

.summary-item {
  display: flex;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e9ecef;
}

.summary-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 50px;
  height: 70px;
  flex-shrink: 0;
  background: #f8f9fa;
  border-radius: 0.25rem;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-title {
  margin: 0 0 0.25rem;
  font-size: 0.9rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-author {
  margin: 0 0 0.5rem;
  font-size: 0.8rem;
  color: #6c757d;
}

.item-price {
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-price .price {
  color: #0d6efd;
  font-weight: 600;
}

.item-price .quantity {
  color: #6c757d;
}

.item-price .total {
  margin-left: auto;
  font-weight: 700;
}

.summary-totals {
  padding: 1rem 0;
  border-top: 1px solid #e9ecef;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.summary-row.total {
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid #e9ecef;
  font-weight: 700;
  font-size: 1.1rem;
}

.summary-actions {
  margin-top: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.secure-checkout {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 0 0 0.5rem 0.5rem;
  margin: 1.5rem -1.5rem -1.5rem;
  color: #6c757d;
  font-size: 0.875rem;
}

.secure-checkout i {
  margin-right: 0.5rem;
}

/* Buttons */
.btn-primary,
.btn-link,
.btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  font-weight: 500;
  border-radius: 0.25rem;
  transition: all 0.15s ease-in-out;
  gap: 0.5rem;
  text-decoration: none;
}

.btn-primary {
  background-color: #0d6efd;
  border: 1px solid #0d6efd;
  color: #ffffff;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.btn-primary:disabled {
  background-color: #6c757d;
  border-color: #6c757d;
  cursor: not-allowed;
}

.btn-link {
  background: transparent;
  border: none;
  color: #0d6efd;
  padding: 0.5rem 0;
  align-self: flex-start;
}

.btn-link:hover {
  color: #0a58ca;
  text-decoration: underline;
}

.btn-outline {
  background: transparent;
  border: 1px solid #0d6efd;
  color: #0d6efd;
}

.btn-outline:hover {
  background-color: #0d6efd;
  color: #ffffff;
}

.place-order {
  width: 100%;
  padding: 0.75rem;
}

/* Loading, Error and Empty States */
.checkout-loader,
.checkout-error,
.checkout-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
}

.loader-spinner,
.btn-spinner {
  border: 0.25rem solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 0.25rem solid #ffffff;
  width: 2rem;
  height: 2rem;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

.btn-spinner {
  width: 1rem;
  height: 1rem;
  border-width: 0.2rem;
  margin: 0 0.5rem 0 0;
}

.loader-spinner {
  border-color: rgba(13, 110, 253, 0.3);
  border-top-color: #0d6efd;
}

.error-icon,
.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.error-icon {
  color: #dc3545;
}

.empty-icon {
  color: #6c757d;
}

.checkout-error h2,
.checkout-empty h2 {
  margin-bottom: 0.5rem;
}

.checkout-error p,
.checkout-empty p {
  margin-bottom: 1.5rem;
  color: #6c757d;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 991.98px) {
  .checkout-content {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .order-summary {
    position: static;
    order: -1;
  }
}

@media (max-width: 767.98px) {
  .checkout-progress {
    width: 100%;
  }

  .progress-step .step-label {
    font-size: 0.75rem;
  }

  .payment-methods {
    flex-direction: column;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .payment-method-content {
    flex-direction: row;
    gap: 0.5rem;
  }
}

@media (max-width: 575.98px) {
  .checkout-header {
    padding: 1.5rem 0;
  }
  
  .checkout-header h1 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .section-header {
    padding: 0.75rem 1rem;
  }
  
  .section-header h2 {
    font-size: 1.1rem;
  }
  
  .section-content,
  .summary-content {
    padding: 1rem;
  }
}
</style>
