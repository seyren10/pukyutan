import type z from "zod";
import type { contributionSchema } from "./schema";
import type { TimeStamp } from "@/types/common";

export type Contribution = TimeStamp & {
  id: number;
  amount: string;
  member_id: number;
  paid_at: string | null;
  cycle_id: number;
};

export type ContributionSchema = z.infer<typeof contributionSchema>;
export type AddContributionPayload = ContributionSchema;
