import { queryOptions } from "@tanstack/vue-query";
import { toValue, type MaybeRefOrGetter } from "vue";
import { getGroupShareRequests, getPendingShareRequests } from "./api";
import type { GroupShareStatus } from "./type";
import type { QueryParams } from "@/types/common";

export const getGroupShareRequestsQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
  status?: MaybeRefOrGetter<GroupShareStatus | undefined>,
) =>
  queryOptions({
    // Raw getters here, not toValue()'d — vue-query unwraps these
    // reactively itself. Resolving them eagerly (like the old version did)
    // freezes the key at whatever `status` was on mount, so switching tabs
    // never triggers a refetch.
    queryKey: ["groups", "detail", groupId, "share-requests", status],
    queryFn: () =>
      getGroupShareRequests(toValue(groupId), { status: toValue(status) }),
  });

// Small, single-shot fetch reused for both the dashboard banner's count
// (via `select`, page 1 only) and the full pending-requests inbox (paged).
export const getPendingShareRequestsQueryOptions = (
  params?: MaybeRefOrGetter<QueryParams>,
  enabled: boolean = true,
) => {
  return queryOptions({
    queryKey: ["share-requests", "pending", params],
    queryFn: () =>
      getPendingShareRequests({
        page: toValue(params)?.page ?? 1,
        per_page: toValue(params)?.per_page,
      }),
    enabled,
  });
};
