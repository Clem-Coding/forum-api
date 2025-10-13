<template>
  <div class="card-container">
    <div class="card">
      <h2>Modifier un topic</h2>

      <div v-if="loading" class="loading">Chargement...</div>
      <div v-if="error" class="error">{{ error }}</div>

      <form v-if="!loading && !error" @submit.prevent="submitForm">
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

        <button type="submit" class="btn-primary">Modifier</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";

const { fetchTopicById } = useTopics();
const route = useRoute();

const title = ref("");
const content = ref("");
const loading = ref(false);
const error = ref<string | null>(null);

const currentTopic = ref<any>(null);

const topicId = route.params.id as string | undefined;

const fetchTopic = async (id: string) => {
  loading.value = true;
  error.value = null;
  try {
    const data = await fetchTopicById(Number(id));
    currentTopic.value = data;
    title.value = data.title;
    content.value = data.content;
  } catch (e: any) {
    error.value = e.response?.data?.message || "Topic non trouvé";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  if (topicId) {
    fetchTopic(topicId);
  }
});

const router = useRouter();
const { updateTopic } = useTopics();

const submitForm = async () => {
  if (!topicId) return;
  loading.value = true;
  error.value = null;
  try {
    await updateTopic(Number(topicId), { title: title.value, content: content.value });
    await router.push(`/topic/${topicId}`);
  } catch (e: any) {
    error.value = e.response?.data?.message || "Erreur lors de la modification";
  } finally {
    loading.value = false;
  }
};
</script>
