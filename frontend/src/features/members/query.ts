import { queryOptions } from "@tanstack/vue-query";
import { toValue, type MaybeRefOrGetter } from "vue";
import { getGroupMembers } from "./api";

export const getGroupMembersQueryOptions = (
  groupId: MaybeRefOrGetter<number>,
) =>
  queryOptions({
    queryKey: () => ["groups", toValue(groupId), "members", "list"],
    queryFn: () => getGroupMembers(toValue(groupId)),
  });
