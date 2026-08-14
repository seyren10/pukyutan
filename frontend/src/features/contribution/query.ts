import { infiniteQueryOptions } from "@tanstack/vue-query";
import { toValue, type MaybeRefOrGetter } from "vue";
import { getCycleContributions } from "./api";

export const getCycleContributionsInfiniteQueryOptions = (
  cycleId: MaybeRefOrGetter<number>,
  enabled: MaybeRefOrGetter<boolean> = true,
) =>
  infiniteQueryOptions({
    queryKey: ["cycles", "detail", toValue(cycleId), "contributions", "infinite-list"],
    queryFn: ({ pageParam }) =>
      getCycleContributions(toValue(cycleId), pageParam),
    initialPageParam: 1,
    getNextPageParam: (page) =>
      page.links.next !== null ? page.meta.current_page + 1 : undefined,
    enabled,
  });