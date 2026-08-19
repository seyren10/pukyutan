import { queryOptions } from "@tanstack/vue-query";
import { getNotifications } from "./api";

export const getNotificationsQueryOptions = () =>
  queryOptions({
    queryKey: ["notifications", "list"],
    queryFn: () => getNotifications({ per_page: 10 }),
    refetchInterval: 30_000,
  });
