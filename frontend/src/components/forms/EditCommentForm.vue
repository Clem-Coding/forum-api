<template>
  <form class="comment-edit" @submit.prevent="save">
    <textarea
      v-model="content"
      rows="3"
      class="edit-textarea"
      @keydown.esc="$emit('cancel')"
      required
    ></textarea>
    <p v-if="errors.content" class="error-msg">{{ errors.content[0] }}</p>
    <div class="edit-actions">
      <button type="submit" class="btn-primary btn-sm">Modifier</button>
      <button type="button" @click="$emit('cancel')" class="btn-secondary btn-sm">Annuler</button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { Comment as ForumComment } from "@/types/comment";
import { useComments } from "@/composables/useComments";
import { useFormErrors } from "@/composables/useErrors";

const props = defineProps<{ comment: ForumComment }>();
const emit = defineEmits<{
  (e: "update", updated: ForumComment): void;
  (e: "cancel"): void;
}>();

const { updateComment } = useComments();
const { errors, setErrors } = useFormErrors();
const content = ref(props.comment.content);

const save = async () => {
  const result = await updateComment(props.comment.id!, { content: content.value });
  if (result && result.success) {
    emit("update", result.comment as ForumComment);
  } else if (result && result.errors) {
    setErrors(result.errors);
  }
};
</script>

<style scoped>
.edit-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-start;
  flex-direction: column;
  align-items: center;
}

.btn-sm {
  padding: 0.75em 1em;
  font-size: 0.875em;
  line-height: 1.25;
}

@media (min-width: 768px) {
  .edit-actions {
    flex-direction: row;
    justify-content: flex-start;
  }

  .edit-actions .btn-primary,
  .edit-actions .btn-secondary {
    width: auto;
  }
}
</style>
