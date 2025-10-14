<template>
  <div class="card-container">
    <div class="card">
      <h2>Créer un nouveau topic</h2>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="title">Titre</label>
          <input
            v-model="title"
            id="title"
            type="text"
            placeholder="Entrez le titre du topic"
            required
          />
          <p v-if="errors.title" class="error-msg">{{ errors.title[0] }}</p>
        </div>

        <div class="form-group">
          <label for="content">Contenu</label>
          <textarea
            v-model="content"
            id="content"
            placeholder="Décrivez votre sujet"
            rows="6"
            required
          ></textarea>
          <p v-if="errors.content" class="error-msg">{{ errors.content[0] }}</p>
        </div>

        <button type="submit" class="btn-primary">Publier</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useTopics } from "@/composables/useTopics";
import { useFormErrors } from "@/composables/useErrors";
import { useRouter } from "vue-router";

const title = ref("");
const content = ref("");
const router = useRouter();
const { errors, setErrors } = useFormErrors();
const { createTopic, topics } = useTopics();

const submitForm = async () => {
  setErrors([]); // reset errors
  const result = await createTopic({ title: title.value, content: content.value });

  if (result.success) {
    if (topics && Array.isArray(topics.value)) {
      topics.value.unshift(result.topic);
    }

    title.value = "";
    content.value = "";
    router.push("/");
  } else {
    setErrors(result.errors);
  }
};
</script>
