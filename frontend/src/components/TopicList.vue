<template>
  <ul class="topics-list">
    <li v-for="topic in topics" :key="topic.id" class="forum-topic">
      <div class="forum-avatar">
        <img
          :src="`${API_BASE_URL}${topic.user.avatarUrl}`"
          :alt="`Avatar de ${topic.user.username}`"
        />
      </div>
      <div class="forum-topic__body">
        <router-link :to="`/topic/${topic.id}`" class="topic-title">
          {{ topic.title }}
        </router-link>
        <div class="forum-topic__meta">
          {{ topic.user.username }} le
          {{ new Date(topic.createdAt).toLocaleDateString() }}
        </div>
      </div>
      <div class="forum-topic__stats">
        <PhChatsCircle :size="24" />
        <span class="comments-count">{{ topic.comments?.length || 0 }}</span>
      </div>
    </li>
  </ul>
  <nav class="pagination">
    <button
      @click="goToPage(currentPage - 1)"
      :disabled="currentPage === 1"
      class="nav-btn"
      aria-label="Page précédente"
    >
      <PhCaretDoubleLeft />
    </button>

    <button
      v-for="page in totalPages"
      :key="page"
      @click="goToPage(page)"
      :class="{ active: currentPage === page }"
    >
      {{ page }}
    </button>

    <button
      @click="goToPage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="nav-btn"
      aria-label="Page suivante"
    >
      <PhCaretDoubleRight />
    </button>
  </nav>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useTopics } from "@/composables/useTopics";
import { API_BASE_URL } from "@/api/client";
import { PhCaretDoubleLeft, PhCaretDoubleRight, PhChatsCircle } from "@phosphor-icons/vue";

const { topics, totalItems, fetchTopics } = useTopics();
const currentPage = ref(1);

const totalPages = computed(() => {
  if (!totalItems.value || totalItems.value === 0) return 1;
  return Math.ceil(totalItems.value / 10); // 10 = paginationItemsPerPage from the API
});

onMounted(() => {
  fetchTopics(1);
});

const goToPage = (page: number) => {
  currentPage.value = page;
  fetchTopics(page);
};
</script>

<style scoped>
.topics-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  list-style: none;
  padding: 0;
  margin: 0;
}

.forum-topic {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #ccc;
  padding: 1rem 0;
}

.topic-title {
  font-weight: 600;
}

.forum-avatar img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.forum-topic__body {
  flex: 1;
  margin-left: 1rem;
  margin-right: 1rem;
}

.forum-topic__stats {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--vt-c-light-grey);
  font-size: 0.9rem;
}

.comments-count {
  font-weight: 500;
}

.forum-topic__meta {
  color: var(--vt-c-light-grey);
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.pagination button {
  padding: 0.5em 0.75em;
  background: var(--vt-c-dark-midnight-muted);
  border-radius: 2px;
  transition: all 0.2s ease;
  width: fit-content;
}

.pagination button:hover:not(:disabled) {
  background: var(--vt-c-light-indigo);
}

.pagination button.active {
  background: var(--vt-c-light-indigo);
  color: white;
}

.pagination button.nav-btn {
  font-weight: 500;
}

.pagination button:disabled {
  transform: none;
  background-color: transparent;
}
</style>
