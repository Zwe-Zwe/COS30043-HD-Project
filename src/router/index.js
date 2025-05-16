import { createRouter, createWebHistory } from "vue-router";
import MainView from "../views/MainView.vue";
import UserLogin from "../views/UserLogin.vue";
import UserRegister from "../views/UserRegister.vue";
import CartView from "../views/CartView.vue";
import ProductDetails from "../views/ProductDetails.vue";
import PurchasesHistory from "../views/PurchasesHistory.vue";
import AccountView from "../views/AccountView.vue";

const routes = [
  { path: "/", name: "Home", component: MainView },
  { path: "/product/:id", name: "Product", component: ProductDetails },
  { path: "/cart", name: "Cart", component: CartView },
  { path: "/register", name: "Register", component: UserRegister },
  { path: "/login", name: "Login", component: UserLogin },
  { path: "/account", name: "Account", component: AccountView },
  { path: "/purchases", name: "Purchases", component: PurchasesHistory },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
