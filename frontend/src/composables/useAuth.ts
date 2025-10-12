import { ref, computed } from "vue";
import { authService } from "../api/auth";
import type { User } from "../types/user";
import type { LoginCredentials } from "../types/auth";

const currentUser = ref<User | null>(authService.getCurrentUser());
const isAuthenticated = computed(() => authService.isAuthenticated());

export function useAuth() {
  const loading = ref(false);
  const error = ref<string | null>(null);

  const login = async (credentials: LoginCredentials) => {
    loading.value = true;
    error.value = null;
    try {
      const result = await authService.login(credentials);
      currentUser.value = result.user;
      return result;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur de connexion";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const logout = () => {
    authService.logout();
    currentUser.value = null;
  };

  const isOwner = (resourceUser: User): boolean => {
    return authService.isOwner(resourceUser);
  };

  return {
    currentUser,
    isAuthenticated,
    loading,
    error,
    login,
    logout,
    isOwner,
  };
}
