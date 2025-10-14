import { ref } from "vue";
import type { User, LoginCredentials } from "@/types/user";
import { authService } from "@/api/authService";

export const isAuthenticated = ref(!!authService.getToken());
export const currentUser = ref<User | null>(authService.getCurrentUser());

export const useAuth = () => {
  const login = async (credentials: LoginCredentials) => {
    const { user } = await authService.login(credentials);
    currentUser.value = user;
    isAuthenticated.value = true;
  };

  const logout = () => {
    authService.logout();
    currentUser.value = null;
    isAuthenticated.value = false;
  };

  return { login, logout, isAuthenticated, currentUser };
};
