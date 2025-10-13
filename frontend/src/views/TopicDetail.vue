<template>
  <div class="card-container">
    <div class="card topic-detail">
      <h2 class="sr-only">Détails du topic</h2>
      <article v-if="topic" class="topic-content">
        <header class="topic-header">
          <div class="topic-info">
            <div class="topic-header-top">
              <h3>{{ topic.title }}</h3>
              <div v-if="canEditOrDelete" class="topic-actions">
                <button @click="editTopic" class="btn-secondary">Modifier</button>
                <button @click="handleDeleteTopic" class="btn-danger">Supprimer</button>
              </div>
            </div>
            <div class="topic-meta">
              <div class="author-info">
                <img
                  :src="`${API_BASE_URL}${topic.user.avatarUrl}`"
                  :alt="`Avatar de ${topic.user.username}`"
                  class="author-avatar"
                />
                <span class="author-name">{{ topic.user.username }}</span>
              </div>
              <time class="topic-date">
                {{ formatDate(topic.createdAt) }}
              </time>
            </div>
          </div>
        </header>
        <section class="topic-body">
          <div class="topic-content-text">
            {{ topic.content }}
          </div>
        </section>
        <section class="comments-section">
          <h3>
            {{
              topic.comments && topic.comments.length > 0
                ? topic.comments.length + " réponse" + (topic.comments.length > 1 ? "s" : "")
                : "Aucune réponse"
            }}
          </h3>
          <ul v-if="topic.comments?.length" class="comments-list">
            <li v-for="comment in topic.comments" :key="comment.id" class="comment">
              <div class="comment-author">
                <img
                  :src="`${API_BASE_URL}${comment.user.avatarUrl}`"
                  :alt="`Avatar de ${comment.user.username}`"
                  class="comment-avatar"
                />
                <div class="comment-meta">
                  <span class="comment-author-name">{{ comment.user.username }}</span>
                  <time class="comment-date">{{ formatDate(comment.createdAt) }}</time>
                </div>
              </div>
              <div class="comment-content">
                {{ comment.content }}
              </div>
            </li>
          </ul>
          <div v-else class="no-comments">
            <p>Soyez le premier à commenter !</p>
          </div>
          <form class="comment-form">
            <label for="comment">Ajouter un commentaire</label>
            <textarea id="comment" rows="4"></textarea>
            <button type="submit" class="btn-primary">Répondre</button>
          </form>
        </section>
      </article>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { API_BASE_URL } from "../api/client";
import { authService } from "../api/authService";
import type { Topic } from "../types/topic";
import { useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";

const router = useRouter();
const route = useRoute();

// State
const topic = ref<Topic | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Determine if the current is owner or admin
const canEditOrDelete = computed(() => {
  if (!topic.value) return false;
  const resourceUser = topic.value.user;
  return authService.isOwner(resourceUser) || authService.isAdmin(resourceUser);
});

// Get topic details from API
const { fetchTopicById } = useTopics();
const fetchTopicDetail = async (id: string) => {
  try {
    loading.value = true;
    error.value = null;
    const data = await fetchTopicById(id);
    topic.value = data;
  } catch (err) {
    error.value =
      "Une erreur est survenue lors du chargement du topic : " +
      (err instanceof Error ? err.message : "Erreur inconnue");
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

//redirection to edit page
const editTopic = () => {
  router.push(`/topic/${topic.value?.id}/edit`);
};

//Delete a topic
const { deleteTopic } = useTopics();

const handleDeleteTopic = async () => {
  if (!topic.value) return;

  if (confirm("Êtes-vous sûr de vouloir supprimer ce topic ?")) {
    try {
      await deleteTopic(topic.value.id);
      router.push("/");
    } catch (err) {
      console.error("Erreur lors de la suppression:", err);
    }
  }
};

// Lifecycle
onMounted(() => {
  const topicId = route.params.id as string;
  if (topicId) {
    fetchTopicDetail(topicId);
  }
});
</script>

<style scoped>
.topic-detail {
  width: 100%;
  padding: 2rem;
  color: var(--vt-c-black);
}

.topic-header-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.topic-header-top h3 {
  font-weight: 600;
}

.topic-actions {
  display: flex;
  gap: 0.75rem;
  flex-shrink: 0;
}

.topic-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--vt-c-grey-velvet);
}

.author-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.author-avatar {
  width: 50px;
  height: 50px;
}

.author-name {
  font-weight: 500;
}

/* Topic Body */
.topic-body {
  padding: 2rem;
}

.topic-content-text {
  font-size: 1.1rem;
  line-height: 1.7;
}

/* Comments Section */
.comments-section {
  padding: 2rem;
  border-top: 1px solid #eee;
  background: #f8f9fa;
}

.comments-section h2 {
  margin-bottom: 1.5rem;
  color: #333;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.comment {
  background: var(--vt-c-white);
  padding: 1.5em;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.comment-author {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.comment-meta {
  display: flex;
  flex-direction: column;
}

.comment-author-name {
  font-weight: 500;
  color: var(--vt-c-black);
}

.comment-date {
  font-size: 0.875rem;
  color: var(--vt-c-grey-velvet);
}

.comment-content {
  /* color: #444; */
  line-height: 1.6;
}

.no-comments {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.no-comments p:first-child {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.comment-form {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5em;
}

.comment-form button {
  align-self: flex-start;
  width: auto;
}

/* Responsive */
@media (max-width: 768px) {
  .topic-detail {
    padding: 1rem;
  }

  .topic-header,
  .topic-body,
  .comments-section {
    padding: 1.5rem;
  }

  .topic-info h1 {
    font-size: 1.5rem;
  }

  .topic-header-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .topic-actions {
    width: 100%;
    justify-content: flex-end;
  }

  .topic-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style>
