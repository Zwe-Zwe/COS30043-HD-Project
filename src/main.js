import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

// Import Bootstrap CSS and JS (including Popper.js for dropdowns, etc.)
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

createApp(App).use(router).mount("#app");
