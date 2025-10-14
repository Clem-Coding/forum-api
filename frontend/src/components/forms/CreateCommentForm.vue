<template>
  <form @submit.prevent="submitComment" class="comment-form">
    <label for="comment">Ajouter un commentaire</label>
    <textarea
      id="comment"
      v-model="newCommentContent"
      rows="4"
      placeholder="Écrivez votre commentaire..."
      required
    ></textarea>
    <p v-if="errors.content" class="error-msg">{{ errors.content[0] }}</p>
    <button type="submit" class="btn-primary">Répondre</button>
  </form>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from "vue";
import { useComments } from "@/composables/useComments";
import { useFormErrors } from "@/composables/useErrors";
// Use an alias for the forum Comment type to avoid conflicts with the built-in DOM 'Comment' type
import type { Comment as ForumComment } from "@/types/comment";

const props = defineProps<{
  topicId: number;
}>();
const emit = defineEmits<{
  (e: "comment-added", comment: ForumComment): void;
}>();

const { createComment } = useComments();
const newCommentContent = ref("");
const { errors, setErrors } = useFormErrors();

const submitComment = async () => {
  const result = await createComment({
    content: newCommentContent.value,
    topic: `/api/topics/${props.topicId}`,
  });

  if (result.success) {
    emit("comment-added", result.comment as ForumComment);
    newCommentContent.value = "";
  } else {
    setErrors(result.errors);
  }
};
</script>

<style scoped>
.comment-form {
  margin-top: 3rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1em;
}

.comment-form button {
  align-self: flex-start;
  width: auto;
}

@media (min-width: 769px) {
}
</style>
