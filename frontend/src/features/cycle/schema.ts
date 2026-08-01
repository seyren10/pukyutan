import { z } from "zod";

export const disburseCycleSchema = z.object({
  disbursed_amount: z.coerce
    .number({ invalid_type_error: "Enter a number." })
    .min(0.01, "Amount can't be negative."),
});
