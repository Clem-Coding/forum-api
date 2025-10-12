export interface User {
  '@id': string;
  '@type': string;
  id?: number;
  username: string;
  email?: string;
  avatarUrl?: string;
}

export interface UserCreate {
  username: string;
  email: string;
  password: string;
}
