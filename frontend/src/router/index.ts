import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import LoginView from "../views/LoginView.vue";
import TopicDetail from "../views/TopicDetail.vue";
import CreateView from "@/views/CreateView.vue";
import EditView from "@/views/EditView.vue";

const routes = [
  { path: "/", name: "home", component: HomeView },
  { path: "/login", name: "login", component: LoginView },
  { path: "/topic/:id", name: "topic-detail", component: TopicDetail },
  { path: "/topics/create", name: "topic-create", component: CreateView },
  { path: "/topic/:id/edit", name: "topic-edit", component: EditView },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});
