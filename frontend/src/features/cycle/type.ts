import type { TimeStamp } from "@/types/common";

export type Cycle = TimeStamp & {
  id: number;
  round_number: number;
  cycle_number: number;
  due_date: string;
  group_id: number;
  recipient_member_id: number;
  disbursed_at: null | string;
  disbursed_amount: null | string;
};
