<template>
  <div class="container mt-5">
    <h2 class="mb-4">Create an Account</h2>
    <form @submit.prevent="submitForm">
      <input v-model="formData.name" class="form-control mb-3" type="text" placeholder="Full Name" required />
      <input v-model="formData.email" class="form-control mb-3" type="email" placeholder="Email" required />
      <input v-model="formData.password" class="form-control mb-3" type="password" placeholder="Password" required />
      <textarea v-model="formData.address" class="form-control mb-3" placeholder="Address" required></textarea>
      <button type="submit" class="btn btn-primary" :disabled="isLoading">
        {{ isLoading ? 'Registering...' : 'Register' }}
      </button>
    </form>
    <p class="mt-3">
      Already have an account? <router-link to="/login">Login here</router-link>
    </p>
  </div>
</template>

<script>
export default {
  name: 'UserRegister',
  data() {
    return {
      formData: {
        name: '',
        email: '',
        password: '',
        address: ''
      },
      isLoading: false
    };
  },
  methods: {
    submitForm() {
      this.isLoading = true;
      fetch('http://localhost/COS30043-HD-Project/api/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.formData)
      })
        .then(async res => {
          if (!res.ok) {
            // Try to extract error message from response body
            let errorMsg = `HTTP error! Status: ${res.status}`;
            try {
              const errorData = await res.json();
              if (errorData && errorData.message) {
                errorMsg += ` - ${errorData.message}`;
              }
            } catch (e) {
              // Ignore JSON parse errors
            }
            throw new Error(errorMsg);
          }
          return res.json();
        })
        .then(data => {
          if (data.status === 'success') {
            alert('Registration successful!');
            this.$router.push('/login');
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          alert('An error occurred while registering. ' + error.message);
        })
        .finally(() => {
          this.isLoading = false;
        });
    }
  }
}
</script>