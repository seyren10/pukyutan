import { queryOptions } from "@tanstack/vue-query";
import { getDashboardStats } from "./api";

// Deliberately keyed under the "groups" prefix (not a separate "dashboard"
// key) so every existing mutation that already does
// `invalidateQueries({ queryKey: ["groups"] })` — creating/editing/deleting
// a group, activating, adding/removing/reordering members, starting a new
// round, disbursing a cycle — keeps this fresh for free via TanStack
// Query's prefix matching, with no extra call sites to maintain.
export const getDashboardStatsQueryOptions = () =>
  queryOptions({
    queryKey: ["groups", "stats"],
    queryFn: getDashboardStats,
  });
