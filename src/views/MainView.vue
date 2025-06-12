<template>
  <div>
    <!-- Hero Section with Components -->
    <hero-section
      title="Welcome to BookNest"
      subtitle="Discover, purchase, and enjoy the best selection of books"
      cta-text="Browse Collection"
      cta-link="/books"
    />

    <!-- Featured Books Section -->
    <section class="container mb-5">
      <section-header
        title="Featured Books"
        icon="bi bi-stars"
        view-all-link="/books"
      />

      <loading-spinner v-if="loading" message="Loading books..." />

      <div v-else class="featured-books">
        <div class="row g-4">
          <div v-for="(book) in featuredBooks.slice(0, 8)" :key="book.id" 
              class="col-6 col-md-3">
            <!-- Static display version of BookCard (not clickable) -->
            <div class="static-book-card">
              <div class="card-image">
                <img :src="getImageUrl(book.imageLink)" :alt="book.title">
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
                  <router-link :to="`/books?genre=${book.genre || 'Fiction'}`" class="view-more-link">
                    More like this <i class="bi bi-arrow-right"></i>
                  </router-link>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Promotions Banner -->
    <section class="promotion-section mb-5 py-4">
      <div class="container">
        <div class="promotion-card">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="promotion-content">
                <span class="badge bg-danger mb-2">Limited Time</span>
                <h2>Summer Reading Sale</h2>
                <p class="lead">Get 20% off on bestsellers this month!</p>
                <router-link to="/books?sale=true" class="btn btn-gradient">
                  <i class="bi bi-tag-fill me-2"></i>Shop the Sale
                </router-link>
              </div>
            </div>
            <div class="col-md-6">
              <img src="https://source.unsplash.com/random/600x300/?books" alt="Summer sale" class="img-fluid rounded shadow">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section with Animated Cards -->
    <section class="container mb-5">
      <section-header title="Browse by Genre" icon="bi bi-grid-3x3-gap" />
      <div class="row g-4">
        <div 
          v-for="(genre, index) in popularGenres" 
          :key="index" 
          class="col-6 col-md-4 col-lg-3"
        >
          <div class="category-card">
            <div class="category-icon">
              <i :class="getCategoryIcon(genre)"></i>
            </div>
            <h5>{{ genre }}</h5>
            <router-link :to="`/books?genre=${genre}`"></router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Recent News -->
    <section class="container mb-5">
      <section-header title="Book News & Events" icon="bi bi-newspaper" />
      <div class="row">
        <div 
          v-for="(article, index) in newsArticles" 
          :key="index" 
          class="col-md-4 mb-4"
        >
          <div class="card h-100 news-card">
            <div class="news-img-wrapper">
              <img :src="article.image" class="card-img-top" :alt="article.title">
              <div class="news-date">
                <span>{{ article.date }}</span>
              </div>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ article.title }}</h5>
              <p class="card-text">{{ article.excerpt }}</p>
              <a href="#" class="read-more">Read More <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="newsletter-section py-5 mb-4">
      <div class="container">
        <div class="newsletter-card">
          <div class="row justify-content-center">
            <div class="col-md-8 text-center">
              <i class="bi bi-envelope-paper-heart newsletter-icon"></i>
              <h2>Stay Updated</h2>
              <p class="mb-4">Subscribe to our newsletter for exclusive offers and updates</p>
              <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Your email address" aria-label="Email">
                <button class="btn btn-primary" type="button">Subscribe</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import HeroSection from '@/components/HeroSection.vue';
import SectionHeader from '@/components/SectionHeader.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import BookCard from '@/components/BookCard.vue';

export default {
  name: 'MainView',
  components: {
    HeroSection,
    SectionHeader,
    LoadingSpinner,
    BookCard
  },
  data() {
    return {
      loading: true,
      books: [],
      featuredBooks: [],
      popularGenres: [
        'Fiction', 'Romance', 'Mystery', 'Science Fiction', 
        'Fantasy', 'Biography', 'History', 'Poetry'
      ],
      newsArticles: [
        {
          title: 'Summer Reading List 2023',
          excerpt: 'Check out the most anticipated books for your summer reading pleasure.',
          image: 'https://source.unsplash.com/random/300x200/?beach,book',
          date: 'June 15, 2023'
        },
        {
          title: 'Author Spotlight: Jane Doe',
          excerpt: 'Bestselling author Jane Doe talks about her creative process and upcoming projects.',
          image: 'https://source.unsplash.com/random/300x200/?author',
          date: 'June 10, 2023'
        },
        {
          title: 'BookNest Literary Awards',
          excerpt: 'The annual BookNest Literary Awards ceremony will be held next month.',
          image: 'https://source.unsplash.com/random/300x200/?award',
          date: 'June 5, 2023'
        }
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
        this.books = data.books;
        
        // Generate featured books (random selection)
        this.featuredBooks = this.getRandomBooks(this.books, 8);
        this.loading = false;
      } catch (error) {
        console.error('Error fetching books:', error);
        this.loading = false;
      }
    },
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    getRandomBooks(books, count) {
      let shuffled = [...books].sort(() => 0.5 - Math.random());
      return shuffled.slice(0, count);
    },
    getCategoryIcon(genre) {
      // Map genres to Bootstrap icons
      const iconMap = {
        'Fiction': 'bi bi-book',
        'Romance': 'bi bi-heart',
        'Mystery': 'bi bi-question-circle',
        'Science Fiction': 'bi bi-rocket',
        'Fantasy': 'bi bi-stars',
        'Biography': 'bi bi-person',
        'History': 'bi bi-hourglass',
        'Poetry': 'bi bi-chat-quote'
      };
      
      return iconMap[genre] || 'bi bi-book';
    }
  }
};
</script>

<style scoped>
/* Featured Books Section */
.featured-books {
  padding: 20px 0;
}

.static-book-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: white;
  height: 100%;
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  display: flex;
  flex-direction: column;
  cursor: default;
}

.static-book-card:hover {
  box-shadow: 0 15px 30px rgba(0,0,0,0.1);
  transform: translateY(-5px);
}

.static-book-card .card-image {
  position: relative;
  overflow: hidden;
  height: 200px;
}

.static-book-card .card-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 15px;
  background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
}

.static-book-card .card-content {
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  flex-grow: 1;
}

.static-book-card .card-badges {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.static-book-card .genre-badge {
  background-color: #f0f4ff;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  color: #4a6cf7;
  font-weight: 600;
}

.static-book-card .rating-badge {
  color: #f59e0b;
  font-weight: 600;
  font-size: 0.75rem;
}

.static-book-card .rating-badge i {
  font-size: 0.75rem;
  margin-right: 3px;
}

.static-book-card .book-title {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  height: 2.4em;
}

.static-book-card .book-author {
  color: #666;
  font-size: 0.85rem;
  font-weight: 500;
  margin-bottom: 8px;
}

.static-book-card .card-footer {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.static-book-card .price {
  font-weight: 700;
  color: #4a6cf7;
  font-size: 1.1rem;
}

.static-book-card .view-more-link {
  color: #555;
  text-decoration: none;
  font-size: 0.8rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: color 0.3s ease;
  cursor: pointer;
}

.static-book-card .view-more-link:hover {
  color: #4a6cf7;
}

.static-book-card .view-more-link i {
  transition: transform 0.3s ease;
}

.static-book-card .view-more-link:hover i {
  transform: translateX(3px);
}

/* Responsive styling */
@media (max-width: 767.98px) {
  .static-book-card .card-image {
    height: 150px;
  }
  
  .static-book-card .card-content {
    padding: 10px;
  }
  
  .static-book-card .book-title {
    font-size: 0.9rem;
    height: 2.2em;
  }
  
  .static-book-card .book-author {
    font-size: 0.8rem;
  }
  
  .static-book-card .price {
    font-size: 1rem;
  }
}

/* ...existing styles for other sections... */
</style>