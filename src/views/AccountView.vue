<template>
  <div class="container py-4">
    <h2 class="mb-4">My Account</h2>

    <div class="row">
      <!-- Account Menu -->
      <div class="col-md-3 mb-4">
        <AccountMenu />
        
        <!-- Account Stats -->
        <div class="card mt-4" v-if="!loading && user">
          <div class="card-body">
            <h5 class="card-title">Account Stats</h5>
            <div class="stats-container">
              <div class="stat-item">
                <div class="stat-value">{{ orderStats.count || 0 }}</div>
                <div class="stat-label">Orders</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">${{ orderStats.spent.toFixed(2) }}</div>
                <div class="stat-label">Spent</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ userSince }}</div>
                <div class="stat-label">Member Since</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Account Content -->
      <div class="col-md-9">
        <div v-if="loading" class="text-center py-4">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        
        <div v-else>
          <!-- Profile Card -->
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
              <h4 class="mb-0">My Profile</h4>
              <button class="btn btn-primary btn-sm" @click="editProfile">
                <i class="bi bi-pencil-square me-1"></i> Edit
              </button>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Profile Avatar -->
                <div class="col-md-3 text-center mb-3 mb-md-0">
                  <div class="profile-avatar">
                    <span v-if="user && user.name">{{ getInitials(user.name) }}</span>
                  </div>
                </div>
                
                <!-- Profile Details -->
                <div class="col-md-9">
                  <div class="profile-details">
                    <table class="table table-borderless profile-table">
                      <tbody>
                        <tr>
                          <th><i class="bi bi-person me-2"></i>Name:</th>
                          <td>{{ user.name }}</td>
                        </tr>
                        <tr>
                          <th><i class="bi bi-envelope me-2"></i>Email:</th>
                          <td>{{ user.email }}</td>
                        </tr>
                        <tr>
                          <th><i class="bi bi-geo-alt me-2"></i>Address:</th>
                          <td>{{ user.address || 'Not specified' }}</td>
                        </tr>
                        <tr>
                          <th><i class="bi bi-calendar-date me-2"></i>Member Since:</th>
                          <td>{{ formatDate(user.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Recent Orders -->
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
              <h4 class="mb-0">Recent Orders</h4>
              <router-link to="/order-history" class="btn btn-outline-primary btn-sm">
                View All Orders
              </router-link>
            </div>
            <div class="card-body p-0">
              <div v-if="isLoadingOrders" class="text-center py-4">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading orders...</span>
                </div>
              </div>
              <div v-else-if="recentOrders.length === 0" class="p-4 text-center">
                <p class="mb-0">You haven't placed any orders yet.</p>
                <router-link to="/books" class="btn btn-sm btn-primary mt-2">Browse Books</router-link>
              </div>
              <table v-else class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in recentOrders" :key="order.id">
                    <td>#{{ order.id }}</td>
                    <td>{{ formatDateShort(order.order_date) }}</td>
                    <td>${{ parseFloat(order.total_amount).toFixed(2) }}</td>
                    <td>
                      <span class="badge" :class="getStatusBadgeClass(order.status)">
                        {{ formatStatus(order.status) }}
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-link p-0" @click="viewOrderDetails(order.id)">
                        View
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- Account Actions -->
          <div class="card">
            <div class="card-header bg-light">
              <h4 class="mb-0">Account Actions</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <div class="action-card" @click="$router.push('/cart')">
                    <div class="action-icon">
                      <i class="bi bi-cart"></i>
                    </div>
                    <div class="action-text">
                      My Cart
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="action-card" @click="$router.push('/order-history')">
                    <div class="action-icon">
                      <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="action-text">
                      Order History
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="action-card" @click="changePasswordModal">
                    <div class="action-icon">
                      <i class="bi bi-key"></i>
                    </div>
                    <div class="action-text">
                      Change Password
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="action-card action-danger" @click="logout">
                    <div class="action-icon">
                      <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <div class="action-text">
                      Log Out
                    </div>
                  </div>
                </div>
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
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updatePassword">
              <div class="mb-3">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="currentPassword" 
                  v-model="passwordData.currentPassword" 
                  required
                >
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="newPassword" 
                  v-model="passwordData.newPassword" 
                  required
                  minlength="6"
                >
                <div class="form-text">
                  Password must be at least 6 characters long.
                </div>
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="confirmPassword" 
                  v-model="passwordData.confirmPassword" 
                  required
                  :class="{ 'is-invalid': passwordError }"
                >
                <div v-if="passwordError" class="invalid-feedback">
                  {{ passwordError }}
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button 
              type="button" 
              class="btn btn-primary" 
              @click="updatePassword" 
              :disabled="passwordUpdating || passwordError || !passwordsComplete"
            >
              <span v-if="passwordUpdating" class="spinner-border spinner-border-sm me-1" role="status"></span>
              Change Password
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { getUser, logout as authLogout, isLoggedIn } from '@/utils/auth';
import AccountMenu from '@/components/AccountMenu.vue';
import { Modal } from 'bootstrap';
import OrderService from '@/services/OrderService';

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
    const recentOrders = ref([]);
    const isLoadingOrders = ref(true);
    const formData = reactive({
      name: '',
      email: '',
      address: ''
    });
    
    // Password change data
    const passwordData = reactive({
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    });
    const passwordUpdating = ref(false);
    const passwordError = ref('');
    
    // Computed property to check if password fields are complete
    const passwordsComplete = computed(() => {
      return passwordData.currentPassword && 
             passwordData.newPassword && 
             passwordData.confirmPassword && 
             passwordData.newPassword.length >= 6;
    });
    
    // Watch for changes in password confirmation
    watch(() => passwordData.confirmPassword, (newVal) => {
      if (newVal && passwordData.newPassword && newVal !== passwordData.newPassword) {
        passwordError.value = 'Passwords do not match';
      } else {
        passwordError.value = '';
      }
    });
    
    watch(() => passwordData.newPassword, (newVal) => {
      if (passwordData.confirmPassword && newVal !== passwordData.confirmPassword) {
        passwordError.value = 'Passwords do not match';
      } else {
        passwordError.value = '';
      }
    });
    
    // Calculate membership duration
    const userSince = computed(() => {
      if (!user.value?.created_at) return 'N/A';
      
      const createdDate = new Date(user.value.created_at);
      const now = new Date();
      const diffTime = Math.abs(now - createdDate);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays < 30) {
        return `${diffDays} days`;
      } else if (diffDays < 365) {
        const months = Math.floor(diffDays / 30);
        return `${months} month${months > 1 ? 's' : ''}`;
      } else {
        const years = Math.floor(diffDays / 365);
        return `${years} year${years > 1 ? 's' : ''}`;
      }
    });
    
    // Order statistics
    const orderStats = reactive({
      count: 0,
      spent: 0,
      lastOrder: null
    });
    
    // Function to load user data
    const loadUserData = () => {
      loading.value = true;
      
      // First check if user is logged in before trying to get user data
      if (!isLoggedIn()) {
        console.log("User not logged in, redirecting to login page");
        router.push({ 
          path: '/login', 
          query: { redirect: '/account' } 
        });
        return;
      }
      
      const userData = getUser();
      
      if (userData) {
        user.value = userData;
        
        // Initialize form data
        formData.name = userData.name || '';
        formData.email = userData.email || '';
        formData.address = userData.address || '';
        
        // Load orders after user data is loaded
        loadOrders();
      } else {
        // If no user data, redirect to login
        router.push({ 
          path: '/login', 
          query: { redirect: '/account' } 
        });
        return;
      }
      
      loading.value = false;
    };
    
    // Load user orders
    const loadOrders = async () => {
      isLoadingOrders.value = true;
      try {
        const orders = await OrderService.getUserOrders();
        
        // Sort by most recent first
        const sortedOrders = orders.sort((a, b) => {
          return new Date(b.order_date) - new Date(a.order_date);
        });
        
        // Take only the 5 most recent orders
        recentOrders.value = sortedOrders.slice(0, 5);
        
        // Calculate order statistics
        orderStats.count = orders.length;
        orderStats.spent = orders.reduce((total, order) => {
          return total + parseFloat(order.total_amount);
        }, 0);
        orderStats.lastOrder = orders.length > 0 ? orders[0] : null;
        
      } catch (error) {
        console.error('Error loading orders:', error);
      } finally {
        isLoadingOrders.value = false;
      }
    };
    
    // Format date for display
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };
    
    // Format date short version
    const formatDateShort = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };
    
    // Get user initials for avatar
    const getInitials = (name) => {
      if (!name) return '?';
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
    };
    
    // Format status display
    const formatStatus = (status) => {
      if (!status) return 'Unknown';
      return status.charAt(0).toUpperCase() + status.slice(1);
    };
    
    // Get badge class based on status
    const getStatusBadgeClass = (status) => {
      switch (status?.toLowerCase()) {
        case 'processing': return 'bg-warning text-dark';
        case 'shipped': return 'bg-info text-dark';
        case 'delivered': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
      }
    };
    
    // Edit profile
    const editProfile = () => {
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
          
          // Close modal
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
    
    // Open change password modal
    const changePasswordModal = () => {
      // Reset password form
      passwordData.currentPassword = '';
      passwordData.newPassword = '';
      passwordData.confirmPassword = '';
      passwordError.value = '';
      
      const modalElement = document.getElementById('changePasswordModal');
      const modal = new Modal(modalElement);
      modal.show();
    };
    
    // Update password
    const updatePassword = () => {
      // Validate passwords match
      if (passwordData.newPassword !== passwordData.confirmPassword) {
        passwordError.value = 'Passwords do not match';
        return;
      }
      
      // Validate password length
      if (passwordData.newPassword.length < 6) {
        passwordError.value = 'Password must be at least 6 characters';
        return;
      }
      
      passwordUpdating.value = true;
      
      fetch('http://localhost/COS30043-HD-Project/api/change_password.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          userId: user.value.id,
          currentPassword: passwordData.currentPassword,
          newPassword: passwordData.newPassword
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Close modal
          const modalElement = document.getElementById('changePasswordModal');
          const modal = Modal.getInstance(modalElement);
          if (modal) modal.hide();
          
          alert('Password changed successfully!');
        } else {
          passwordError.value = data.message || 'Failed to change password';
        }
      })
      .catch(error => {
        console.error('Error changing password:', error);
        passwordError.value = 'An error occurred. Please try again.';
      })
      .finally(() => {
        passwordUpdating.value = false;
      });
    };
    
    // View order details
    const viewOrderDetails = (orderId) => {
      router.push({
        path: '/order-confirmation',
        query: { orderId: orderId }
      });
    };
    
    // Logout function
    const logout = () => {
      if (confirm('Are you sure you want to log out?')) {
        authLogout();
        router.push('/login');
      }
    };
    
    // Load user data when component mounts
    onMounted(loadUserData);
    
    return {
      // User data
      user,
      loading,
      formData,
      updating,
      
      // Order data
      recentOrders,
      isLoadingOrders,
      
      // Computed values
      userSince,
      orderStats,
      passwordsComplete,
      
      // Password change
      passwordData,
      passwordUpdating,
      passwordError,
      
      // Methods - Format helpers
      formatDate,
      formatDateShort,
      getInitials,
      formatStatus,
      getStatusBadgeClass,
      
      // Methods - UI actions
      editProfile,
      updateProfile,
      changePasswordModal,
      updatePassword,
      viewOrderDetails,
      logout
    };
  }
}
</script>

<style scoped>
.card {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border: none;
  border-radius: 8px;
  margin-bottom: 20px;
}

.card-header {
  border-bottom: 1px solid #eaeaea;
  padding: 15px 20px;
  font-weight: 600;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background-color: #4e73df;
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0 auto;
}

.profile-details {
  padding: 10px 0;
}

.profile-table th {
  width: 35%;
  color: #6c757d;
}

.stats-container {
  display: flex;
  justify-content: space-between;
  text-align: center;
}

.stat-item {
  flex: 1;
  padding: 5px;
}

.stat-value {
  font-size: 1.4rem;
  font-weight: bold;
  color: #4e73df;
}

.stat-label {
  font-size: 0.8rem;
  color: #858796;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px 10px;
  background-color: #f8f9fc;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.action-card:hover {
  background-color: #eaecf4;
  transform: translateY(-3px);
}

.action-danger {
  background-color: #ffefef;
}

.action-danger:hover {
  background-color: #ffe0e0;
}

.action-icon {
  font-size: 1.8rem;
  margin-bottom: 10px;
  color: #4e73df;
}

.action-danger .action-icon {
  color: #e74a3b;
}

.action-text {
  font-weight: 600;
  font-size: 0.9rem;
}

/* Password requirements styling */
.password-requirements {
  font-size: 0.8rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.password-requirement {
  display: flex;
  align-items: center;
  margin-bottom: 0.25rem;
}

.password-requirement i {
  margin-right: 0.5rem;
}

.requirement-met {
  color: #198754;
}

.requirement-not-met {
  color: #6c757d;
}

@media (max-width: 767.98px) {
  .profile-avatar {
    width: 80px;
    height: 80px;
    font-size: 1.8rem;
    margin-bottom: 15px;
  }
}
</style>
