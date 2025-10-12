import apiClient from "./client";
import type { Topic, TopicCreate, TopicUpdate } from "../types/topic";

export const topicsService = {
  /**
   * Getall topics (with pagination)
   */
  async getAll(page: number = 1): Promise<any> {
    const response = await apiClient.get("/api/topics", {
      params: { page },
    });
    return response.data;
  },

  /**
   * Get a topic by ID
   */
  async getById(id: number): Promise<Topic> {
    const response = await apiClient.get<Topic>(`/api/topics/${id}`);
    return response.data;
  },

  /**
   * Create a new topic
   */
  async create(data: TopicCreate): Promise<Topic> {
    const response = await apiClient.post<Topic>("/api/topics", data, {
      headers: {
        "Content-Type": "application/ld+json",
      },
    });
    return response.data;
  },

  /**
   * Update a topic
   */
  async update(id: number, data: TopicUpdate): Promise<Topic> {
    const response = await apiClient.patch<Topic>(`/api/topics/${id}`, data, {
      headers: {
        "Content-Type": "application/merge-patch+json",
      },
    });
    return response.data;
  },

  /**
   * Delete a topic
   */
  async delete(id: number): Promise<void> {
    await apiClient.delete(`/api/topics/${id}`);
  },
};
