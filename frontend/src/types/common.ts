import type { AxiosError } from "axios";

export type LaravelError<T = any> = AxiosError<{
  message?: string;
  errors?: T;
}>;

export type TimeStamp = {
  created_at: string;
  updated_at: string;
};

export type QueryParams = {
  page?: string | number;
  per_page?: string | number;
};
