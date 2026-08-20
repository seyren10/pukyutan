import type { BadgeVariants } from "@/components/ui/badge";

export const GROUP_FREQUENCY_UNIT = ["day", "week", "month"] as const;
export const GROUP_STATUS = ["draft", "active", "completed"] as const;

// Single source of truth for how a status renders — mirrors the Badge
// variants GroupCard already used inline (variant="success"/"accent"/
// "secondary"), now shared with the status filter and search results too.
export const GROUP_STATUS_META: Record<
  (typeof GROUP_STATUS)[number],
  { label: string; badgeVariant: BadgeVariants["variant"] }
> = {
  draft: { label: "Draft", badgeVariant: "accent" },
  active: { label: "Active", badgeVariant: "success" },
  completed: { label: "Completed", badgeVariant: "secondary" },
};

export const GROUP_SORT_OPTIONS = [
  { value: "created_at:desc", label: "Newest first", sort_by: "created_at", sort_dir: "desc" },
  { value: "created_at:asc", label: "Oldest first", sort_by: "created_at", sort_dir: "asc" },
  { value: "name:asc", label: "Name (A–Z)", sort_by: "name", sort_dir: "asc" },
  { value: "name:desc", label: "Name (Z–A)", sort_by: "name", sort_dir: "desc" },
  { value: "start_date:asc", label: "Start date (earliest)", sort_by: "start_date", sort_dir: "asc" },
  { value: "start_date:desc", label: "Start date (latest)", sort_by: "start_date", sort_dir: "desc" },
  { value: "contribution_amount:desc", label: "Contribution (high–low)", sort_by: "contribution_amount", sort_dir: "desc" },
  { value: "contribution_amount:asc", label: "Contribution (low–high)", sort_by: "contribution_amount", sort_dir: "asc" },
] as const satisfies ReadonlyArray<{
  value: string;
  label: string;
  sort_by: "created_at" | "name" | "start_date" | "contribution_amount";
  sort_dir: "asc" | "desc";
}>;