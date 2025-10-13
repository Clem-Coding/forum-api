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
        </div>

        <button type="submit" class="btn-primary">Publier</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";

const title = ref("");
const content = ref("");
const router = useRouter();

const { createTopic } = useTopics();

const submitForm = async () => {
  await createTopic({ title: title.value, content: content.value });
  await router.push("/");
};
</script>
