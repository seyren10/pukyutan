import { z } from "zod";

export const contributionSchema = z.object({
  amount: z.coerce
    .number({ invalid_type_error: "Enter a number." })
    .min(0.01, "Amount can't be negative."),
  member_id: z.number(),
  paid_at: z.string(),
  notes: z.string().max(255).optional(),
});
