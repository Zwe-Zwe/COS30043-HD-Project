<template>
  <section class="hero-section text-white text-center py-5 mb-4">
    <div class="container">
      <div class="hero-content">
        <h1 class="display-4 fw-bold">{{ title }}</h1>
        <p class="lead">{{ subtitle }}</p>
        <slot name="cta">
          <!-- Default CTA button if none provided -->
          <router-link :to="ctaLink" class="btn btn-primary btn-lg pulse-animation">
            {{ ctaText }}
          </router-link>
        </slot>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      default: ''
    },
    ctaText: {
      type: String,
      default: 'Learn More'
    },
    ctaLink: {
      type: String,
      default: '#'
    },
    backgroundImage: {
      type: String,
      default: 'https://source.unsplash.com/random/1200x600/?library'
    }
  }
}
</script>

<style scoped>
.hero-section {
  background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                    v-bind('`url(${backgroundImage})`');
  background-size: cover;
  background-position: center;
  padding: 80px 0;
  margin-top: -20px;
  position: relative;
  overflow: hidden;
}

.hero-content {
  position: relative;
  z-index: 2;
  animation: fadeIn 1.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Pulse animation for CTA button */
.pulse-animation {
  animation: pulse 2s infinite;
  box-shadow: 0 0 0 rgba(66, 133, 244, 0.4);
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(66, 133, 244, 0.4);
  }
  70% {
    box-shadow: 0 0 0 15px rgba(66, 133, 244, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(66, 133, 244, 0);
  }
}

/* Responsive adaptations */
@media (max-width: 767.98px) {
  .hero-section {
    padding: 60px 0;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .hero-section {
    padding: 80px 0;
  }
}

@media (min-width: 992px) {
  .hero-section {
    padding: 120px 0;
  }
}
</style>
