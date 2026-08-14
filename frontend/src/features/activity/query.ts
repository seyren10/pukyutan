import { infiniteQueryOptions } from "@tanstack/vue-query";
import { getUserActivities } from "./api";

export const getUserActivitiesInfiniteQueryOptions = () =>
  infiniteQueryOptions({
    queryKey: ["activities", "infinite-list"],
    queryFn: ({ pageParam }) => getUserActivities({ page: pageParam }),
    initialPageParam: 1,
    getNextPageParam: (page) =>
      page.next_page_url !== null ? page.current_page + 1 : undefined,
  });