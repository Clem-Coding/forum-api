import { ref } from "vue";
import { topicsService } from "../api/topics";
import type { Topic, TopicCreate, TopicUpdate } from "../types/topic";

export function useTopics() {
  const topics = ref<Topic[]>([]);
  const currentTopic = ref<Topic | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const totalItems = ref(0);

  // Get all topics with pagination
  const fetchTopics = async (page: number = 2) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await topicsService.getAll(page);
      topics.value = data.member || data["hydra:member"];
      totalItems.value = data.totalItems || data["hydra:totalItems"];
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors du chargement des topics";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Get a topic by ID
  const fetchTopicById = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await topicsService.getById(id);
      currentTopic.value = data;
      return data;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Topic non trouvé";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Create a new topic
  const createTopic = async (data: TopicCreate) => {
    loading.value = true;
    error.value = null;
    try {
      const newTopic = await topicsService.create(data);
      topics.value.unshift(newTopic); // Add to the top of the list
      return newTopic;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la création";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Update a topic
  const updateTopic = async (id: number, data: TopicUpdate) => {
    loading.value = true;
    error.value = null;
    try {
      const updatedTopic = await topicsService.update(id, data);

      // Update in the list
      const index = topics.value.findIndex((t) => t.id === id);
      if (index !== -1) {
        topics.value[index] = updatedTopic;
      }

      // Update current topic
      if (currentTopic.value?.id === id) {
        currentTopic.value = updatedTopic;
      }
      return updatedTopic;
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la modification";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  // Delete a topic
  const deleteTopic = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      await topicsService.delete(id);

      // Remove from the list
      topics.value = topics.value.filter((t) => t.id !== id);

      // Reset current topic if it's the one being deleted
      if (currentTopic.value?.id === id) {
        currentTopic.value = null;
      }
    } catch (e: any) {
      error.value = e.response?.data?.message || "Erreur lors de la suppression";
      throw e;
    } finally {
      loading.value = false;
    }
  };

  return {
    topics,
    currentTopic,
    loading,
    error,
    totalItems,
    fetchTopics,
    fetchTopicById,
    createTopic,
    updateTopic,
    deleteTopic,
  };
}
