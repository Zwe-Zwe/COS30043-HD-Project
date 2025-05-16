<template>
  <section class="bg-primary text-white text-center py-5">
    <div class="container">
      <h1>Welcome to BookNest 📚</h1>
      <p>Your cozy online bookstore. Discover, read, repeat.</p>
    </div>
  </section>

  <div class="container mt-4">
    <div class="row mb-3">
      <div class="col-md-4">
        <label for="genreFilter" class="form-label">Filter by Genre</label>
        <select id="genreFilter" class="form-select" v-model="selectedGenre">
          <option v-for="genre in genres" :key="genre" :value="genre">
            {{ genre }}
          </option>
        </select>
      </div>
    </div>
  </div>


  <section class="container my-5">
    <h2>🌟 Featured Books</h2>
    <div class="row">
      <div class="col-md-3" v-for="book in filteredFeaturedBooks" :key="book.id">
        <router-link :to="`/product/${book.id}`" class="text-decoration-none text-dark">
          <div class="card h-100">
            <img :src="book.imageLink" class="card-img-top" alt="book cover">
            <div class="card-body">
              <h5 class="card-title">{{ book.title }}</h5>
              <p class="card-text">{{ book.author }}</p>
              <p class="card-text text-primary">$ {{ book.price.toFixed(2) }}</p>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>

  <!-- New Arrivals Section -->
  <section class="container my-5">
    <h2>🆕 New Arrivals</h2>
    <div class="row">
      <div class="col-md-3" v-for="book in filteredNewArrivals" :key="book.id">
        <router-link :to="`/product/${book.id}`" class="text-decoration-none text-dark">
          <div class="card h-100">
            <img :src="book.imageLink" class="card-img-top" alt="book cover">
            <div class="card-body">
              <h5 class="card-title">{{ book.title }}</h5>
              <p class="card-text">{{ book.author }}</p>
              <p class="card-text text-primary">$ {{ book.price.toFixed(2) }}</p>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'MainView',
  mounted() {
  fetch('http://localhost:3000/books')
    .then(res => res.json())
    .then(data => {
      this.featuredBooks = data.slice(0, 50); // first 4 books
      this.newArrivals = data.slice(51,99); // rest
    });
},

  data() {
  return {
    featuredBooks: [],
    newArrivals: [],
    selectedGenre: 'All',
    genres: ['All', 'Fiction', 'Self-help']
  };
}
,
  computed: {
  filteredFeaturedBooks() {
    if (this.selectedGenre === 'All') {
      return this.featuredBooks;
    }
    return this.featuredBooks.filter(book => book.genre === this.selectedGenre);
  },
  filteredNewArrivals() {
    if (this.selectedGenre === 'All') {
      return this.newArrivals;
    }
    return this.newArrivals.filter(book => book.genre === this.selectedGenre);
  }
}

};
</script>