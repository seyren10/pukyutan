export interface PaginationLink {
  url: string | null;
  label: string;
  page: number | null;
  active: boolean;
}

export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  links: PaginationLink[];
  path: string;
  per_page: number;
  to: number | null;
  total: number;
}

export interface PaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: PaginationLinks;
  meta: PaginationMeta;
}

export interface SimplePaginationMeta {
  current_page: number;
  current_page_url: string;
  from: number | null;
  path: string;
  per_page: number | string;
  to: number | null;
}

export interface SimplePaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface SimplePaginatedResponse<T> {
  data: T[];
  links: SimplePaginationLinks;
  meta: SimplePaginationMeta;
}
