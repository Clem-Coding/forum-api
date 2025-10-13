import apiClient from "./client";
import type { Comment, CommentCreate } from "../types/comment";

export const commentsService = {
  async create(data: CommentCreate): Promise<Comment> {
    const response = await apiClient.post<Comment>("/api/comments", data, {
      headers: {
        "Content-Type": "application/ld+json",
      },
    });
    return response.data;
  },

  async getByTopic(topicId: number): Promise<Comment[]> {
    const response = await apiClient.get<{ member: Comment[] }>(`/api/comments?topic=${topicId}`);
    return response.data.member || [];
  },

  async update(id: number, data: Partial<CommentCreate>): Promise<Comment> {
    const response = await apiClient.patch<Comment>(`/api/comments/${id}`, data, {
      headers: {
        "Content-Type": "application/merge-patch+json",
      },
    });
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await apiClient.delete(`/api/comments/${id}`);
  },
};
