<template>
  <div class="text-center py-5 fade-in">
    <div class="empty-container">
      <i :class="icon"></i>
      <p>{{ message }}</p>
      <slot name="action">
        <button v-if="buttonText" class="btn btn-primary" @click="$emit('action-click')">
          {{ buttonText }}
        </button>
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmptyState',
  props: {
    icon: {
      type: String,
      default: 'bi bi-search'
    },
    message: {
      type: String,
      required: true
    },
    buttonText: {
      type: String,
      default: ''
    }
  },
  emits: ['action-click']
}
</script>

<style scoped>
.fade-in {
  animation: fadeIn 1s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.empty-container {
  padding: 40px 20px;
}

.empty-container i {
  font-size: 3rem;
  color: #6c757d;
  margin-bottom: 15px;
  display: block;
}

.empty-container p {
  margin-bottom: 20px;
  color: #495057;
}
</style>
