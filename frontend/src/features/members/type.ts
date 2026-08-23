import type z from "zod";
import type { memberSchema } from "./schema";
import type { TimeStamp } from "@/types/common";

export type Member = TimeStamp & {
  id: number;
  name: string;
  email?: string | null;
  payout_order: number;
  deleted_at: null | string;
  group_id: number;
  dicebear_seed: string | null;
};

export type RecentMember = {
  id: number;
  name: string;
  email: string;
  group_id: number;
  dicebear_seed: string | null;
};

export type MemberWithLedger = Member & {
  expected_total: number;
  paid_total: number;
  balance: number;
  laravel_through_key: number;
};

export type MemberSchema = z.infer<typeof memberSchema>;
export type CreateMemberPayload = MemberSchema;
export type UpdateMemberPayload = MemberSchema;
