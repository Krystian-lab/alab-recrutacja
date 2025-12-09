import { createRouter, createWebHistory } from "vue-router";
import LoginView from "../views/LoginView.vue";
import ResultsView from "../views/ResultsView.vue";

const routes = [
  {
    path: "/login",
    name: "login",
    component: LoginView,
  },
  {
    path: "/results",
    name: "results",
    component: ResultsView,
    meta: { requiresAuth: true },
  },
  {
    path: "/",
    redirect: "/login",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  if (to.meta.requiresAuth && !token) {
    return next({ name: "login" });
  }
  if (to.name === "login" && token) {
    return next({ name: "results" });
  }
  next();
});

export default router;
