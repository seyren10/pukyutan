import type { AxiosError } from "axios";

export type LaravelError = AxiosError<{
  message?: string;
}>;

export type TimeStamp = {
  created_at: string;
  updated_at: string;
};

export type QueryParams = {
  page?: string | number;
  per_page?: string | number;
};
