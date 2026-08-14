import { infiniteQueryOptions, queryOptions } from "@tanstack/vue-query";
import type { GroupQueryParams } from "./type";
import {
  getGroupActivities,
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

/**
 * GROUP ACTIVITY
 */

export const getGroupActivitiesInfiniteQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
) =>
  infiniteQueryOptions({
    queryKey: ["groups", toValue(groupId), "activities", "infinite-list"],
    queryFn: ({ pageParam }) =>
      getGroupActivities(toValue(groupId), { page: pageParam }),
    initialPageParam: 1,
    getNextPageParam: (page) =>
      page.next_page_url !== null ? page.current_page + 1 : undefined,
  });

// A small, single-shot fetch for the group-detail page's "recent activity"
// card — capped server-side via per_page rather than fetching a full page
// and slicing client-side. The full, filterable history lives on the
// activity page (via the infinite query above).
export const getRecentGroupActivitiesQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
  limit: MaybeRefOrGetter<number> = 5,
) =>
  queryOptions({
    queryKey: ["groups","detail", toValue(groupId), "activities", "recent", toValue(limit)],
    queryFn: () =>
      getGroupActivities(toValue(groupId), { per_page: toValue(limit) }),
  });