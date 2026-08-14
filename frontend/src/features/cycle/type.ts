import type { TimeStamp } from "@/types/common";
import type z from "zod";
import type { disburseCycleSchema } from "./schema";
import type { MemberWithLedger } from "../members/type";

export type Cycle = TimeStamp & {
  id: number;
  round_number: number;
  cycle_number: number;
  due_date: string;
  group_id: number;
  recipient_member_id: number;
  disbursed_at: null | string;
  disbursed_amount: null | string;
  recipient: {
    id: number;
    name: string;
  };
  expected_total: number;
  collected_total: number;
  reserve_balance: number;
  recommended_disbursement: number;
  members: MemberWithLedger[] | null;
};

export type CycleSummary = Pick<
  Cycle,
  | "id"
  | "round_number"
  | "cycle_number"
  | "due_date"
  | "group_id"
  | "recipient_member_id"
  | "disbursed_at"
  | "disbursed_amount"
  | "created_at"
  | "updated_at"
> & {
  // Added via `withSum('contributions', 'amount')` on the group-detail
  // endpoint — total contributed toward this specific cycle, regardless of
  // its current status. Laravel returns aggregate columns as raw numeric
  // strings, so this stays untyped as `number | string` until parsed.
  contributions_sum_amount?: number | string | null;
};

export type DisburseCycleSchema = z.infer<typeof disburseCycleSchema>;