import apiClient from "./client";
import type { LoginCredentials, LoginResponse } from "../types/auth";
import type { User } from "../types/user";

export const authService = {
  async login(credentials: LoginCredentials): Promise<{ token: string; user: User }> {
    const response = await apiClient.post<LoginResponse>("/api/login_check", credentials);
    const { token } = response.data;

    const userResponse = await apiClient.get<User>("/api/users/me", {
      headers: { Authorization: `Bearer ${token}` },
    });

    const user = userResponse.data;

    if (!user) throw new Error("Utilisateur non trouvé");

    localStorage.setItem("auth_token", token);
    localStorage.setItem("current_user", JSON.stringify(user));

    return { token, user };
  },

  logout(): void {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("current_user");
  },

  getCurrentUser(): User | null {
    const userJson = localStorage.getItem("current_user");
    return userJson ? JSON.parse(userJson) : null;
  },

  getToken(): string | null {
    return localStorage.getItem("auth_token");
  },

  isOwner(resourceUser: User): boolean {
    const currentUser = this.getCurrentUser();
    return currentUser ? currentUser.username === resourceUser.username : false;
  },

  isAdmin(resourceUser: User): boolean {
    const currentUser = this.getCurrentUser();
    return currentUser
      ? (currentUser.roles?.includes("ROLE_ADMIN") ?? false) ||
          currentUser.username === resourceUser.username
      : false;
  },
};
