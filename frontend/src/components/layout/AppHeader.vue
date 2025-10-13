<template>
  <header class="header container">
    <h1 @click="router.push('/')" title="Retour à l'accueil" aria-label="Retour à l'accueil">
      Mon Forum
    </h1>

    <div class="auth-section">
      <button v-if="!isAuthenticated" @click="goToLogin" class="btn-login">Se connecter</button>
      <div v-else class="user-info">
        <div class="user-container">
          <img
            v-if="currentUser && currentUser.avatarUrl"
            :src="`http://localhost:8000${currentUser.avatarUrl}`"
            :alt="`Avatar de ${currentUser.username}`"
            class="avatar"
          />
          <strong v-if="currentUser">{{ currentUser.username }}</strong>
        </div>
        <button
          @click="handleLogout"
          class="btn-logout"
          title="Se déconnecter"
          aria-label="Se déconnecter"
        >
          <PhSignOut />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { useAuth } from "../../composables/useAuth";
import { PhSignOut } from "@phosphor-icons/vue";

const router = useRouter();
const { isAuthenticated, currentUser, logout } = useAuth();

const goToLogin = () => {
  router.push("/login");
};

const handleLogout = () => {
  logout();
  router.push("/");
};
</script>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

h1 {
  cursor: pointer;
}

.auth-section {
  display: flex;
  align-items: center;
  gap: 1em;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.75em;
}

.user-container {
  display: flex;
  align-items: center;
  gap: 1em;
}

.avatar {
  width: 40px;
  height: 40px;
}

.btn-login,
.btn-logout {
  background: none;
  color: var(--vt-c-primary);
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: color 0.2s;
}

.btn-login:hover,
.btn-logout:hover {
  color: var(--vt-c-lime);
}

@media (min-width: 1024px) {
  .user-info {
    flex-direction: row;
    align-items: center;
  }
}
</style>
