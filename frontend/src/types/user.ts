export interface User {
  "@id": string;
  "@type": string;
  id?: number;
  username: string;
  avatarUrl?: string;
  roles?: string[];
}

export interface LoginCredentials {
  username: string;
  password: string;
}
