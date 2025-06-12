<template>
  <div v-if="loading" class="container my-5 text-center">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-3">Loading book details...</p>
  </div>
  
  <div v-else-if="error" class="container my-5">
    <div class="alert alert-danger" role="alert">
      <h4 class="alert-heading">Error Loading Book</h4>
      <p>{{ error }}</p>
      <hr>
      <router-link to="/books" class="btn btn-primary">Return to Books</router-link>
    </div>
  </div>
  
  <div v-else-if="book" class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
        <li class="breadcrumb-item"><router-link to="/books">Books</router-link></li>
        <li class="breadcrumb-item"><router-link :to="`/books?genre=${book.genre}`">{{ book.genre }}</router-link></li>
        <li class="breadcrumb-item active" aria-current="page">{{ book.title }}</li>
      </ol>
    </nav>

    <div class="row">
      <!-- Left Column: Book Cover and Quick Actions -->
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <img :src="getImageUrl(book.imageLink)" class="card-img-top book-cover" alt="book cover">
          <div class="card-body text-center">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge bg-secondary">{{ book.genre }}</span>
              <div class="rating">
                <i class="bi bi-star-fill text-warning"></i>
                <span class="ms-1">{{ book.rating.toFixed(1) }}</span>
              </div>
            </div>
            <h3 class="text-primary fw-bold price-tag">${{ book.price.toFixed(2) }}</h3>
            <div class="d-grid gap-2">
              <add-to-cart-button 
                :book-id="parseInt(book.id)" 
                :quantity="quantity"
                class="w-100"
                @added-to-cart="handleAddedToCart"
              ></add-to-cart-button>
              <div class="quantity-selector d-flex align-items-center mt-2">
                <button 
                  class="btn btn-outline-secondary" 
                  @click="decreaseQuantity" 
                  :disabled="quantity <= 1"
                >
                  <i class="bi bi-dash"></i>
                </button>
                <span class="mx-3 fw-bold">{{ quantity }}</span>
                <button class="btn btn-outline-secondary" @click="increaseQuantity">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Availability and Shipping Info -->
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="mb-0">Availability</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <span>In Stock</span>
              </li>
              <li class="list-group-item d-flex align-items-center">
                <i class="bi bi-truck me-2"></i>
                <span>Free shipping on orders over $35</span>
              </li>
              <li class="list-group-item d-flex align-items-center">
                <i class="bi bi-arrow-repeat me-2"></i>
                <span>30-day return policy</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      
      <!-- Right Column: Book Details -->
      <div class="col-md-8">
        <h1 class="book-title mb-2">{{ book.title }}</h1>
        <h5 class="text-muted mb-3">by <span class="author-name">{{ book.author }}</span></h5>
        
        <!-- Book details tabs -->
        <ul class="nav nav-tabs mb-4" id="bookDetailsTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link active" 
              id="overview-tab" 
              data-bs-toggle="tab" 
              data-bs-target="#overview" 
              type="button" 
              role="tab" 
              aria-controls="overview" 
              aria-selected="true"
            >
              Overview
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link" 
              id="details-tab" 
              data-bs-toggle="tab" 
              data-bs-target="#details" 
              type="button" 
              role="tab" 
              aria-controls="details" 
              aria-selected="false"
            >
              Details
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link" 
              id="reviews-tab" 
              data-bs-toggle="tab" 
              data-bs-target="#reviews" 
              type="button" 
              role="tab" 
              aria-controls="reviews" 
              aria-selected="false"
            >
              Reviews
            </button>
          </li>
        </ul>
        
        <div class="tab-content" id="bookDetailsTabsContent">
          <!-- Overview Tab -->
          <div 
            class="tab-pane fade show active" 
            id="overview" 
            role="tabpanel" 
            aria-labelledby="overview-tab"
          >
            <div class="book-description">
              <p>{{ generateBookDescription() }}</p>
              
              <div class="key-features mt-4">
                <h5>Key Features:</h5>
                <ul>
                  <li>Written by renowned author {{ book.author }} from {{ book.country }}</li>
                  <li>Originally published in {{ formatYear(book.year) }}</li>
                  <li>{{ book.pages }} pages of captivating content</li>
                  <li>Language: {{ book.language }}</li>
                </ul>
              </div>
              
              <div class="read-more mt-4" v-if="book.link">
                <h5>Read More:</h5>
                <p>
                  Learn more about this book on 
                  <a :href="book.link" target="_blank" rel="noopener noreferrer">Wikipedia</a>.
                </p>
              </div>
            </div>
          </div>
          
          <!-- Details Tab -->
          <div 
            class="tab-pane fade" 
            id="details" 
            role="tabpanel" 
            aria-labelledby="details-tab"
          >
            <table class="table table-striped">
              <tbody>
                <tr>
                  <th scope="row">Title</th>
                  <td>{{ book.title }}</td>
                </tr>
                <tr>
                  <th scope="row">Author</th>
                  <td>{{ book.author }}</td>
                </tr>
                <tr>
                  <th scope="row">Genre</th>
                  <td>{{ book.genre }}</td>
                </tr>
                <tr>
                  <th scope="row">Language</th>
                  <td>{{ book.language }}</td>
                </tr>
                <tr>
                  <th scope="row">Country</th>
                  <td>{{ book.country }}</td>
                </tr>
                <tr>
                  <th scope="row">Year Published</th>
                  <td>{{ formatYear(book.year) }}</td>
                </tr>
                <tr>
                  <th scope="row">Pages</th>
                  <td>{{ book.pages }}</td>
                </tr>
                <tr>
                  <th scope="row">ISBN</th>
                  <td>{{ generateISBN() }}</td>
                </tr>
                <tr>
                  <th scope="row">Rating</th>
                  <td>
                    <div class="stars-container">
                      <div class="stars-outer">
                        <div class="stars-inner" :style="{ width: `${(book.rating / 5) * 100}%` }"></div>
                      </div>
                      <span class="ms-2">{{ book.rating.toFixed(1) }} out of 5</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Reviews Tab -->
          <div 
            class="tab-pane fade" 
            id="reviews" 
            role="tabpanel" 
            aria-labelledby="reviews-tab"
          >
            <div class="reviews-summary mb-4">
              <div class="d-flex align-items-center">
                <h1 class="me-3">{{ book.rating.toFixed(1) }}</h1>
                <div>
                  <div class="stars-container">
                    <div class="stars-outer">
                      <div class="stars-inner" :style="{ width: `${(book.rating / 5) * 100}%` }"></div>
                    </div>
                  </div>
                  <p class="mb-0">Based on {{ randomReviewCount() }} reviews</p>
                </div>
              </div>
            </div>
            
            <!-- Sample Reviews -->
            <div class="reviews-list">
              <div v-for="(review, index) in generateFakeReviews()" :key="index" class="card mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                      <div class="stars-container">
                        <div class="stars-outer">
                          <div class="stars-inner" :style="{ width: `${(review.rating / 5) * 100}%` }"></div>
                        </div>
                      </div>
                      <span class="ms-2 fw-bold">{{ review.rating.toFixed(1) }}</span>
                    </div>
                    <span class="text-muted">{{ review.date }}</span>
                  </div>
                  <h5 class="card-title">{{ review.title }}</h5>
                  <p class="card-text">{{ review.content }}</p>
                  <footer class="blockquote-footer">{{ review.author }}</footer>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Related Books -->
        <div class="related-books mt-5">
          <h3 class="mb-4">You May Also Like</h3>
          <div class="row">
            <div v-for="relatedBook in relatedBooks" :key="relatedBook.id" class="col-md-4 mb-3">
              <div class="card h-100 related-book-card">
                <img :src="getImageUrl(relatedBook.imageLink)" class="card-img-top" :alt="relatedBook.title">
                <div class="card-body">
                  <h5 class="card-title">{{ relatedBook.title }}</h5>
                  <p class="card-text text-muted">{{ relatedBook.author }}</p>
                  <p class="card-text text-primary">${{ relatedBook.price.toFixed(2) }}</p>
                  <router-link :to="`/product/${relatedBook.id}`" class="btn btn-outline-primary btn-sm">
                    View Details
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
      <div 
        id="addToCartToast" 
        class="toast" 
        role="alert" 
        aria-live="assertive" 
        aria-atomic="true"
        ref="toast"
      >
        <div class="toast-header">
          <strong class="me-auto">Success</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          {{ book.title }} has been added to your cart.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AddToCartButton from '@/components/AddToCartButton.vue';

export default {
  name: 'ProductDetails',
  components: {
    AddToCartButton
  },
  data() {
    return {
      book: null,
      loading: true,
      error: null,
      quantity: 1,
      relatedBooks: []
    };
  },
  created() {
    this.fetchBookDetails();
  },
  watch: {
    // Re-fetch when route changes (for related books navigation)
    '$route.params.id': function() {
      this.fetchBookDetails();
    }
  },
  methods: {
    async fetchBookDetails() {
      this.loading = true;
      this.error = null;
      
      const bookId = this.$route.params.id;
      
      try {
        // Fetch book details
        const response = await fetch('http://localhost/COS30043-HD-Project/api/books.php');
        const data = await response.json();
        
        // Find the book with matching ID
        const bookData = data.books.find(book => parseInt(book.id) === parseInt(bookId));
        
        if (!bookData) {
          this.error = `Book with ID ${bookId} not found.`;
          this.loading = false;
          return;
        }
        
        // Process the book data
        this.book = {
          ...bookData,
          id: String(bookData.id), // Ensure id is string
          link: bookData.link ? bookData.link.trim() : '' // Clean up trailing \n
        };
        
        // Generate related books (same author or genre)
        const filteredBooks = data.books.filter(book => 
          (book.author === this.book.author || book.genre === this.book.genre) && 
          book.id !== parseInt(bookId)
        );
        
        // Get random 3 books from filtered list
        this.relatedBooks = this.getRandomItems(filteredBooks, 3);
        
      } catch (error) {
        console.error('Error fetching book details:', error);
        this.error = 'Failed to load book details. Please try again later.';
      } finally {
        this.loading = false;
      }
    },
    
    getImageUrl(imageLink) {
      if (imageLink && !imageLink.startsWith('http')) {
        return `http://localhost:3000/${imageLink}`;
      }
      return imageLink || 'https://via.placeholder.com/150x200?text=No+Image';
    },
    
    formatYear(year) {
      // Handle BC/AD years
      if (year < 0) {
        return `${Math.abs(year)} BC`;
      }
      return year;
    },
    
    increaseQuantity() {
      this.quantity++;
    },
    
    decreaseQuantity() {
      if (this.quantity > 1) {
        this.quantity--;
      }
    },
    
    handleAddedToCart() {
      // eslint-disable-next-line no-undef
      const toast = new bootstrap.Toast(this.$refs.toast);
      toast.show();
    },
    
    generateBookDescription() {
      // Generate a fake book description based on book data
      return `${this.book.title} is a ${this.book.genre.toLowerCase()} book written by ${this.book.author} from ${this.book.country}. Originally published in ${this.formatYear(this.book.year)}, this ${this.book.pages}-page masterpiece has captivated readers with its compelling narrative and unique perspective. Written in ${this.book.language}, the book explores timeless themes that resonate with readers around the world.`;
    },
    
    generateISBN() {
      // Generate a fake ISBN based on book id
      const prefix = "978-0-";
      const mid = Math.floor(Math.random() * 90000) + 10000;
      const suffix = Math.floor(Math.random() * 9000) + 1000;
      return `${prefix}${mid}-${suffix}`;
    },
    
    randomReviewCount() {
      // Generate a random number of reviews
      return Math.floor(Math.random() * 1000) + 50;
    },
    
    generateFakeReviews() {
      // Generate 3 fake reviews
      const reviews = [];
      const reviewTitles = [
        "Excellent read!",
        "Highly recommend",
        "Could not put it down",
        "A masterpiece",
        "Worth every penny"
      ];
      
      const reviewContents = [
        "This book exceeded my expectations. The characters are well-developed and the plot is engaging from start to finish.",
        "I was captivated by the author's writing style. The way they describe scenes makes you feel like you're right there experiencing everything.",
        "A truly remarkable work that will stay with you long after you've finished reading. I've already recommended it to all my friends.",
        "The depth of the story and character development is impressive. It's the kind of book you want to read again to catch all the subtle details.",
        "An absolute gem! The storytelling is masterful and the pacing perfect. One of the best books I've read this year."
      ];
      
      const reviewerNames = [
        "John D.",
        "Emily S.",
        "Michael R.",
        "Sarah L.",
        "Robert J.",
        "Lisa M."
      ];
      
      // Generate random dates for the past year
      const getRandomDate = () => {
        const today = new Date();
        const pastDate = new Date(today.getTime() - Math.random() * 31536000000); // Past year
        return pastDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      };
      
      // Generate random rating weighted around the book's actual rating
      const getRandomRating = () => {
        const baseRating = this.book.rating;
        const variation = Math.random() * 1 - 0.5; // -0.5 to +0.5
        let rating = baseRating + variation;
        // Keep within 1-5 range
        rating = Math.max(1, Math.min(5, rating));
        return Math.round(rating * 10) / 10; // Round to 1 decimal place
      };
      
      // Create 3 reviews
      for (let i = 0; i < 3; i++) {
        reviews.push({
          author: reviewerNames[Math.floor(Math.random() * reviewerNames.length)],
          title: reviewTitles[Math.floor(Math.random() * reviewTitles.length)],
          content: reviewContents[Math.floor(Math.random() * reviewContents.length)],
          rating: getRandomRating(),
          date: getRandomDate()
        });
      }
      
      return reviews;
    },
    
    getRandomItems(array, count) {
      const shuffled = [...array].sort(() => 0.5 - Math.random());
      return shuffled.slice(0, count);
    }
  }
};
</script>

<style scoped>
.book-cover {
  width: 100%;
  height: 250px;
  object-fit: contain;
  padding: 15px;
}

.author-name {
  color: #444;
  font-weight: 500;
}

.price-tag {
  font-size: 1.5rem;
}

.book-title {
  color: #333;
  font-weight: 600;
  font-size: 1.5rem;
}

/* Star Rating Styles */
.stars-outer {
  display: inline-block;
  position: relative;
  font-family: 'Font Awesome 5 Free';
  content: "\f005 \f005 \f005 \f005 \f005";
}

.stars-outer::before {
  content: "★★★★★";
  color: #ccc;
}

.stars-inner {
  position: absolute;
  top: 0;
  left: 0;
  white-space: nowrap;
  overflow: hidden;
  width: 0;
}

.stars-inner::before {
  content: "★★★★★";
  color: #f8ce0b;
}

.related-book-card img {
  height: 120px;
  object-fit: contain;
  padding: 10px;
}

.related-book-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-bottom: 1rem;
}

.related-book-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quantity-selector {
  justify-content: center;
}

.tab-content {
  min-height: 200px;
}

/* Hide breadcrumbs on mobile */
@media (max-width: 767.98px) {
  .breadcrumb {
    display: none;
  }
  
  .book-title {
    font-size: 1.25rem;
    margin-top: 1rem;
  }
  
  .related-books .row {
    margin-left: -5px;
    margin-right: -5px;
  }
  
  .related-books .col-md-4 {
    padding-left: 5px;
    padding-right: 5px;
  }
}

/* Tablet */
@media (min-width: 768px) and (max-width: 991.98px) {
  .book-cover {
    height: 300px;
  }
  
  .related-book-card img {
    height: 150px;
  }
}

/* Desktop */
@media (min-width: 992px) {
  .book-cover {
    height: 400px;
  }
  
  .book-title {
    font-size: 2rem;
  }
  
  .related-book-card img {
    height: 150px;
  }
  
  .tab-content {
    min-height: 300px;
  }
}
</style>

