import type { TimeStamp } from "@/types/common";
import type z from "zod";
import type { disburseCycleSchema } from "./schema";

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
};

export type DisburseCycleSchema = z.infer<typeof disburseCycleSchema>;
