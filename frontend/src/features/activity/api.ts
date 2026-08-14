import { httpClient } from "@/services/axios/axios";
import type { LaravelPaginatedResponse } from "@/types/paginate";
import type { UserActivity } from "./type";

export const getUserActivities = async (params?: {
  page?: number;
  per_page?: number;
}) => {
  const res = await httpClient.get<LaravelPaginatedResponse<UserActivity>>(
    "/api/v1/activities",
    { params },
  );

  return res.data;
};