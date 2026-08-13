import type { GroupFrequencyUnit } from "@/features/group/type";
import { addDays, addMonths, addWeeks, format, parseISO } from "date-fns";

export function advanceDate(
  baseDate: Date,
  unit: GroupFrequencyUnit,
  interval: number,
  steps = 1,
): Date {
  const amount = interval * steps;

  switch (unit) {
    case "day":
      return addDays(baseDate, amount);
    case "week":
      return addWeeks(baseDate, amount);
    case "month":
      return addMonths(baseDate, amount);
  }
}

/**
 * Computes the label for a new round's first cycle — the last cycle's
 * due date, advanced by one more interval. Matches how the backend
 * continues numbering/dating into a new round rather than restarting
 * from the group's original start_date.
 */
export function formatNextRoundFirstDueLabel(
  lastCycleDueDate: string,
  frequencyUnit: GroupFrequencyUnit,
  frequencyInterval: number,
): string {
  const nextDate = advanceDate(
    parseISO(lastCycleDueDate),
    frequencyUnit,
    frequencyInterval,
    1,
  );
  return format(nextDate, "MMM d, yyyy");
}
