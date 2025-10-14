<template>
  <div v-if="!loading && topic" class="card-container">
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
              <div class="comment-header">
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

                <div
                  v-if="canEditOrDeleteComment(comment) && editingCommentId !== comment.id"
                  class="comment-actions"
                >
                  <button @click="editComment(comment)" class="btn-icon" title="Modifier">
                    <PhPencil />
                  </button>
                  <button
                    @click="deleteComment(comment)"
                    class="btn-icon btn-danger"
                    title="Supprimer"
                  >
                    <PhTrash />
                  </button>
                </div>
              </div>

              <EditCommentForm
                v-if="editingCommentId === comment.id"
                :comment="comment"
                @update="handleCommentUpdated"
                @cancel="cancelEdit"
              />
              <div v-else class="comment-content">{{ comment.content }}</div>
            </li>
          </ul>
          <div v-else class="no-comments">
            <p>Soyez le premier à commenter !</p>
          </div>

          <div v-if="isAuthenticated && topic">
            <CreateCommentForm :topic-id="topic.id" @comment-added="handleCommentAdded" />
          </div>

          <p class="login-msg" v-else>Connectez-vous pour poster un commentaire</p>
        </section>
      </article>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { API_BASE_URL } from "@/api/client";
import { authService } from "@/api/authService";
import { useAuth } from "@/composables/useAuth";
import { useComments } from "@/composables/useComments";
import type { Topic } from "@/types/topic";
// Use an alias for the forum Comment type to avoid conflicts with the built-in DOM 'Comment' type
import type { Comment as ForumComment } from "@/types/comment";
import { useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";
import { PhTrash, PhPencil } from "@phosphor-icons/vue";
import CreateCommentForm from "@/components/forms/CreateCommentForm.vue";
import EditCommentForm from "@/components/forms/EditCommentForm.vue";

const router = useRouter();
const route = useRoute();
const { isAuthenticated, currentUser } = useAuth();
const { deleteComment: removeComment } = useComments();

// State
const topic = ref<Topic | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);
const editingCommentId = ref<number | null>(null);

// Format date in French
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

/**
 * Authorization
 */

// Can edit/delete a topic (owner or admin)
const canEditOrDelete = computed(() => {
  if (!topic.value || !isAuthenticated.value || !currentUser.value) return false;
  const resourceUser = topic.value.user;
  return authService.isOwner(resourceUser) || authService.isAdmin(resourceUser);
});

// Can edit/delete a comment (owner or admin)
const canEditOrDeleteComment = (c: ForumComment) => {
  if (!isAuthenticated.value || !currentUser.value) return false;
  return authService.isOwner(c.user) || authService.isAdmin(c.user);
};

/**
 * Topic management
 */

const { fetchTopicById } = useTopics();

const fetchTopicDetail = async (id: string) => {
  try {
    loading.value = true;
    error.value = null;
    const data = await fetchTopicById(Number(id));
    topic.value = data;
  } catch (err) {
    error.value =
      "Une erreur est survenue lors du chargement du topic : " +
      (err instanceof Error ? err.message : "Erreur inconnue");
  } finally {
    loading.value = false;
  }
};

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

// Redirection to edit page
const editTopic = () => {
  router.push(`/topic/${topic.value?.id}/edit`);
};

/**
 * Comment management
 */

const handleCommentAdded = (newComment: ForumComment) => {
  if (!topic.value) return;
  if (!topic.value.comments) topic.value.comments = [];
  topic.value.comments.push(newComment);
};

const handleCommentUpdated = (updatedComment: ForumComment) => {
  if (!topic.value?.comments) return;
  const index = topic.value.comments.findIndex((c) => c.id === updatedComment.id);
  if (index !== -1) {
    topic.value.comments[index] = updatedComment;
  }
  editingCommentId.value = null;
};

const editComment = (comment: ForumComment) => {
  editingCommentId.value = comment.id!;
};

const cancelEdit = () => {
  editingCommentId.value = null;
};

const deleteComment = async (comment: ForumComment) => {
  if (!comment.id) return;

  if (confirm("Êtes-vous sûr de vouloir supprimer ce commentaire ?")) {
    try {
      await removeComment(comment.id);
      if (!topic.value?.comments) return;
      topic.value.comments = topic.value.comments.filter((c) => c.id !== comment.id);
    } catch (err) {
      console.error("Erreur lors de la suppression du commentaire:", err);
      alert("Erreur lors de la suppression du commentaire");
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
/* Topic Styles */

.topic-detail {
  width: 100%;
  padding: 1rem;
  color: var(--vt-c-black);
}

.topic-header-top {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}

.topic-header-top h3 {
  font-weight: 600;
}

.topic-actions {
  display: flex;
  gap: 0.75rem;
  flex-shrink: 0;
  width: 100%;
  justify-content: flex-end;
}

.topic-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5rem;
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

.topic-body {
  padding: 1.5rem;
}

.topic-content-text {
  font-size: 1rem;
  line-height: 1.6;
}

/* Comments Section */

.comments-section {
  border-top: 1px solid #eee;
  background: #f8f9fa;
  padding: 1.5rem;
}

.comments-section h3 {
  margin-top: 0;
  color: var(--vt-c-black);
  padding: 0.5em 0;
  font-size: 1.2rem;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 0;
}

.comment {
  background: var(--vt-c-white);
  padding: 1em;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.comment-author {
  display: flex;
  align-items: center;
  gap: 0.75rem;
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
  line-height: 1.5;
}

.no-comments {
  text-align: center;
  padding: 1.5rem;
  color: var(--vt-c-grey-velvet);
  margin-top: 1em;
}

.no-comments p:first-child {
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.login-msg {
  color: var(--vt-c-grey-velvet);
  font-style: italic;
  text-align: center;
  margin-top: 1rem;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.comment-actions {
  display: flex;
  gap: 0.5rem;
  opacity: 1;
  transition: none;
}

/* Buttons styles */

.btn-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  padding: 0;
  border: none;
  border-radius: 8px;
  background: transparent;
  color: var(--vt-c-grey-velvet);
}

.btn-icon:hover {
  background-color: var(--vt-c-indigo-light);
  color: var(--vt-c-black);
}

.btn-icon:not(.btn-danger):hover {
  background: #e6eaff;
  color: var(--vt-c-light-indigo);
}

.btn-icon.btn-danger:hover {
  background: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.btn-icon svg {
  width: 22px;
  height: 22px;
}

/* Media Queries Desktop */

@media (min-width: 769px) {
  .topic-header-top {
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
  }
  .topic-actions {
    width: auto;
    justify-content: flex-end;
  }

  .topic-meta {
    flex-direction: row;
    gap: 1.5rem;
    justify-content: space-between;
    align-items: center;
  }

  .comments-list {
    gap: 1.5rem;
    padding-left: 4rem;
  }
}
</style>
