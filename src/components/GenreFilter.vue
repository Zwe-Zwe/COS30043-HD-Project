<template>
  <div class="row mb-3">
    <div class="col-md-4">
      <label :for="id" class="form-label">{{ label }}</label>
      <select :id="id" class="form-select" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
        <option v-for="option in options" :key="option" :value="option">
          {{ option }}
        </option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GenreFilter',
  props: {
    id: {
      type: String,
      default: 'genreFilter'
    },
    label: {
      type: String,
      default: 'Filter by Genre'
    },
    options: {
      type: Array,
      required: true
    },
    modelValue: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue']
};
</script>