<template>
  <div class="main-view">
    <!-- Compact Hero with Search -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 hero-content">
            <h1 class="hero-title">Find your next read</h1>
            <div class="search-container mt-4">
              <div class="input-group">
                <input 
                  type="text" 
                  class="form-control form-control-lg" 
                  placeholder="Search books..." 
                  v-model="searchQuery"
                  @keyup.enter="searchBooks"
                >
                <button class="btn btn-primary" type="button" @click="searchBooks">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image-wrapper">
              <div class="floating-book-grid">
                <div class="floating-book" v-for="(book, index) in featuredBooks.slice(0, 3)" :key="index">
                  <img :src="getImageUrl(book.imageLink)" :alt="book.title" class="img-fluid shadow">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Genre Pills -->
    <section class="genre-pills-section">
      <div class="container">
        <div class="genre-pill-container">
          <router-link 
            v-for="(genre, index) in popularGenres" 
            :key="index"
            :to="`/books?genre=${genre}`" 
            class="genre-pill"
          >
            <i :class="getCategoryIcon(genre)"></i>
            <span>{{ genre }}</span>
          </router-link>
          <router-link to="/books" class="genre-pill genre-pill-more">
            <i class="bi bi-grid"></i>
            <span>All Genres</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Featured Books -->
    <section class="book-section">
      <div class="container">
        <div class="section-header-mini">
          <h2>Featured Books</h2>
          <router-link to="/books" class="view-all">View all</router-link>
        </div>
        
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border spinner-sm" role="status"></div>
        </div>

        <div v-else class="book-row">
          <div class="book-card" 
               v-for="book in featuredBooks.slice(0, 5)" 
               :key="book.id"
               @click="goToProductPage(book.id)">
            <div class="book-image">
              <img :src="getImageUrl(book.imageLink)" :alt="book.title">
            </div>
            <div class="book-info">
              <h3 class="book-title">{{ book.title }}</h3>
              <p class="book-author">{{ book.author }}</p>
              <div class="book-price">${{ book.price.toFixed(2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Collection Cards -->
    <section class="collection-section">
      <div class="container">
        <div class="collection-row">
          <div 
            class="collection-card collection-primary" 
            @click="$router.push('/books?new=true')"
          >
            <h3>New Arrivals</h3>
            <div class="collection-link">Explore <i class="bi bi-arrow-right"></i></div>
          </div>
          <div 
            class="collection-card collection-secondary" 
            @click="$router.push('/books?bestseller=true')"
          >
            <h3>Bestsellers</h3>
            <div class="collection-link">Explore <i class="bi bi-arrow-right"></i></div>
          </div>
          <div 
            class="collection-card collection-tertiary" 
            @click="$router.push('/books?sale=true')"
          >
            <h3>On Sale</h3>
            <div class="collection-link">Explore <i class="bi bi-arrow-right"></i></div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'MainView',
  data() {
    return {
      loading: true,
      books: [],
      featuredBooks: [],
      searchQuery: '',
      popularGenres: [
        'Historical Fiction', 'Fantasy', 'Poetry', 
        'Romance', 'Modernist Fiction', 'Classic'
      ]
    };
  },
  mounted() {
    this.fetchBooks();
  },
  
  methods: {
    async fetchBooks() {
      try {
        const response = await fetch('http://localhost/COS30043-HD-Project/api/books.php');
        const data = await response.json();
        
        // Fix: Handle the API response properly regardless of structure
        let booksArray;
        if (Array.isArray(data)) {
          booksArray = data; // Direct array response
        } else if (data.books && Array.isArray(data.books)) {
          booksArray = data.books; // Nested under 'books' property
        } else {
          // Try to find any array in the response that looks like books
          const possibleArrays = Object.values(data).filter(val => Array.isArray(val));
          booksArray = possibleArrays.length > 0 ? possibleArrays[0] : [];
        }
        
        this.books = booksArray;
        console.log('Books loaded:', this.books);
        
        // Get featured books with high ratings
        this.featuredBooks = this.books
          .filter(book => book && (book.rating || 0) >= 4.0)
          .sort(() => 0.5 - Math.random())
          .slice(0, 5);
          
        // If we don't have enough featured books, just take the first 5 books
        if (this.featuredBooks.length < 5) {
          this.featuredBooks = this.books.slice(0, 5);
        }
          
        this.loading = false;
      } catch (error) {
        console.error('Error fetching books:', error);
        this.loading = false;
        
        // Setup default featured books on error
        this.featuredBooks = [
          { id: 1, title: 'The Great Gatsby', author: 'F. Scott Fitzgerald', price: 12.99, rating: 4.5 },
          { id: 2, title: 'To Kill a Mockingbird', author: 'Harper Lee', price: 14.99, rating: 4.8 },
          { id: 3, title: '1984', author: 'George Orwell', price: 11.99, rating: 4.6 },
          { id: 4, title: 'Pride and Prejudice', author: 'Jane Austen', price: 10.99, rating: 4.7 },
          { id: 5, title: 'The Catcher in the Rye', author: 'J.D. Salinger', price: 13.99, rating: 4.4 }
        ];
      }
    },
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    goToProductPage(bookId) {
      this.$router.push(`/product/${bookId}`);
    },
    getCategoryIcon(genre) {
      const iconMap = {
        'Historical Fiction': 'bi bi-book-half',
        'Fantasy': 'bi bi-stars',
        'Poetry': 'bi bi-chat-quote',
        'Romance': 'bi bi-heart',
        'Modernist Fiction': 'bi bi-pencil',
        'Classic': 'bi bi-bookmark'
      };
      
      return iconMap[genre] || 'bi bi-book';
    },
    searchBooks() {
      if (this.searchQuery.trim()) {
        this.$router.push({
          path: '/books',
          query: { search: this.searchQuery.trim() }
        });
      }
    },
    subscribeNewsletter() {
      // Simple confirmation for newsletter subscription
      alert('Thank you for subscribing!');
    }
  }
};
</script>

<style scoped>
/* Main View Styling - Minimalist Version */
.main-view {
  --primary-color: #0d6efd;
  --secondary-color: #6c757d;
  --accent-color: #fd7e14;
  --light-bg: #f8f9fa;
  --dark-bg: #212529;
  --section-spacing: 3rem;
}

/* Hero Section */
.hero-section {
  padding: 3rem 0;
  background-color: var(--light-bg);
  position: relative;
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  line-height: 1.2;
}

.search-container {
  max-width: 500px;
}

.hero-image-wrapper {
  position: relative;
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.floating-book-grid {
  position: relative;
  width: 100%;
  height: 100%;
}

.floating-book {
  position: absolute;
  width: 150px;
  transition: all 0.5s ease;
  transform: rotate(5deg);
}

.floating-book:nth-child(1) {
  top: 0;
  left: 50%;
  transform: translateX(-50%) rotate(-5deg);
  z-index: 3;
}

.floating-book:nth-child(2) {
  top: 50px;
  left: 20%;
  transform: translateX(-50%) rotate(8deg);
  z-index: 2;
}

.floating-book:nth-child(3) {
  top: 80px;
  left: 80%;
  transform: translateX(-50%) rotate(-10deg);
  z-index: 1;
}

.floating-book img {
  width: 100%;
  border-radius: 4px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Genre Pills Section */
.genre-pills-section {
  padding: 1.5rem 0;
  background: white;
}

.genre-pill-container {
  display: flex;
  flex-wrap: nowrap;
  gap: 0.75rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.genre-pill-container::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

.genre-pill {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: var(--light-bg);
  border-radius: 50px;
  text-decoration: none;
  color: var(--dark-bg);
  font-weight: 500;
  transition: all 0.2s ease;
}

.genre-pill:hover {
  background-color: var(--primary-color);
  color: white;
  transform: translateY(-2px);
}

.genre-pill i {
  font-size: 1rem;
}

.genre-pill-more {
  background-color: var(--dark-bg);
  color: white;
}

/* Book Section */
.book-section {
  padding: 2rem 0;
}

.section-header-mini {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header-mini h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
}

.view-all {
  font-weight: 500;
  text-decoration: none;
  color: var(--primary-color);
}

.book-row {
  display: flex;
  overflow-x: auto;
  gap: 1.25rem;
  padding: 0.5rem 0.25rem;
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.book-row::-webkit-scrollbar {
  display: none;
}

.book-card {
  flex: 0 0 auto;
  width: 180px;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.book-card:hover {
  transform: translateY(-5px);
}

.book-image {
  height: 250px;
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  margin-bottom: 0.75rem;
  background-color: var(--light-bg);
}

.book-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.book-info {
  padding: 0.5rem 0;
}

.book-title {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.book-author {
  font-size: 0.8rem;
  color: var(--secondary-color);
  margin-bottom: 0.5rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.book-price {
  font-weight: 700;
  color: var(--primary-color);
  font-size: 0.9rem;
}

/* Collection Section */
.collection-section {
  padding: 2rem 0;
}

.collection-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.collection-card {
  height: 120px;
  border-radius: 8px;
  padding: 1.5rem;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease;
  color: white;
}

.collection-card:hover {
  transform: translateY(-3px);
}

.collection-primary {
  background-color: var(--primary-color);
}

.collection-secondary {
  background-color: var(--dark-bg);
}

.collection-tertiary {
  background-color: var(--accent-color);
}

.collection-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.collection-link {
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  opacity: 0.9;
}

.collection-card:hover .collection-link {
  opacity: 1;
}

/* Newsletter Mini Section */
.newsletter-mini {
  padding: 2.5rem 0;
  background-color: var(--light-bg);
}

.newsletter-content {
  text-align: center;
  max-width: 500px;
  margin: 0 auto;
}

.newsletter-content h2 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.newsletter-form {
  margin: 0 auto;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
  .hero-image-wrapper {
    height: 250px;
    margin-top: 2rem;
  }
  
  .floating-book {
    width: 130px;
  }
  
  .collection-row {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .collection-card:last-child {
    grid-column: span 2;
  }
}

@media (max-width: 767.98px) {
  .hero-section {
    text-align: center;
    padding: 2rem 0;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .search-container {
    margin: 0 auto;
  }
  
  .hero-image-wrapper {
    height: 200px;
  }
  
  .floating-book {
    width: 100px;
  }
  
  .book-card {
    width: 150px;
  }
  
  .book-image {
    height: 200px;
  }
  
  .collection-row {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .collection-card {
    height: 100px;
  }
  
  .collection-card:last-child {
    grid-column: span 1;
  }
}
</style>