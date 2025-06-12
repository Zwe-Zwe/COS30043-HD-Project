<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <router-link to="/" class="navbar-brand">BookNest</router-link>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><router-link to="/" class="nav-link">Home</router-link></li>
          <li class="nav-item"><router-link to="/books" class="nav-link">Books</router-link></li>
          <li class="nav-item"><router-link to="/order-history" class="nav-link">Purchase History</router-link></li>
          <li class="nav-item"><router-link to="/account" class="nav-link">Account</router-link></li>
        </ul>
        
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link to="/cart" class="nav-link position-relative">
              <i class="bi bi-cart"></i> Cart
              <span 
                v-if="cartCount > 0" 
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              >
                {{ cartCount }}
              </span>
            </router-link>
          </li>
          <li class="nav-item d-flex">
            <template v-if="!isUserLoggedIn">
              <router-link to="/login" class="nav-link">Login</router-link>
              <router-link to="/register" class="nav-link d-none d-sm-block">Register</router-link>
            </template>
            <button v-else @click="handleLogout" class="nav-link btn btn-link">Logout</button>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { isLoggedIn, logout } from '@/utils/auth';
import { loginEvent } from '@/utils/eventBus';
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import CartService from '@/services/CartService';

export default {
  name: 'AppNavbar',
  setup() {
    const router = useRouter();
    const isUserLoggedIn = ref(isLoggedIn());
    const cartCount = ref(0);
    
    // Subscribe to login events
    let unsubscribe;
    
    // Update cart count function
    const updateCartCount = async () => {
      cartCount.value = await CartService.getCartCount();
    };
    
    // Set up interval to check cart count
    let cartCountInterval;
    
    onMounted(() => {
      // Initialize state
      isUserLoggedIn.value = isLoggedIn();
      
      // Subscribe to login events
      unsubscribe = loginEvent.subscribe((loggedIn) => {
        isUserLoggedIn.value = loggedIn;
      });
      
      // Listen for storage events (for cross-tab login/logout)
      window.addEventListener('storage', handleStorageChange);
      
      // Initial cart count
      updateCartCount();
      
      // Set interval to update cart count
      cartCountInterval = setInterval(updateCartCount, 5000);
    });
    
    onUnmounted(() => {
      if (unsubscribe) unsubscribe();
      window.removeEventListener('storage', handleStorageChange);
      if (cartCountInterval) clearInterval(cartCountInterval);
    });
    
    const handleStorageChange = (event) => {
      if (event.key === 'booknestUser') {
        isUserLoggedIn.value = isLoggedIn();
      }
    };
    
    // Handle logout
    const handleLogout = () => {
      logout();
      router.push('/login');
    };

    return {
      isUserLoggedIn,
      cartCount,
      handleLogout,
    };
  },
};
</script>

<style scoped>
.navbar {
  padding: 10px 15px;
}

.navbar-brand {
  font-weight: bold;
  font-size: 1.5rem;
  color: #42b983;
}

.nav-link {
  font-weight: 500;
  color: #2c3e50;
  padding: 0.5rem 1rem;
}

.nav-link.router-link-exact-active {
  color: #42b983;
}

/* Mobile adjustments */
@media (max-width: 991.98px) {
  .navbar-collapse {
    padding: 1rem 0;
  }
  
  .nav-item {
    padding: 0.25rem 0;
  }
}

/* Tablet adjustments */
@media (min-width: 768px) and (max-width: 991.98px) {
  .navbar {
    padding: 15px 20px;
  }
}

/* Desktop adjustments */
@media (min-width: 992px) {
  .navbar {
    padding: 20px 30px;
  }
}
</style>