import { createRouter, createWebHistory } from "vue-router";
import MainView from "../views/MainView.vue";
import UserLogin from "../views/UserLogin.vue";
import UserRegister from "../views/UserRegister.vue";
import CartView from "../views/CartView.vue";
import CheckoutView from "../views/CheckoutView.vue";
import OrderConfirmationView from "../views/OrderConfirmationView.vue";
import OrderHistoryView from "../views/OrderHistoryView.vue";
import ProductDetails from "../views/ProductDetails.vue";
import AccountView from "../views/AccountView.vue";
import Books from "../views/BooksView.vue";
import ContactView from "../views/ContactView.vue";
import ShippingView from "../views/ShippingView.vue";
import FaqView from "../views/FaqView.vue";
import PrivacyView from "../views/PrivacyView.vue";
import TermsView from "../views/TermsView.vue";
import AccessibilityView from "../views/AccessibilityView.vue";
import { isLoggedIn } from "@/utils/auth";

const routes = [
  { path: "/", name: "Home", component: MainView },
  { path: "/product/:id", name: "Product", component: ProductDetails },
  { path: "/cart", name: "Cart", component: CartView },
  { path: "/checkout", name: "Checkout", component: CheckoutView },
  {
    path: "/order-confirmation",
    name: "OrderConfirmation",
    component: OrderConfirmationView,
  },
  {
    path: "/order-history",
    name: "OrderHistory",
    component: OrderHistoryView,
  },
  { path: "/register", name: "Register", component: UserRegister },
  { path: "/login", name: "Login", component: UserLogin },
  { path: "/account", name: "Account", component: AccountView },
  { path: "/books", name: "Books", component: Books },
  { path: "/contact", name: "Contact", component: ContactView },
  { path: "/shipping", name: "Shipping", component: ShippingView },
  { path: "/faq", name: "FAQ", component: FaqView },
  { path: "/privacy", name: "Privacy", component: PrivacyView },
  { path: "/terms", name: "Terms", component: TermsView },
  {
    path: "/accessibility",
    name: "Accessibility",
    component: AccessibilityView,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Add navigation guards to protected routes
router.beforeEach((to, from, next) => {
  // Define which routes require authentication
  const requiresAuth = [
    "Account",
    "OrderHistory",
    "Checkout",
    "OrderConfirmation",
    "purchases",
  ].includes(to.name);

  if (requiresAuth && !isLoggedIn()) {
    // Redirect to login if trying to access protected route while not logged in
    next({ name: "Login", query: { redirect: to.fullPath } });
  } else {
    // Continue as normal
    next();
  }
});

export default router;
