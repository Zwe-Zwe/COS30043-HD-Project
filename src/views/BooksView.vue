<template>
  <div class="container py-4">
    <!-- Page Header with Animation -->
    <div class="books-header mb-5 fade-in">
      <h1>Explore Our Collection</h1>
      <p class="lead text-muted">Find your next favorite book in our carefully curated collection</p>
    </div>
    
    <!-- Mobile filter control with animation -->
    <div class="d-block d-md-none mb-3 slide-in-right">
      <button class="btn btn-primary w-100 filter-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
        <i class="bi bi-funnel"></i> Filters & Sorting
      </button>
    </div>
    
    <div class="row mb-4">
      <!-- Mobile offcanvas filters -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="filtersOffcanvasLabel">Filters & Sorting</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <!-- Mobile filters content (same as desktop) -->
          <!-- Search -->
          <div class="mb-3">
            <label for="search" class="form-label">Search</label>
            <input 
              type="text" 
              id="search" 
              class="form-control" 
              v-model="filters.search"
              placeholder="Title, author, etc."
            >
          </div>
          
          <!-- Genre Filter -->
          <div class="mb-3">
            <label class="form-label">Genre</label>
            <div class="overflow-auto" style="max-height: 200px;">
              <div v-for="genre in genres" :key="genre" class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  :id="`genre-${genre}`"
                  :value="genre"
                  v-model="filters.genres"
                >
                <label class="form-check-label" :for="`genre-${genre}`">
                  {{ genre }}
                </label>
              </div>
            </div>
          </div>
          
          <!-- Language Filter -->
          <div class="mb-3">
            <label for="language" class="form-label">Language</label>
            <select id="language" class="form-select" v-model="filters.language">
              <option value="">All Languages</option>
              <option v-for="language in languages" :key="language" :value="language">
                {{ language }}
              </option>
            </select>
          </div>
          
          <!-- Price Range -->
          <div class="mb-3">
            <label class="form-label">Price Range</label>
            <div class="d-flex align-items-center">
              <span class="me-2">$</span>
              <input 
                type="number" 
                class="form-control form-control-sm me-2" 
                v-model.number="filters.minPrice"
                min="0"
                step="0.01"
              >
              <span class="mx-2">to</span>
              <span class="me-2">$</span>
              <input 
                type="number" 
                class="form-control form-control-sm" 
                v-model.number="filters.maxPrice"
                min="0"
                step="0.01"
              >
            </div>
          </div>
          
          <!-- Rating Filter -->
          <div class="mb-3">
            <label class="form-label">Minimum Rating</label>
            <div class="d-flex align-items-center">
              <input 
                type="range" 
                class="form-range me-2"
                min="0"
                max="5"
                step="0.1"
                v-model.number="filters.minRating"
              >
              <span class="badge bg-primary">{{ filters.minRating.toFixed(1) }}</span>
            </div>
          </div>
          
          <button class="btn btn-secondary w-100" @click="resetFilters">Reset Filters</button>
        </div>
      </div>
      
      <!-- Desktop filters sidebar with animation -->
      <div class="col-md-3 d-none d-md-block slide-in-left">
        <div class="filter-card">
          <div class="filter-header">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters</h5>
          </div>
          <div class="filter-body">
            <!-- Search -->
            <div class="mb-3">
              <label for="search" class="form-label">Search</label>
              <input 
                type="text" 
                id="search" 
                class="form-control" 
                v-model="filters.search"
                placeholder="Title, author, etc."
              >
            </div>
            
            <!-- Genre Filter -->
            <div class="mb-3">
              <label class="form-label">Genre</label>
              <div class="overflow-auto" style="max-height: 200px;">
                <div v-for="genre in genres" :key="genre" class="form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    :id="`genre-${genre}`"
                    :value="genre"
                    v-model="filters.genres"
                  >
                  <label class="form-check-label" :for="`genre-${genre}`">
                    {{ genre }}
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Language Filter -->
            <div class="mb-3">
              <label for="language" class="form-label">Language</label>
              <select id="language" class="form-select" v-model="filters.language">
                <option value="">All Languages</option>
                <option v-for="language in languages" :key="language" :value="language">
                  {{ language }}
                </option>
              </select>
            </div>
            
            <!-- Price Range -->
            <div class="mb-3">
              <label class="form-label">Price Range</label>
              <div class="d-flex align-items-center">
                <span class="me-2">$</span>
                <input 
                  type="number" 
                  class="form-control form-control-sm me-2" 
                  v-model.number="filters.minPrice"
                  min="0"
                  step="0.01"
                >
                <span class="mx-2">to</span>
                <span class="me-2">$</span>
                <input 
                  type="number" 
                  class="form-control form-control-sm" 
                  v-model.number="filters.maxPrice"
                  min="0"
                  step="0.01"
                >
              </div>
            </div>
            
            <!-- Rating Filter -->
            <div class="mb-3">
              <label class="form-label">Minimum Rating</label>
              <div class="d-flex align-items-center">
                <input 
                  type="range" 
                  class="form-range me-2"
                  min="0"
                  max="5"
                  step="0.1"
                  v-model.number="filters.minRating"
                >
                <span class="badge bg-primary">{{ filters.minRating.toFixed(1) }}</span>
              </div>
            </div>
            
            <!-- Reset filters button with animation -->
            <button class="btn btn-gradient w-100" @click="resetFilters">
              <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Filters
            </button>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-md-9 slide-in-right">
        <!-- Results summary and sorting with animations -->
        <div class="results-header">
          <div class="found-counter fade-in">
            <span class="badge rounded-pill">{{ filteredBooks.length }}</span> books found
          </div>
          
          <div class="controls-wrapper">
            <div class="sort-control slide-in-right">
              <label for="sortBy" class="me-2">
                <i class="bi bi-sort-alpha-down"></i> Sort:
              </label>
              <select id="sortBy" class="form-select form-select-sm" v-model="sortOption">
                <option value="titleAsc">Title (A-Z)</option>
                <option value="titleDesc">Title (Z-A)</option>
                <option value="priceAsc">Price (Low to High)</option>
                <option value="priceDesc">Price (High to Low)</option>
                <option value="ratingDesc">Rating (High to Low)</option>
                <option value="yearDesc">Newest First</option>
                <option value="yearAsc">Oldest First</option>
              </select>
            </div>
            
            <div class="view-toggle slide-in-right">
              <button 
                class="btn btn-view"
                :class="{ 'active': viewMode === 'grid' }"
                @click="viewMode = 'grid'"
              >
                <i class="bi bi-grid"></i>
              </button>
              <button 
                class="btn btn-view"
                :class="{ 'active': viewMode === 'list' }"
                @click="viewMode = 'list'"
              >
                <i class="bi bi-list-ul"></i>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Use common loading component -->
        <loading-spinner 
          v-if="loading" 
          type="book" 
          message="Loading books..." 
        />
        
        <!-- Use common empty state component -->
        <empty-state
          v-else-if="filteredBooks.length === 0"
          icon="bi bi-search"
          message="No books match your filters. Try adjusting your criteria."
          button-text="Reset Filters"
          @action-click="resetFilters"
        />
        
        <!-- Grid View with staggered animation -->
        <div v-else-if="viewMode === 'grid'" class="row book-grid">
          <div 
            v-for="(book, index) in paginatedBooks" 
            :key="book.id" 
            class="col-6 col-sm-6 col-md-4 col-lg-4 mb-4 book-item"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <book-card :book="book" />
          </div>
        </div>
        
        <!-- List View with fade animations -->
        <div v-else class="list-view">
          <div 
            v-for="(book, index) in paginatedBooks" 
            :key="book.id" 
            class="list-card list-item"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <div class="row g-0">
              <div class="col-md-2">
                <div class="list-img-wrapper">
                  <img :src="getImageUrl(book.imageLink)" class="img-fluid rounded-start" :alt="book.title">
                  <div class="book-badge">
                    <span class="badge bg-primary">{{ book.genre }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title">{{ book.title }}</h5>
                  <p class="card-author">by {{ book.author }}</p>
                  <div class="book-meta">
                    <span class="language"><i class="bi bi-translate"></i> {{ book.language }}</span>
                    <span class="rating"><i class="bi bi-star-fill text-warning"></i> {{ book.rating.toFixed(1) }}</span>
                    <span class="year"><i class="bi bi-calendar"></i> {{ formatYear(book.year) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-2 d-flex flex-column justify-content-center align-items-center p-3">
                <p class="price-tag">${{ book.price.toFixed(2) }}</p>
                <div class="list-actions">
                  <router-link :to="`/product/${book.id}`" class="btn btn-outline-primary btn-sm mb-2 w-100">
                    <i class="bi bi-eye"></i> Details
                  </router-link>
                  <add-to-cart-button :book-id="book.id" class="w-100"></add-to-cart-button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination with animation -->
        <nav v-if="totalPages > 1" class="mt-4 pagination-container fade-in">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <li
              v-for="page in displayedPages"
              :key="page" 
              class="page-item"
              :class="{ active: currentPage === page }"
            >
              <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import BookCard from '@/components/BookCard.vue';
import AddToCartButton from '@/components/AddToCartButton.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import EmptyState from '@/components/EmptyState.vue';

export default {
  name: 'BooksView',
  components: {
    BookCard,
    AddToCartButton,
    LoadingSpinner,
    EmptyState
  },
  data() {
    return {
      loading: true,
      books: [],
      filters: {
        search: '',
        genres: [],
        language: '',
        minPrice: 0,
        maxPrice: 100,
        minRating: 0
      },
      sortOption: 'titleAsc',
      viewMode: 'grid',
      currentPage: 1,
      itemsPerPage: 12,
      genres: [],
      languages: []
    };
  },
  computed: {
    filteredBooks() {
      return this.books.filter(book => {
        // Search filter
        if (this.filters.search) {
          const searchTerm = this.filters.search.toLowerCase();
          const matchesSearch = 
            book.title.toLowerCase().includes(searchTerm) ||
            book.author.toLowerCase().includes(searchTerm) ||
            book.genre.toLowerCase().includes(searchTerm);
          
          if (!matchesSearch) return false;
        }
        
        // Genre filter
        if (this.filters.genres.length > 0 && !this.filters.genres.includes(book.genre)) {
          return false;
        }
        
        // Language filter
        if (this.filters.language && book.language !== this.filters.language) {
          return false;
        }
        
        // Price range filter
        if (book.price < this.filters.minPrice || book.price > this.filters.maxPrice) {
          return false;
        }
        
        // Rating filter
        if (book.rating < this.filters.minRating) {
          return false;
        }
        
        return true;
      }).sort((a, b) => {
        // Sorting
        switch(this.sortOption) {
          case 'titleAsc':
            return a.title.localeCompare(b.title);
          case 'titleDesc':
            return b.title.localeCompare(a.title);
          case 'priceAsc':
            return a.price - b.price;
          case 'priceDesc':
            return b.price - a.price;
          case 'ratingDesc':
            return b.rating - a.rating;
          case 'yearDesc':
            return b.year - a.year;
          case 'yearAsc':
            return a.year - b.year;
          default:
            return 0;
        }
      });
    },
    paginatedBooks() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = startIndex + this.itemsPerPage;
      return this.filteredBooks.slice(startIndex, endIndex);
    },
    totalPages() {
      return Math.ceil(this.filteredBooks.length / this.itemsPerPage);
    },
    displayedPages() {
      // Show a window of 5 pages around the current page
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.totalPages, start + 4);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  mounted() {
    // Initialize Bootstrap components
    if (typeof bootstrap !== 'undefined') {
      // Initialize any Bootstrap components if needed
    }
  },
  async created() {
    // Check for URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('genre')) {
      this.filters.genres = [urlParams.get('genre')];
    }
    
    if (urlParams.has('search')) {
      this.filters.search = urlParams.get('search');
    }
    
    // Load books
    try {
      const response = await fetch('http://localhost/COS30043-HD-Project/api/books.php');
      const data = await response.json();
      
      // Fix: Handle the API response properly regardless of structure
      if (Array.isArray(data)) {
        this.books = data; // Direct array response
      } else if (data.books && Array.isArray(data.books)) {
        this.books = data.books; // Nested under 'books' property
      } else {
        // Try to find any array in the response that looks like books
        const possibleArrays = Object.values(data).filter(val => Array.isArray(val));
        this.books = possibleArrays.length > 0 ? possibleArrays[0] : [];
      }
      
      console.log('Books loaded:', this.books);
      
      // Make sure books is at least an empty array if everything fails
      if (!this.books) {
        this.books = [];
        console.warn('No books found - initialized as empty array');
      }
      
      // Extract unique genres and languages
      this.genres = [...new Set(this.books.map(book => book.genre))].sort();
      this.languages = [...new Set(this.books.map(book => book.language))].sort();
      
      // Set max price to the highest book price (rounded up to nearest 10)
      const maxPrice = Math.ceil(Math.max(...this.books.map(book => book.price || 0)) / 10) * 10 || 100;
      this.filters.maxPrice = maxPrice;
      
      this.loading = false;
    } catch (error) {
      console.error('Error fetching books:', error);
      this.books = []; // Ensure books is at least an empty array
      this.loading = false;
    }
  },
  methods: {
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    resetFilters() {
      this.filters = {
        search: '',
        genres: [],
        language: '',
        minPrice: 0,
        maxPrice: Math.ceil(Math.max(...this.books.map(book => book.price)) / 10) * 10,
        minRating: 0
      };
      this.currentPage = 1;
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        // Scroll to top of results
        window.scrollTo(0, 0);
      }
    },
    formatYear(year) {
      // Handle BC/AD years
      if (year < 0) {
        return `${Math.abs(year)} BC`;
      }
      return year;
    },
    // Fix the addToCartDirectly method
    async addToCartDirectly(bookId) {
      try {
        // Import CartService directly
        const CartService = await import('@/services/CartService').then(m => m.default);
        
        // Check if user is logged in
        const { isLoggedIn } = await import('@/utils/auth');
        
        if (!isLoggedIn()) {
          // Redirect to login with redirect back to current page
          this.$router.push({
            path: '/login',
            query: { redirect: window.location.pathname }
          });
          return;
        }
        
        // Add to cart directly using CartService
        await CartService.addToCart(bookId, 1);
        
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
  },
  watch: {
    // Reset to first page when filters change
    filters: {
      deep: true,
      handler() {
        this.currentPage = 1;
      }
    },
    sortOption() {
      this.currentPage = 1;
    }
  }
};
</script>

<style scoped>
/* Page Header */
.books-header {
  text-align: center;
  padding-bottom: 20px;
  position: relative;
}

.books-header:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 3px;
  background: linear-gradient(90deg, #007bff, #6610f2);
}

/* Animation Classes */
.fade-in {
  animation: fadeIn 1s ease forwards;
}

.slide-in-left {
  animation: slideInLeft 0.8s ease forwards;
}

.slide-in-right {
  animation: slideInRight 0.8s ease forwards;
}

.book-item {
  animation: fadeInUp 0.5s ease forwards;
}

.list-item {
  animation: fadeInUp 0.5s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideInLeft {
  from { opacity: 0; transform: translateX(-30px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes slideInRight {
  from { opacity: 0; transform: translateX(30px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Filter Button */
.filter-btn {
  background: linear-gradient(45deg, #007bff, #6610f2);
  border: none;
  box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
  transition: all 0.3s ease;
}

.filter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
}

/* Filter Card */
.filter-card {
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  background: white;
  transition: all 0.3s ease;
}

.filter-header {
  background: linear-gradient(90deg, #007bff, #6610f2);
  color: white;
  padding: 15px 20px;
}

.filter-body {
  padding: 20px;
}

.btn-gradient {
  background: linear-gradient(45deg, #007bff, #6610f2);
  border: none;
  color: white;
  transition: all 0.3s ease;
}

.btn-gradient:hover {
  background: linear-gradient(45deg, #0069d9, #5a0cbd);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
}

/* Results Header */
.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.found-counter {
  font-weight: 500;
  color: #495057;
}

.found-counter .badge {
  background: linear-gradient(45deg, #007bff, #6610f2);
  font-size: 1rem;
  padding: 6px 12px;
  margin-right: 5px;
}

.controls-wrapper {
  display: flex;
  align-items: center;
  gap: 15px;
}

.sort-control {
  display: flex;
  align-items: center;
}

.view-toggle {
  display: flex;
  gap: 5px;
}

.btn-view {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: white;
  border: 1px solid #dee2e6;
  transition: all 0.3s ease;
}

.btn-view:hover, .btn-view.active {
  background: linear-gradient(45deg, #007bff, #6610f2);
  color: white;
  border-color: transparent;
}

/* Loading Animation */
.loader-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 150px;
}

.book {
  width: 32px;
  height: 12px;
  position: relative;
  perspective: 150px;
}

.book__page {
  position: absolute;
  width: 30px;
  height: 50px;
  top: -25px;
  left: 1px;
  transform-origin: 50% 100%;
  background: linear-gradient(90deg, #007bff, #6610f2);
  animation: flip 1.2s infinite linear;
  animation-fill-mode: forwards;
  backface-visibility: visible;
}

.book__page:nth-child(1) {
  z-index: 3;
  animation-delay: 0s;
}

.book__page:nth-child(2) {
  z-index: 2;
  animation-delay: 0.25s;
}

.book__page:nth-child(3) {
  z-index: 1;
  animation-delay: 0.5s;
}

@keyframes flip {
  0% {
    transform: rotateY(0deg);
    background-position: 0% 50%;
  }
  20% {
    background-position: 100% 50%;
  }
  100% {
    transform: rotateY(-180deg);
    background-position: 0% 50%;
  }
}

/* Empty Results */
.empty-results {
  padding: 40px 20px;
  text-align: center;
}

.empty-results i {
  font-size: 3rem;
  color: #6c757d;
  margin-bottom: 15px;
  display: block;
}

/* Book Grid View */
.book-grid {
  padding: 10px 0;
}

.book-card {
  perspective: 1000px;
  height: 100%;
  border-radius: 15px;
  overflow: hidden;
}

.book-card-inner {
  position: relative;
  width: 100%;
  height: 100%;
  text-align: center;
  transition: transform 0.6s;
  transform-style: preserve-3d;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  min-height: 350px; /* Set a minimum height */
}

.book-card:hover .book-card-inner {
  transform: rotateY(180deg);
}

.book-card-front, .book-card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  border-radius: 15px;
  overflow: hidden;
}

.book-card-front {
  background-color: white;
}

.book-card-front img {
  width: 100%;
  height: 220px;
  object-fit: contain;
  padding: 10px;
}

.book-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
  color: white;
  padding: 15px;
  text-align: left;
}

.book-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.book-author {
  font-size: 0.85rem;
  margin: 5px 0;
}

.book-rating {
  display: inline-block;
  padding: 2px 5px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  font-size: 0.8rem;
  margin-right: 5px;
}

.book-price {
  font-weight: bold;
  color: #fff;
  display: inline-block;
}

.book-card-back {
  background: white;
  color: #333;
  transform: rotateY(180deg);
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.book-details {
  text-align: left;
  margin: 15px 0;
}

.book-details p {
  margin-bottom: 8px;
  font-size: 0.9rem;
}

.book-details i {
  width: 20px;
  color: #007bff;
}

.book-actions {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  gap: 5px;
}

/* List View */
.list-view {
  padding: 10px 0;
}

.list-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.list-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.list-img-wrapper {
  position: relative;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}

.list-img-wrapper img {
  max-height: 150px;
  object-fit: contain;
}

.book-badge {
  position: absolute;
  top: 10px;
  left: 10px;
}

.card-author {
  color: #6c757d;
  margin-bottom: 10px;
}

.book-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  font-size: 0.85rem;
  color: #6c757d;
}

.book-meta span {
  display: flex;
  align-items: center;
  gap: 5px;
}

.price-tag {
  font-weight: bold;
  font-size: 1.25rem;
  color: #007bff;
}

.list-actions {
  width: 100%;
}

/* Pagination */
.pagination-container {
  margin-top: 30px;
}

.pagination .page-link {
  border-radius: 50%;
  margin: 0 5px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #007bff;
  border: 1px solid #dee2e6;
  transition: all 0.3s ease;
}

.pagination .page-link:hover {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination .page-item.active .page-link {
  background: linear-gradient(45deg, #007bff, #6610f2);
  border-color: transparent;
}

/* New Modern Card Styles */
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
}

.modern-card:hover {
  box-shadow: 0 15px 30px rgba(0,0,0,0.1);
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
  flex-grow: 1;
  position: relative;
}

.card-badges {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.genre-badge {
  background-color: #f0f4ff;
  color: #4a6cf7;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.rating-badge {
  color: #f59e0b;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.875rem;
}

.rating-badge i {
  font-size: 0.75rem;
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

/* Responsive adjustments for mobile */
@media (max-width: 767.98px) {
  .results-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .controls-wrapper {
    width: 100%;
    justify-content: space-between;
  }
  
  .book-card-front img {
    height: 180px;
  }
  
  .book-overlay {
    padding: 10px;
  }
  
  .list-card .row {
    flex-direction: column;
  }
  
  .list-img-wrapper {
    height: 180px;
  }
  
  .col-md-8, .col-md-2 {
    width: 100%;
    max-width: 100%;
  }
  
  .price-tag {
    margin-top: 15px;
  }
}

/* Tablet and Desktop Adjustments */
@media (min-width: 768px) {
  .book-card-front img {
    height: 220px;
  }
}

@media (min-width: 992px) {
  .book-card-front img {
    height: 250px;
  }
}
</style>
