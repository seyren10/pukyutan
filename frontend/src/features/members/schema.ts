import { z } from "zod";

export const memberSchema = z.object({
  name: z
    .string()
    .trim()
    .min(1, "Enter this member's name.")
    .max(255, "Name must be at most 255 characters."),

  email: z
    .string()
    .email("Enter a valid email address.")
    .max(255, "Email must be at most 255 characters.")
    .optional(),
});
