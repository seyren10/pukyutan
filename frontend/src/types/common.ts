import type { AxiosError } from "axios";

export type LaravelError = AxiosError<{
  message?: string;
}>;
