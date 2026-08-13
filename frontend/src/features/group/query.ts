import { infiniteQueryOptions, queryOptions } from "@tanstack/vue-query";
import type { GroupQueryParams } from "./type";
import {
  getGroupDetail,
  getGroupRoundSummary,
  getGroups,
  getSharedGroups,
} from "./api";
import { toValue, type MaybeRefOrGetter } from "vue";

export const getGroupsInfiniteQueryOptions = (
  params?: MaybeRefOrGetter<GroupQueryParams>,
) =>
  infiniteQueryOptions({
    queryKey: ["groups", "infinite-list", params],
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

export const getGroupDetailQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
  enabled: MaybeRefOrGetter<boolean> = true,
) =>
  queryOptions({
    queryKey: ["groups", "detail", toValue(groupId)],
    queryFn: () => getGroupDetail(toValue(groupId)),
    enabled: () => toValue(enabled),
  });

/**
 * GROUP ROUNDS
 */

export const getGroupRoundSummaryQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
  round: MaybeRefOrGetter<number>,
  enabled: MaybeRefOrGetter<boolean> = true,
) =>
  queryOptions({
    queryKey: ["groups", "detail", toValue(groupId), "rounds", toValue(round)],
    queryFn: () => getGroupRoundSummary(toValue(groupId), toValue(round)),
    enabled,
  });
