<template>
  <div class="section-header d-flex justify-content-between align-items-center mb-4">
    <h2><i v-if="icon" :class="icon"></i> {{ title }}</h2>
    <slot name="action">
      <router-link v-if="viewAllLink" :to="viewAllLink" class="btn btn-outline-primary view-all-btn">
        {{ viewAllText }}
      </router-link>
    </slot>
  </div>
</template>

<script>
export default {
  name: 'SectionHeader',
  props: {
    title: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      default: ''
    },
    viewAllLink: {
      type: String,
      default: ''
    },
    viewAllText: {
      type: String,
      default: 'View All'
    }
  }
}
</script>

<style scoped>
.section-header {
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 5; /* Ensure it's above carousel */
}

.section-header h2 {
  position: relative;
  display: inline-block;
  color: #333;
  font-weight: 700;
}

.view-all-btn {
  position: relative;
  overflow: hidden;
  z-index: 1;
  transition: all 0.3s ease;
}

.view-all-btn:hover {
  color: white;
  background-color: #007bff;
}

/* Mobile adjustments */
@media (max-width: 767.98px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .view-all-btn {
    margin-top: 10px;
  }
}
</style>
