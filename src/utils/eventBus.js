import { ref } from "vue";

// Create a reactive reference for authentication state
export const authState = ref(false);

// Update auth state method
export function updateAuthState(isLoggedIn) {
  authState.value = isLoggedIn;
}

// Custom event emitter for login state changes
export const loginEvent = {
  listeners: [],
  subscribe(callback) {
    this.listeners.push(callback);
    return () => {
      this.listeners = this.listeners.filter(
        (listener) => listener !== callback
      );
    };
  },
  emit(isLoggedIn) {
    this.listeners.forEach((callback) => callback(isLoggedIn));
  },
};

// Initialize state on page load
updateAuthState(localStorage.getItem("booknestUser") !== null);
