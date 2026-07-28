import type { GroupFrequencyUnit } from "@/features/group/type";

/**
 * "Roy Victor" -> "RV". Falls back gracefully for single-word names.
 */
export const getInitials = (name: string): string => {
  const parts = name.trim().split(/\s+/);
  const initials = parts
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() ?? "");
  return initials.join("");
};

export const formatFrequencyLabel = (
  unit: GroupFrequencyUnit,
  interval: number,
): string => {
  if (interval === 1) {
    switch (unit) {
      case "day":
        return "Daily";
      case "week":
        return "Weekly";
      case "month":
        return "Monthly";
    }
  }

  return `every ${interval} ${unit}s`;
};
