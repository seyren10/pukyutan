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

export const getGroupsQueryOptions = (
  params?: MaybeRefOrGetter<GroupQueryParams>,
) =>
  queryOptions({
    queryKey: ["groups", "list", params],
    queryFn: () => getGroups(toValue(params)),
  });

// Backs the spotlight-style search dialog in the main layout header. This is
// deliberately a separate branch of the query cache (["groups","search",...]
// vs. the dashboard's ["groups","list",...]) — typing in the search dialog
// must never refetch or otherwise disturb the dashboard's own paginated,
// filtered/sorted groups list underneath it.
export const getGroupSearchQueryOptions = (search: MaybeRefOrGetter<string>) =>
  queryOptions({
    queryKey: ["groups", "search", search],
    queryFn: () => getGroups({ search: toValue(search), per_page: 8 }),
    enabled: () => toValue(search).trim().length > 0,
  });

// Mirrors getGroupsQueryOptions above — the shared-groups list is a regular
// page-numbered listing (same PaginatedResponse shape from the API), so it
// pairs with AppPaginationBar the same way the owned-groups list does.
export const getSharedGroupsQueryOptions = (
  params?: MaybeRefOrGetter<GroupQueryParams>,
) =>
  queryOptions({
    queryKey: ["groups-shared", "list", params],
    queryFn: () => getSharedGroups(toValue(params)),
  });

export const getGroupDetailQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
  enabled: MaybeRefOrGetter<boolean> = true,
) =>
  queryOptions({
    queryKey: ["groups", "detail", groupId],
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
    queryKey: [
      "groups",
      "detail",
      toValue(groupId),
      "activities",
      "infinite-list",
    ],
    queryFn: ({ pageParam }) =>
      getGroupActivities(toValue(groupId), { page: pageParam }),
    initialPageParam: 1,
    getNextPageParam: (page) =>
      page.links.next !== null ? page.meta.current_page + 1 : undefined,
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
    queryKey: [
      "groups",
      "detail",
      toValue(groupId),
      "activities",
      "recent",
      toValue(limit),
    ],
    queryFn: () =>
      getGroupActivities(toValue(groupId), { per_page: toValue(limit) }),
  });