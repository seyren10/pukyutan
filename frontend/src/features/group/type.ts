import type { QueryParams, TimeStamp } from "@/types/common";
import type { GROUP_FREQUENCY_UNIT, GROUP_STATUS } from "./constant";
import type { Cycle, CycleSummary } from "../cycle/type";
import type z from "zod";
import type { createGroupSchema, renameGroupSchema } from "./schema";
import type { Member, RecentMember } from "../members/type";

export type GroupFrequencyUnit = (typeof GROUP_FREQUENCY_UNIT)[number];
export type GroupStatus = (typeof GROUP_STATUS)[number];
export type GroupSchema = z.infer<typeof createGroupSchema>;
export type CreateGroupSchema = GroupSchema;
export type EditGroupSchema = Partial<GroupSchema>;
export type RenameGroupSchema = z.infer<typeof renameGroupSchema>;

export type Group = TimeStamp & {
  id: number;
  name: string;
  contribution_amount: string;
  frequency_unit: GroupFrequencyUnit;
  frequency_interval: number;
  start_date: string;
  status: GroupStatus;
  invite_code?: string;
  user_id: number;
  recent_members: RecentMember[];
  members_count: number;
  cycles_count: number;
  next_cycle: Cycle | null;
  user?: { id: number; name: string };
  cycles: CycleSummary[];
  is_round_completed: boolean;
};

export type GroupRoundSummary = {
  round_number: number;
  is_complete: boolean;
  total_collected: number;
  total_expected: number;
  members_with_outstanding_balance: number;
};

export type GroupDetail = Omit<Group, "recent_members" | "members_count"> & {
  members: Member[];
};

export type GroupLike = Group | GroupDetail;

export type GroupSortBy =
  | "created_at"
  | "name"
  | "start_date"
  | "contribution_amount";
export type GroupSortDir = "asc" | "desc";

export type GroupQueryParams = QueryParams & {
  search?: string;
  status?: GroupStatus;
  sort_by?: GroupSortBy;
  sort_dir?: GroupSortDir;
};

export type GroupActivity = TimeStamp & {
  id: number;
  log_name: string | null;
  description: string;
  event: string | null;
  causer_type: string | null;
  causer_id: number | null;
  subject_type: string | null;
  subject_id: number | null;
  properties: Record<string, unknown>;
};