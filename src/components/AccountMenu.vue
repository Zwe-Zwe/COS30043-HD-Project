<template>
  <div class="list-group mb-4">
    <router-link to="/account" class="list-group-item list-group-item-action" :class="{ active: currentPath === '/account' }">
      <i class="bi bi-person me-2"></i> My Profile
    </router-link>
    <router-link to="/order-history" class="list-group-item list-group-item-action" :class="{ active: currentPath === '/order-history' }">
      <i class="bi bi-box-seam me-2"></i> Order History
    </router-link>
    <a href="#" class="list-group-item list-group-item-action" @click.prevent="logout">
      <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
  </div>
</template>

<script>
import { logout } from '@/utils/auth';

export default {
  name: 'AccountMenu',
  data() {
    return {
      currentPath: ''
    };
  },
  created() {
    this.currentPath = this.$route.path;
  },
  watch: {
    '$route.path'(newPath) {
      this.currentPath = newPath;
    }
  },
  methods: {
    logout() {
      logout();
      this.$router.push('/');
    }
  }
};
</script>
