<template>
  <div class="container py-4">
    <h2 class="mb-4">Cart Debug Information</h2>
    
    <div class="card mb-4">
      <div class="card-header">
        <h3>Current User Info</h3>
      </div>
      <div class="card-body">
        <pre>{{ JSON.stringify(currentUser, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="card mb-4">
      <div class="card-header">
        <h3>Raw Cart Data</h3>
      </div>
      <div class="card-body">
        <div v-if="loading">Loading...</div>
        <pre v-else>{{ JSON.stringify(rawCartData, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="card mb-4">
      <div class="card-header">
        <h3>Detailed Cart Data</h3>
      </div>
      <div class="card-body">
        <div v-if="loading">Loading...</div>
        <pre v-else>{{ JSON.stringify(cartWithDetails, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="card mb-4">
      <div class="card-header">
        <h3>Database Schema</h3>
      </div>
      <div class="card-body">
        <div v-if="loadingSchema">Loading...</div>
        <pre v-else>{{ JSON.stringify(dbSchema, null, 2) }}</pre>
      </div>
    </div>
    
    <div class="mt-4">
      <button @click="reloadData" class="btn btn-primary">Reload Data</button>
      <router-link to="/cart" class="btn btn-secondary ms-2">Return to Cart</router-link>
    </div>
  </div>
</template>

<script>
import CartService from '@/services/CartService';
import { getUser } from '@/utils/auth';

export default {
  name: 'CartDebug',
  data() {
    return {
      currentUser: null,
      rawCartData: [],
      cartWithDetails: [],
      dbSchema: null,
      loading: true,
      loadingSchema: true
    };
  },
  async created() {
    this.currentUser = getUser();
    await this.loadCartData();
    await this.loadDatabaseSchema();
  },
  methods: {
    async loadCartData() {
      this.loading = true;
      try {
        // Get raw cart data
        this.rawCartData = await CartService.getCart();
        
        // Get cart with book details
        this.cartWithDetails = await CartService.getCartWithDetails();
      } catch (error) {
        console.error('Error loading cart data:', error);
      } finally {
        this.loading = false;
      }
    },
    async loadDatabaseSchema() {
      this.loadingSchema = true;
      try {
        const response = await fetch('http://localhost/COS30043-HD-Project/api/debug_cart.php');
        this.dbSchema = await response.json();
      } catch (error) {
        console.error('Error loading database schema:', error);
      } finally {
        this.loadingSchema = false;
      }
    },
    async reloadData() {
      await this.loadCartData();
      await this.loadDatabaseSchema();
    }
  }
};
</script>
