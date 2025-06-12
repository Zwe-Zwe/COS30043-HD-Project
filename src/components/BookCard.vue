<template>
  <div class="modern-card">
    <div class="card-image">
      <img :src="getImageUrl(book.imageLink)" :alt="book.title">
      <div class="card-overlay">
        <div class="card-quick-actions">
          <router-link :to="`/product/${book.id}`" class="action-btn view-btn">
            <i class="bi bi-eye-fill"></i>
          </router-link>
          <button class="action-btn cart-btn" @click.stop.prevent="addToCartDirectly">
            <i class="bi bi-cart-plus-fill"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="card-content">
      <div class="card-badges">
        <span class="genre-badge">{{ book.genre || 'Fiction' }}</span>
        <span class="rating-badge">
          <i class="bi bi-star-fill"></i> {{ (book.rating || 4.5).toFixed(1) }}
        </span>
      </div>
      <h5 class="book-title">{{ book.title }}</h5>
      <p class="book-author">{{ book.author }}</p>
      <div class="card-footer">
        <div class="price">${{ book.price.toFixed(2) }}</div>
        <router-link :to="`/product/${book.id}`" class="details-link">
          Details <i class="bi bi-arrow-right"></i>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import AddToCartButton from '@/components/AddToCartButton.vue';

export default {
  name: 'BookCard',
  components: {
    AddToCartButton
  },
  props: {
    book: {
      type: Object,
      required: true
    }
  },
  methods: {
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    async addToCartDirectly() {
      this.$emit('book-added-to-cart', this.book.id);
      
      try {
        // Import CartService directly
        const CartService = await import('@/services/CartService').then(m => m.default);
        
        // Check if user is logged in
        const { isLoggedIn } = await import('@/utils/auth');
        
        if (!isLoggedIn()) {
          // Use this.$router directly
          this.$router.push({
            path: '/login',
            query: { redirect: window.location.pathname }
          });
          return;
        }
        
        // Add to cart directly
        await CartService.addToCart(this.book.id, 1);
        
        // Show a small notification
        const notificationDiv = document.createElement('div');
        notificationDiv.textContent = 'Added to cart!';
        notificationDiv.style.cssText = `
          position: fixed;
          bottom: 20px;
          right: 20px;
          background: #17c964;
          color: white;
          padding: 10px 20px;
          border-radius: 5px;
          box-shadow: 0 3px 10px rgba(0,0,0,0.2);
          z-index: 1050;
          animation: fadeOut 3s forwards;
        `;
        
        document.body.appendChild(notificationDiv);
        
        setTimeout(() => {
          document.body.removeChild(notificationDiv);
        }, 3000);
        
      } catch (error) {
        console.error('Error adding to cart:', error);
      }
    }
  }
};
</script>

<style scoped>
.modern-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: white;
  height: 100%;
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  display: flex;
  flex-direction: column;
  /* Ensure cards don't overlap with carousel controls */
  z-index: 1;
}

.modern-card:hover {
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

.card-image {
  position: relative;
  overflow: hidden;
  height: 230px;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.5s ease;
  padding: 15px;
  background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
}

.modern-card:hover .card-image img {
  transform: scale(1.05);
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.modern-card:hover .card-overlay {
  opacity: 1;
}

.card-quick-actions {
  display: flex;
  gap: 15px;
}

.action-btn {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.2rem;
  cursor: pointer;
  transform: translateY(20px);
  opacity: 0;
  transition: all 0.3s ease;
  border: none;
}

.modern-card:hover .action-btn {
  transform: translateY(0);
  opacity: 1;
}

.view-btn {
  background-color: #4a6cf7;
  text-decoration: none;
}

.view-btn:hover {
  background-color: #3255f3;
  color: white;
}

.cart-btn {
  background-color: #17c964;
}

.cart-btn:hover {
  background-color: #0fb857;
}

.card-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 0.875rem;
}

.card-badges {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.genre-badge {
  background-color: #f0f4ff;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  color: #4a6cf7;
  font-weight: 600;
}

.rating-badge {
  color: #f59e0b;
  font-weight: 600;
  font-size: 0.75rem;
}

.rating-badge i {
  font-size: 0.75rem;
  margin-right: 3px;
}

.book-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  height: 2.8em;
}

.book-author {
  color: #666;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 15px;
}

.card-footer {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price {
  font-weight: 700;
  color: #4a6cf7;
  font-size: 1.2rem;
}

.details-link {
  color: #555;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: color 0.3s ease;
}

.details-link:hover {
  color: #4a6cf7;
}

.details-link i {
  transition: transform 0.3s ease;
}

.details-link:hover i {
  transform: translateX(3px);
}

/* Fade out animation for notifications */
@keyframes fadeOut {
  0% {
    opacity: 1;
  }
  70% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

/* Responsive adaptations */
@media (max-width: 767.98px) {
  .card-image {
    height: 180px;
  }

  .card-content {
    padding: 15px;
  }

  .book-title {
    font-size: 1rem;
  }

  .action-btn {
    width: 38px;
    height: 38px;
    font-size: 1rem;
  }
}

/* Fix for carousel display issues */
@media (max-width: 991.98px) {
  .modern-card {
    max-height: 450px;
  }
  
  .card-image {
    height: 180px;
  }
  
  .card-content {
    padding: 12px;
  }
  
  .book-title {
    font-size: 0.95rem;
    height: 2.4em;
  }
  
  .book-author {
    font-size: 0.8rem;
    margin-bottom: 10px;
  }
  
  .price {
    font-size: 1rem;
  }
  
  .details-link {
    font-size: 0.8rem;
  }
}
</style>
