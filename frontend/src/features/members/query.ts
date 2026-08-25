import { queryOptions } from "@tanstack/vue-query";
import { toValue, type MaybeRefOrGetter } from "vue";
import { getGroupMembers, getGroupMembersWithSummary, getMemberLedger } from "./api";

export const getGroupMembersQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
) =>
  queryOptions({
    queryKey: () => ["groups", toValue(groupId), "members", "list"],
    queryFn: () => getGroupMembers(toValue(groupId)),
  });

// Distinct query key ("with-summary") from the plain list above — same
// endpoint, different response shape, so they can't share a cache entry.
export const getGroupMembersWithSummaryQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
) =>
  queryOptions({
    queryKey: () => ["groups", toValue(groupId), "members", "with-summary"],
    queryFn: () => getGroupMembersWithSummary(toValue(groupId)),
  });

export const getMemberLedgerQueryOptions = (
  memberId: MaybeRefOrGetter<number>,
) =>
  queryOptions({
    queryKey: () => ["members", toValue(memberId), "ledger"],
    queryFn: () => getMemberLedger(toValue(memberId)),
  });
