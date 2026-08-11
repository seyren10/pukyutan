import type z from "zod";
import type { memberSchema } from "./schema";
import type { TimeStamp } from "@/types/common";

export type Member = TimeStamp & {
  id: number;
  name: string;
  email?: string;
  payout_order: number;
  deleted_at: null | string;
  group_id: number;
};

export type RecentMember = {
  id: number;
  name: string;
  email: string;
  group_id: number;
};

export type MemberWithLedger = Member & {
  expected_total: number;
  paid_total: number;
  balance: number;
  laravel_through_key: number
};

export type MemberSchema = z.infer<typeof memberSchema>;
export type CreateMemberPayload = MemberSchema;
