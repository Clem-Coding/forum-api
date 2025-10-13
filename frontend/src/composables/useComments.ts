import { ref } from "vue";
import { commentsService } from "../api/commentService";
import type { Comment, CommentCreate } from "../types/comment";

export function useComments() {
  const comments = ref<Comment[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Create a new comment
  const createComment = async (data: CommentCreate) => {
    loading.value = true;
    error.value = null;
    try {
      const newComment = await commentsService.create(data);
      comments.value.push(newComment); // Add to the list
      return newComment;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la création du commentaire";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Get comments by topic
  const fetchCommentsByTopic = async (topicId: number) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await commentsService.getByTopic(topicId);
      comments.value = data;
      return data;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors du chargement des commentaires";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Update a comment
  const updateComment = async (id: number, data: Partial<CommentCreate>) => {
    loading.value = true;
    error.value = null;
    try {
      const updatedComment = await commentsService.update(id, data);

      const index = comments.value.findIndex((c) => c.id === id);
      if (index !== -1) {
        comments.value[index] = updatedComment;
      }

      return updatedComment;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la modification du commentaire";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Delete a comment
  const deleteComment = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      await commentsService.delete(id);
      comments.value = comments.value.filter((c) => c.id !== id);
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la suppression du commentaire";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  return {
    comments,
    loading,
    error,
    createComment,
    fetchCommentsByTopic,
    updateComment,
    deleteComment,
  };
}
