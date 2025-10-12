import type { User } from "./user";

export interface Comment {
  "@id": string;
  "@type": string;
  id?: number;
  content: string;
  createdAt: string;
  user: User;
}

export interface CommentCreate {
  content: string;
  topic: string; // IRI : "/api/topics/1"
}

export interface CommentUpdate {
  content: string;
}
