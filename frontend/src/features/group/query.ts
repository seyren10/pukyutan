import { infiniteQueryOptions } from "@tanstack/vue-query";
import type { GroupQueryParams } from "./type";
import { getGroups, getSharedGroups } from "./api";
import { toValue, type MaybeRefOrGetter } from "vue";

export const getGroupsInfiniteQueryOptions = (
  params?: MaybeRefOrGetter<GroupQueryParams>,
) =>
  infiniteQueryOptions({
    queryKey: ["groups", "infinite-list", toValue(params)],
    queryFn: ({ pageParam }) =>
      getGroups({ ...toValue(params), page: pageParam }),
    initialPageParam: 1,
    getNextPageParam: (page) => {
      return page.links.next !== null ? page.meta.current_page + 1 : undefined;
    },
  });

export const getSharedGroupsInfiniteQueryOptions = (
  params?: MaybeRefOrGetter<GroupQueryParams>,
) =>
  infiniteQueryOptions({
    queryKey: ["groups-shared", "infinite-list", toValue(params)],
    queryFn: ({ pageParam }) =>
      getSharedGroups({ ...toValue(params), page: pageParam }),
    initialPageParam: 1,
    getNextPageParam: (page) => {
      return page.links.next !== null ? page.meta.current_page + 1 : undefined;
    },
  });
