import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import LoginView from "../views/LoginView.vue";
import TopicDetail from "../views/TopicDetail.vue";

const routes = [
  { path: "/", name: "home", component: HomeView },
  { path: "/login", name: "login", component: LoginView },
  { path: "/topic/:id", name: "topic-detail", component: TopicDetail },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});
