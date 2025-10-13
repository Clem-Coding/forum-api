<template>
  <section>
    <div class="topics-header">
      <h2>Les derniers topics</h2>
      <button v-if="isAuthenticated" @click="$router.push('/topics/create')" class="btn-create">
        <PhPencilSimple />Créer
      </button>
    </div>
    <TopicList />
  </section>
</template>

<script setup lang="ts">
import { PhPencilSimple } from "@phosphor-icons/vue";
import TopicList from "../components/TopicList.vue";
import { isAuthenticated } from "@/composables/useAuth";
</script>

<style scoped>
.topics-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2em;
}
.btn-create {
  background-color: var(--vt-c-lime);
  color: var(--vt-c-dark-midnight);
  border-radius: 2px;
  padding: 0.5em 1em;
  display: flex;
  align-items: center;
  width: fit-content;
}

.btn-create svg {
  margin-right: 0.25em;
}

.btn-create:hover {
  background-color: var(--vt-c-lime-hover);
}

@media (min-width: 600px) {
  .topics-header {
    flex-direction: row;
    justify-content: space-between;
  }
}
</style>
