import type { Comment } from './comment';
import type { User } from './user';

export interface Topic {
  '@id': string;
  '@type': string;
  id: number;
  title: string;
  content: string;
  createdAt: string;
  user: User;
  comments?: Comment[];
}

export interface TopicCreate {
  title: string;
  content: string;
}

export interface TopicUpdate {
  title?: string;
  content?: string;
}

export interface HydraCollection<T> {
  '@context': string;
  '@id': string;
  '@type': string;
  'hydra:member': T[];
  'hydra:totalItems': number;
}
