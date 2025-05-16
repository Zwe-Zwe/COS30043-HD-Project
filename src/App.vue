<template>
  <div>
    <nav class="navbar navbar-expand navbar-light bg-light">
      <ul class="navbar-nav">
        <li class="nav-item"><router-link to="/" class="nav-link">Home</router-link></li>
        <li class="nav-item"><router-link to="/cart" class="nav-link">Cart</router-link></li>
        <li class="nav-item"><router-link to="/register" class="nav-link">Register</router-link></li>
        <li class="nav-item"><router-link to="/login" class="nav-link">Login</router-link></li>
        <li class="nav-item"><router-link to="/account" class="nav-link">Account</router-link></li>
        <li class="nav-item"><router-link to="/purchases" class="nav-link">Purchases</router-link></li>
      </ul>
    </nav>

    <router-view/>
  </div>
</template>


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
</style>
