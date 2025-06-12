import { updateAuthState, loginEvent } from "./eventBus";

export function isLoggedIn() {
  return localStorage.getItem("booknestUser") !== null;
}

export function getUser() {
  const data = localStorage.getItem("booknestUser");
  return data ? JSON.parse(data) : null;
}

export function login(userData) {
  // Store user data in localStorage for persistent sessions
  localStorage.setItem("booknestUser", JSON.stringify(userData));

  // Update auth state for reactivity
  updateAuthState(true);
  loginEvent.emit(true);

  // Optional: Set session start time
  localStorage.setItem("sessionStart", Date.now().toString());
}

export function logout() {
  localStorage.removeItem("booknestUser");
  localStorage.removeItem("sessionStart");
  updateAuthState(false);
  loginEvent.emit(false);
}

export function getSessionDuration() {
  const start = localStorage.getItem("sessionStart");
  return start ? Math.floor((Date.now() - parseInt(start)) / 1000) : 0;
}

// Check if session is valid (e.g., not expired)
export function validateSession() {
  const user = getUser();
  if (!user) return false;

  // Add any additional session validation logic here
  // For example, check if session is older than 24 hours
  // const sessionAge = getSessionDuration();
  // if (sessionAge > 86400) {
  //   logout();
  //   return false;
  // }

  return true;
}

/**
 * Get the JWT token from localStorage
 * @returns {string|null} The JWT token or null if not found
 */
export function getToken() {
  return localStorage.getItem("token");
}

/**
 * Get the authentication token from localStorage
 * @returns {string} The authentication token or empty string if not found
 */
export function getAuthToken() {
  return localStorage.getItem("token") || "";
}
