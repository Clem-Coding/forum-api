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

        <button type="submit" class="btn-primary">Modifier</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTopics } from "@/composables/useTopics";
import { useFormErrors } from "@/composables/useFormErrors";

const { fetchTopicById } = useTopics();
const route = useRoute();
const { errors, setErrors } = useFormErrors();

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
  setErrors([]); // reset errors
  const result = await updateTopic(Number(topicId), { title: title.value, content: content.value });

  if (result.success) {
    await router.push(`/topic/${topicId}`);
  } else {
    setErrors(result.errors);
  }
};
</script>
