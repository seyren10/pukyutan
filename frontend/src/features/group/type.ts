import type { QueryParams, TimeStamp } from "@/types/common";
import type { GROUP_FREQUENCY_UNIT, GROUP_STATUS } from "./constant";
import type { Cycle } from "../cycle/type";
import type z from "zod";
import type { createGroupSchema } from "./schema";
import type { MemberWithLedger, RecentMember } from "../members/type";

export type GroupFrequencyUnit = (typeof GROUP_FREQUENCY_UNIT)[number];
export type GroupStatus = (typeof GROUP_STATUS)[number];
export type GroupSchema = z.infer<typeof createGroupSchema>;
export type CreateGroupSchema = GroupSchema;

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
  cycles: Cycle[];
  is_round_completed: boolean;
};

export type GroupDetail = Omit<Group, "recent_members" | "members_count"> & {
  members: MemberWithLedger[];
};

export type GroupQueryParams = QueryParams;
