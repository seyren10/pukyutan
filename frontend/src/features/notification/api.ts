import { httpClient } from "@/services/axios/axios";
import type { LaravelPaginatedResponse } from "@/types/paginate";
import type { AppNotification } from "./type";

export const getNotifications = async (params?: {
  page?: number;
  per_page?: number;
}) => {
  const res = await httpClient.get<LaravelPaginatedResponse<AppNotification>>(
    "/api/v1/notifications",
    { params },
  );

  return res.data;
};

export const markNotificationAsRead = async (notificationId: string) => {
  await httpClient.post(`/api/v1/notifications/${notificationId}/read`);
};

export const markAllNotificationsAsRead = async () => {
  await httpClient.post("/api/v1/notifications/read-all");
};
