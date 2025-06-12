<template>
  <section class="bg-primary text-white text-center py-5">
    <div class="container">
      <h1>{{ title }}</h1>
      <p>{{ subtitle }}</p>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroBanner',
  props: {
    title: {
      type: String,
      default: 'Welcome to BookNest 📚'
    },
    subtitle: {
      type: String,
      default: 'Your cozy online bookstore. Discover, read, repeat.'
    }
  }
};
</script>