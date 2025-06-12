<template>
  <div class="container py-4">
    <h2 class="mb-4">My Account</h2>

    <div class="row">
      <!-- Account Menu -->
      <div class="col-md-3">
        <AccountMenu />
      </div>

      <!-- Account Content -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header">
            <h4 class="mb-0">My Profile</h4>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-3">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            
            <div v-else-if="user">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" v-model="user.name" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" v-model="user.email" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label">Member Since</label>
                <input type="text" class="form-control" :value="formatDate(user.created_at)" readonly />
              </div>

              <div class="d-flex mt-4">
                <button class="btn btn-primary me-2" @click="editProfile">
                  Edit Profile
                </button>
                <router-link to="/order-history" class="btn btn-outline-primary me-2">
                  View Order History
                </router-link>
                <button @click="logout" class="btn btn-outline-danger">
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateProfile">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" v-model="formData.name" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" v-model="formData.email" required>
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" rows="3" v-model="formData.address"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="updateProfile" :disabled="updating">
              <span v-if="updating" class="spinner-border spinner-border-sm me-1" role="status"></span>
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { getUser, logout as authLogout } from '@/utils/auth';
import AccountMenu from '@/components/AccountMenu.vue';
// Import Bootstrap modal functionality
import { Modal } from 'bootstrap';

export default {
  name: 'AccountView',
  components: {
    AccountMenu
  },
  setup() {
    const router = useRouter();
    const user = ref(null);
    const loading = ref(true);
    const updating = ref(false);
    const formData = reactive({
      name: '',
      email: '',
      address: ''
    });
    
    // Function to load user data
    const loadUserData = () => {
      loading.value = true;
      const userData = getUser();
      
      if (userData) {
        user.value = userData;
        
        // Initialize form data
        formData.name = userData.name || '';
        formData.email = userData.email || '';
        formData.address = userData.address || '';
      } else {
        // If no user data, redirect to login
        router.push('/login');
      }
      
      loading.value = false;
    };
    
    // Format date for display
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };
    
    // Edit profile
    const editProfile = () => {
      // Show modal - using imported Bootstrap
      const modalElement = document.getElementById('editProfileModal');
      const modal = new Modal(modalElement);
      modal.show();
    };
    
    // Update profile
    const updateProfile = () => {
      updating.value = true;
      
      fetch('http://localhost/COS30043-HD-Project/api/update_profile.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: user.value.id,
          name: formData.name,
          email: formData.email,
          address: formData.address
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Update local user data
          const updatedUser = { ...user.value, ...formData };
          localStorage.setItem('booknestUser', JSON.stringify(updatedUser));
          user.value = updatedUser;
          
          // Close modal using imported Bootstrap
          const modalElement = document.getElementById('editProfileModal');
          const modal = Modal.getInstance(modalElement);
          if (modal) modal.hide();
          
          alert('Profile updated successfully!');
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error updating profile:', error);
        alert('Failed to update profile. Please try again.');
      })
      .finally(() => {
        updating.value = false;
      });
    };
    
    // Logout function
    const logout = () => {
      authLogout();
      router.push('/login');
    };
    
    // Load user data when component mounts
    onMounted(loadUserData);
    
    return {
      user,
      loading,
      formData,
      updating,
      formatDate,
      editProfile,
      updateProfile,
      logout
    };
  }
}
</script>

<style scoped>
.card {
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card-header {
  font-weight: bold;
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
}

.btn-danger:hover {
  background-color: #c82333;
  border-color: #bd2130;
}
</style>
