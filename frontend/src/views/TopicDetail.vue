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

              <div v-if="editingCommentId !== comment.id" class="comment-content">
                {{ comment.content }}
              </div>

              <div v-else class="comment-edit">
                <textarea
                  v-model="editingCommentContent"
                  rows="3"
                  class="edit-textarea"
                  @keydown.esc="cancelEdit"
                ></textarea>
                <div class="edit-actions">
                  <button
                    @click="saveEdit(comment)"
                    class="btn-primary btn-sm"
                    :disabled="commentLoading"
                  >
                    {{ commentLoading ? "Sauvegarde..." : "Sauvegarder" }}
                  </button>
                  <button @click="cancelEdit" class="btn-secondary btn-sm">Annuler</button>
                </div>
              </div>
            </li>
          </ul>
          <div v-else class="no-comments">
            <p>Soyez le premier à commenter !</p>
          </div>
          <div v-if="isAuthenticated">
            <form @submit.prevent="submitComment" class="comment-form">
              <label for="comment">Ajouter un commentaire</label>
              <textarea
                id="comment"
                v-model="newCommentContent"
                rows="4"
                placeholder="Écrivez votre commentaire..."
                required
              ></textarea>
              <div v-if="commentError" class="error-message">{{ commentError }}</div>
              <button type="submit" class="btn-primary" :disabled="commentLoading">
                {{ commentLoading ? "Envoi..." : "Répondre" }}
              </button>
            </form>
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
import { API_BASE_URL } from "../api/client";
import { authService } from "../api/authService";
import { useAuth } from "../composables/useAuth";
import { useComments } from "../composables/useComments";
import type { Topic } from "../types/topic";
import type { Comment } from "../types/comment";
import { useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";
import { PhTrash, PhPencil } from "@phosphor-icons/vue";

const router = useRouter();
const route = useRoute();
const { isAuthenticated, currentUser } = useAuth();
const {
  createComment,
  updateComment,
  deleteComment: removeComment,
  loading: commentLoading,
} = useComments();

// State
const topic = ref<Topic | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);
const newCommentContent = ref("");
const commentError = ref<string | null>(null);

// Edit state
const editingCommentId = ref<number | null>(null);
const editingCommentContent = ref("");

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
const canEditOrDeleteComment = (c: Comment) => {
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

//redirection to edit page
const editTopic = () => {
  router.push(`/topic/${topic.value?.id}/edit`);
};

/**
 * Comment management
 */
const editComment = (comment: Comment) => {
  editingCommentId.value = comment.id!;
  editingCommentContent.value = comment.content;
};

const cancelEdit = () => {
  editingCommentId.value = null;
  editingCommentContent.value = "";
};

const saveEdit = async (comment: Comment) => {
  if (!editingCommentContent.value.trim() || !comment.id) return;

  try {
    const updateData = {
      content: editingCommentContent.value.trim(),
    };

    await updateComment(comment.id, updateData);
    await fetchTopicDetail(topic.value!.id.toString());

    // Reset edit state
    editingCommentId.value = null;
    editingCommentContent.value = "";
  } catch (err) {
    console.error("Error updating comment:", err);
    alert("Erreur lors de la modification du commentaire");
  }
};

const deleteComment = async (comment: Comment) => {
  console.log("lecommentid", comment.id);
  if (!comment.id) {
    alert("Erreur: ID du commentaire manquant");
    return;
  }

  if (confirm("Êtes-vous sûr de vouloir supprimer ce commentaire ?")) {
    try {
      await removeComment(comment.id);
      await fetchTopicDetail(topic.value!.id.toString());
    } catch (err) {
      console.error("Error deleting comment:", err);
      alert("Erreur lors de la suppression du commentaire");
    }
  }
};

const submitComment = async () => {
  if (!newCommentContent.value.trim() || !topic.value) return;
  commentError.value = null;

  try {
    const commentData = {
      content: newCommentContent.value.trim(),
      topic: `/api/topics/${topic.value.id}`,
    };

    await createComment(commentData);
    await fetchTopicDetail(topic.value.id.toString());
    console.log("Commentaires après fetch :", topic.value.comments);

    newCommentContent.value = "";
  } catch (err) {
    commentError.value =
      err instanceof Error ? err.message : "Erreur lors de l'ajout du commentaire";
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

/* Topic Body */
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

.comment-form {
  margin-top: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1em;
}

.comment-form button {
  align-self: flex-start;
  width: auto;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.login-msg {
  color: var(--vt-c-grey-velvet);
  font-style: italic;
  text-align: center;
  margin-top: 1rem;
}

/* Comment layout improvements */
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
  transition: all 0.2s ease;
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

@media (max-width: 768px) {
  .comment-actions {
    opacity: 1;
  }

  .comment-header {
    align-items: center;
  }
}

/* Tablet styles */
@media (min-width: 769px) {
  .topic-detail {
    padding: 1.5rem;
  }

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
    justify-content: space-between;
    align-items: center;
  }

  .topic-body {
    padding: 1.75rem;
  }

  .topic-content-text {
    font-size: 1.05rem;
    line-height: 1.65;
  }

  .comments-section {
    padding: 1.75rem;
  }

  .comments-section h3 {
    font-size: 1.3rem;
    padding: 0.75em 0;
  }

  .comments-list {
    gap: 1.25rem;
    background-color: transparent;
    padding-left: 4em;
  }

  .comment {
    padding: 1.25em;
  }

  .no-comments {
    padding: 1.75rem;
  }

  .comment-form {
    margin-top: 1.75rem;
    padding: 1.25em;
  }
}

/* Desktop styles */
@media (min-width: 1024px) {
  .topic-detail {
    padding: 2rem;
  }

  .topic-body {
    padding: 2rem;
  }

  .topic-content-text {
    font-size: 1.1rem;
    line-height: 1.7;
  }

  .comments-section {
    padding: 2rem;
  }

  .comments-section h3 {
    font-size: 1.4rem;
    padding: 1em 0;
    margin-top: 1em;
  }

  .comments-list {
    gap: 1.5rem;
  }

  .comment {
    padding: 1.5em;
  }

  .no-comments {
    padding: 2rem;
    margin-top: 2em;
  }

  .no-comments p:first-child {
    font-size: 1.1rem;
  }

  .comment-form {
    margin-top: 2rem;
    padding: 1.5em;
  }
}

.comment-edit {
  margin-top: 0.75rem;
}

.edit-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--vt-c-divider-light-2);
  border-radius: 6px;
  resize: vertical;
  font-family: inherit;
  font-size: 0.95rem;
  line-height: 1.5;
  background: var(--vt-c-white);
  color: var(--vt-c-black);
  margin-bottom: 0.75rem;
}

.edit-textarea:focus {
  outline: none;
  border-color: var(--vt-c-brand);
  box-shadow: 0 0 0 2px var(--vt-c-brand-light);
}

.edit-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-start;
  align-items: center;
}

.btn-sm {
  padding: 0.5em 0.75em;
  font-size: 0.875em;
  line-height: 1.25;
}

.comment-edit {
  animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (min-width: 769px) {
  .edit-textarea {
    font-size: 1rem;
    padding: 1rem;
  }

  .edit-actions {
    gap: 0.75rem;
  }
}
</style>
