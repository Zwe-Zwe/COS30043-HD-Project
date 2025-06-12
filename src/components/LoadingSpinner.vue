<template>
  <div class="text-center py-5 fade-in">
    <div v-if="type === 'book'" class="loader-container">
      <div class="book">
        <div class="book__page"></div>
        <div class="book__page"></div>
        <div class="book__page"></div>
      </div>
    </div>
    <div v-else class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">{{ message }}</p>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    type: {
      type: String,
      default: 'spinner', // or 'book'
      validator: value => ['spinner', 'book'].includes(value)
    },
    message: {
      type: String,
      default: 'Loading...'
    }
  }
}
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

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
</style>
