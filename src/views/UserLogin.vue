<template>
  <div class="container mt-5">
    <h2>Login to BookNest</h2>
    <form @submit.prevent="submitLogin">
      <input v-model="email" class="form-control my-3" type="email" placeholder="Email" required />
      <input v-model="password" class="form-control my-3" type="password" placeholder="Password" required />
      <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <router-link to="/register">Register</router-link></p>
  </div>
</template>


<script>
import { login } from '@/utils/auth';
import CartService from '@/services/CartService';

export default {
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
    submitLogin() {
      this.isLoading = true;
      fetch('http://localhost/COS30043-HD-Project/api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: this.email, password: this.password })
      })
      .then(res => res.json())
      .then(async data => {
        if (data.status === 'success') {
          // Store user data and update auth state
          login(data.user);
          
          // Merge localStorage cart with database cart
          await CartService.mergeCartsAfterLogin();
          
          // Check if there's a redirect query parameter
          const redirectPath = this.$route.query.redirect || '/';
          this.$router.push(redirectPath);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Login error:', error);
        alert('An error occurred while logging in. Please try again.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    }
  }
};

</script>
