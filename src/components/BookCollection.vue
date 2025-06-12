<template>
  <section :class="containerClass">
    <section-header 
      :title="title" 
      :icon="icon" 
      :view-all-link="viewAllLink" 
      :view-all-text="viewAllText"
    />
    
    <div class="row">
      <div 
        v-for="(book, index) in books" 
        :key="book.id" 
        :class="colClass"
        :data-aos="animation ? 'fade-up' : ''"
        :data-aos-delay="animation ? `${index * 100}` : ''"
      >
        <book-card :book="book" />
      </div>
    </div>
    
    <div v-if="books.length === 0" class="text-center py-4">
      <p class="text-muted">No books available.</p>
    </div>
  </section>
</template>

<script>
import BookCard from './BookCard.vue';
import SectionHeader from './SectionHeader.vue';

export default {
  name: 'BookCollection',
  components: {
    BookCard,
    SectionHeader
  },
  props: {
    title: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      default: ''
    },
    books: {
      type: Array,
      required: true
    },
    viewAllLink: {
      type: String,
      default: ''
    },
    viewAllText: {
      type: String,
      default: 'View All'
    },
    containerClass: {
      type: String,
      default: 'container mb-5'
    },
    colClass: {
      type: String,
      default: 'col-md-3 mb-4'
    },
    animation: {
      type: Boolean,
      default: false
    }
  }
};
</script>

<style scoped>
/* Add any specific styles needed for the collection layout */
</style>