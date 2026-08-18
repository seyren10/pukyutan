import { getLocalTimeZone, today } from "@internationalized/date";
import z from "zod";
import { GROUP_FREQUENCY_UNIT } from "./constant";

export const createGroupSchema = z.object({
  name: z
    .string()
    .trim()
    .min(1, "Enter a group name")
    .max(255, "Keep the name under 255 characters"),
  contribution_amount: z.coerce
    .number({ invalid_type_error: "Enter an amount" })
    .min(0.01, "Enter an amount greater than ₱0"),
  frequency_unit: z.enum(GROUP_FREQUENCY_UNIT, {
    required_error: "Choose a contribution schedule",
  }),
  frequency_interval: z.coerce.number().int().min(1),
  start_date: z
    .string()
    .min(1, "Choose a start date")
    .refine(
      (value) => value >= today(getLocalTimeZone()).toString(),
      "Start date can't be in the past",
    ),
});

export const renameGroupSchema = z.object({
  name: createGroupSchema.shape.name,
});
