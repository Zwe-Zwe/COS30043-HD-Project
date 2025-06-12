<template>
  <button 
    class="btn" 
    :class="[inCart ? 'btn-success' : 'btn-primary']" 
    @click.stop="handleAddToCart"
    :disabled="isLoading"
  >
    <span v-if="isLoading" class="spinner-border spinner-border-sm me-1" role="status"></span>
    {{ buttonText }}
  </button>
</template>

<script>
import CartService from '@/services/CartService';
import { isLoggedIn } from '@/utils/auth';
import { useRouter } from 'vue-router';

export default {
  name: 'AddToCartButton',
  props: {
    bookId: {
      type: Number,
      required: true
    },
    quantity: {
      type: Number,
      default: 1
    }
  },
  setup() {
    const router = useRouter();
    return { router };
  },
  data() {
    return {
      inCart: false,
      isLoading: false
    };
  },
  computed: {
    buttonText() {
      if (this.isLoading) return 'Adding...';
      return this.inCart ? 'Added to Cart' : 'Add to Cart';
    }
  },
  async created() {
    await this.checkIfInCart();
  },
  methods: {
    async checkIfInCart() {
      const cart = await CartService.getCart();
      this.inCart = cart.some(item => parseInt(item.book_json_id) === this.bookId);
    },
    async handleAddToCart(event) {
      // Prevent navigation when clicking the button
      event.preventDefault();
      event.stopPropagation();
      
      if (this.isLoading) return;
      
      // Check if user is logged in
      if (!isLoggedIn()) {
        // Redirect to login with redirect back to current page
        this.router.push({
          path: '/login',
          query: { redirect: window.location.pathname }
        });
        return;
      }
      
      this.isLoading = true;
      try {
        await CartService.addToCart(this.bookId, this.quantity);
        this.inCart = true;
        
        // Reset after 2 seconds to allow adding more copies
        setTimeout(() => {
          this.inCart = false;
        }, 2000);
        
        // Emit event for parent components
        this.$emit('added-to-cart', this.bookId);
      } catch (error) {
        console.error('Error adding to cart:', error);
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>
