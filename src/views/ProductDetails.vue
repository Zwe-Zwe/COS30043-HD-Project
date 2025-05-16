<template>
  <div v-if="book" class="container my-5">
    <div class="row">
      <div class="col-md-4">
        <img :src="book.image" class="card-img-top" alt="book cover">
      </div>
      <div class="col-md-8">
        
        <h2>{{ book.title }}</h2>
        <h5 class="text-muted">{{ book.author }}</h5>
        <p class="text-primary h5">$ {{ book.price.toFixed(2) }}</p>
        <button class="btn btn-success mt-3">Add to Cart</button>
      </div>
    </div>
  </div>
  <div v-else class="container text-center my-5">
    <p>Loading book details...</p>
  </div>
</template>


<script>
export default {
  name: 'ProductDetails',
  data() {
    return {
      book: null
    };
  },
  mounted() {
    const bookId = this.$route.params.id;
    fetch(`http://localhost:3000/books/${bookId}`)
      .then(res => {
        if (!res.ok) {
          throw new Error(`Book not found: ${res.status} ${res.statusText}`);
        }
        return res.json();
      })
      .then(data => {
        this.book = {
          ...data,
          image: `/${data.imageLink}`, // Prepend / to make absolute path
          id: String(data.id), // Ensure id is string
          link: data.link ? data.link.trim() : '' // Clean up trailing \n
        };
      })
      .catch(err => {
        console.error('Error fetching book:', err);
        this.book = null; // Ensure loading state clears on error
      });
  },
};
</script>

