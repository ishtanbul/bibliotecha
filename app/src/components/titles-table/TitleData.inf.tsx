export interface TitleData {
  id: string;
  title: string;
  authors: AuthorData[];
  genre: GenreData[];
}

export interface AuthorData {
  id: number;
  name: string;
}

export interface GenreData {
  id: number;
  genre: string;
}
