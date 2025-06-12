<template>
  <footer class="footer mt-auto py-4">
    <div class="container">
      <div class="row">
        <!-- Brand and About -->
        <div class="col-md-3 mb-4 mb-md-0">
          <h5 class="footer-heading">BookNest</h5>
          <p class="text-muted">
            Your destination for quality books at great prices. Discover new worlds one page at a time.
          </p>
          <div class="social-links">
            <a href="#" class="me-2" aria-label="Facebook">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#" class="me-2" aria-label="Twitter">
              <i class="bi bi-twitter-x"></i>
            </a>
            <a href="#" class="me-2" aria-label="Instagram">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#" aria-label="LinkedIn">
              <i class="bi bi-linkedin"></i>
            </a>
          </div>
        </div>
        
        <!-- Quick Links -->
        <div class="col-6 col-md-3 mb-4 mb-md-0">
          <h5 class="footer-heading">Quick Links</h5>
          <ul class="list-unstyled">
            <li><router-link to="/" class="footer-link">Home</router-link></li>
            <li><router-link to="/books" class="footer-link">Browse Books</router-link></li>
            <li><router-link to="/cart" class="footer-link">Shopping Cart</router-link></li>
            <li><router-link to="/account" class="footer-link">My Account</router-link></li>
          </ul>
        </div>
        
        <!-- Customer Service -->
        <div class="col-6 col-md-3 mb-4 mb-md-0">
          <h5 class="footer-heading">Customer Service</h5>
          <ul class="list-unstyled">
            <li><router-link to="/contact" class="footer-link">Contact Us</router-link></li>
            <li><router-link to="/shipping" class="footer-link">Shipping & Returns</router-link></li>
            <li><router-link to="/faq" class="footer-link">FAQ</router-link></li>
            <li><router-link to="/privacy" class="footer-link">Privacy Policy</router-link></li>
          </ul>
        </div>
        
        <!-- Newsletter -->
        <div class="col-md-3">
          <h5 class="footer-heading">Newsletter</h5>
          <p class="text-muted">Subscribe for updates and special offers</p>
          <form @submit.prevent="subscribeNewsletter">
            <div class="input-group mb-3">
              <input 
                type="email" 
                class="form-control" 
                placeholder="Your email" 
                aria-label="Email address for newsletter" 
                v-model="newsletterEmail"
                required
              >
              <button 
                class="btn btn-primary" 
                type="submit" 
                :disabled="isSubscribing"
              >
                <i class="bi bi-send-fill"></i>
              </button>
            </div>
          </form>
          <p v-if="subscriptionMessage" class="small" :class="subscriptionStatus">
            {{ subscriptionMessage }}
          </p>
        </div>
      </div>
      
      <hr class="my-4">
      
      <!-- Copyright and Bottom Links -->
      <div class="row">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0">© {{ currentYear }} BookNest. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <router-link to="/terms" class="footer-link small">Terms of Use</router-link>
            </li>
            <li class="list-inline-item">
              <span class="footer-separator">|</span>
            </li>
            <li class="list-inline-item">
              <router-link to="/privacy" class="footer-link small">Privacy Policy</router-link>
            </li>
            <li class="list-inline-item">
              <span class="footer-separator">|</span>
            </li>
            <li class="list-inline-item">
              <router-link to="/accessibility" class="footer-link small">Accessibility</router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  data() {
    return {
      newsletterEmail: '',
      isSubscribing: false,
      subscriptionMessage: '',
      subscriptionStatus: '',
      currentYear: new Date().getFullYear()
    };
  },
  methods: {
    subscribeNewsletter() {
      this.isSubscribing = true;
      
      // Simulate API call
      setTimeout(() => {
        // Success simulation
        this.subscriptionMessage = 'Thank you for subscribing!';
        this.subscriptionStatus = 'text-success';
        this.newsletterEmail = '';
        this.isSubscribing = false;
        
        // Clear message after 3 seconds
        setTimeout(() => {
          this.subscriptionMessage = '';
        }, 3000);
      }, 1000);
    }
  }
};
</script>

<style scoped>
.footer {
  background-color: #f8f9fa;
  margin-top: 3rem;
  border-top: 1px solid #e9ecef;
}

.footer-heading {
  font-weight: 600;
  font-size: 1.1rem;
  margin-bottom: 1rem;
  color: #495057;
}

.footer-link {
  color: #6c757d;
  text-decoration: none;
  transition: color 0.2s ease;
  display: inline-block;
  margin-bottom: 0.5rem;
}

.footer-link:hover {
  color: #0d6efd;
  text-decoration: underline;
}

.footer-separator {
  color: #dee2e6;
  margin: 0 0.5rem;
}

.social-links a {
  color: #6c757d;
  font-size: 1.2rem;
  transition: color 0.2s ease;
}

.social-links a:hover {
  color: #0d6efd;
}

/* Mobile responsiveness */
@media (max-width: 767.98px) {
  .footer {
    text-align: center;
  }
  
  .footer-heading {
    margin-top: 1rem;
  }
}
</style>
