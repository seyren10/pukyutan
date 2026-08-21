import { httpClient } from "@/services/axios/axios";
import type { DashboardStats } from "./type";

export const getDashboardStats = async () => {
  const res = await httpClient.get<{ data: DashboardStats }>(
    "/api/v1/dashboard/stats",
  );

  return res.data;
};
