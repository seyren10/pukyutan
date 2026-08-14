import { computed, type Ref } from "vue";
import { isToday, isYesterday, format } from "date-fns";
import type { UserActivity } from "../type";

export type ActivityDayGroup = {
  label: string;
  items: UserActivity[];
};

export function useGroupedActivities(activities: Ref<UserActivity[] | undefined>) {
  return computed<ActivityDayGroup[]>(() => {
    const groups = new Map<string, UserActivity[]>();

    for (const activity of activities.value ?? []) {
      const date = new Date(activity.created_at);
      const label = isToday(date)
        ? "Today"
        : isYesterday(date)
          ? "Yesterday"
          : format(date, "MMMM d, yyyy");

      const list = groups.get(label) ?? [];
      list.push(activity);
      groups.set(label, list);
    }

    return [...groups.entries()].map(([label, items]) => ({ label, items }));
  });
}