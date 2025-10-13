<script setup lang="ts">
import AppHeader from "./components/layout/AppHeader.vue";
</script>

<template>
  <div>
    <AppHeader />
    <main class="container">
      <router-view />
    </main>
  </div>
</template>
