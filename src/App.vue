<template>
  <div class="d-flex flex-column min-vh-100">
    <AppNavbar/>
    <main class="flex-shrink-0">
      <router-view/>
    </main>
    <AppFooter />
  </div>
</template>

<script>
import { onMounted } from 'vue';
import { isLoggedIn, logout, validateSession, getUser } from '@/utils/auth';
import AppNavbar from './components/AppNavbar.vue';
import AppFooter from './components/AppFooter.vue';

export default {
  components: {
    AppNavbar,
    AppFooter
  },

  computed: {
    userLoggedIn() {
      return isLoggedIn();
    }
  },
  methods: {
    handleLogout() {
      logout();
      this.$router.push('/login');
    }
  },
  setup() {
    // Create initializeDatabase function inside setup to avoid 'this' context issue
    async function initializeDatabase() {
      try {
        const response = await fetch('http://localhost/COS30043-HD-Project/api/init-db.php');
        const data = await response.json();
        
        if (data.status === 'success') {
          console.log('Database initialized successfully');
        } else {
          console.warn('Database initialization warning:', data.message);
        }
      } catch (error) {
        console.error('Database initialization error:', error);
        // Continue anyway, as this is a best-effort operation
      }
    }

    onMounted(async () => {
      // Initialize database when app loads
      await initializeDatabase();
      
      // Validate user session when app loads
      const isValid = validateSession();
      
      // If user data exists, validate with backend
      if (isValid) {
        const user = getUser();
        if (user && user.id) {
          // Validate session with backend
          fetch('http://localhost/COS30043-HD-Project/api/session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId: user.id })
          })
          .then(res => res.json())
          .then(data => {
            if (data.status !== 'success') {
              // If session is invalid on server, log out
              logout();
            }
          })
          .catch(err => {
            console.error('Session validation error:', err);
            // Optional: handle connection errors (may not want to log out if server is down)
          });
        }
      }
    });
  }
}
</script>


<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

nav {
  padding: 30px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}

/* Global Mobile-First Styles */
body {
  font-size: 16px;
  line-height: 1.5;
}

h1 {
  font-size: 1.75rem;
  margin-bottom: 1rem;
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 0.75rem;
}

h3 {
  font-size: 1.25rem;
}

.container {
  padding-left: 15px;
  padding-right: 15px;
}

.btn {
  padding: 0.375rem 0.75rem;
}

/* Tablet Landscape Styles */
@media (min-width: 768px) {
  h1 {
    font-size: 2rem;
    margin-bottom: 1.25rem;
  }
  
  h2 {
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  
  h3 {
    font-size: 1.5rem;
  }
  
  .container {
    padding-left: 20px;
    padding-right: 20px;
  }
}

/* Desktop Styles */
@media (min-width: 992px) {
  h1 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
  }
  
  h2 {
    font-size: 2rem;
    margin-bottom: 1.25rem;
  }
  
  h3 {
    font-size: 1.75rem;
  }
  
  .container {
    padding-left: 30px;
    padding-right: 30px;
  }
}

/* Add styles to ensure footer stays at bottom */
.min-vh-100 {
  min-height: 100vh;
}

main {
  flex: 1 0 auto;
}
</style>

