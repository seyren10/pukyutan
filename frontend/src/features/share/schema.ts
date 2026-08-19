import z from "zod";

export const joinGroupSchema = z.object({
  invite_code: z
    .string()
    .trim()
    .min(1, "Enter an invite code")
    .length(6, "Invite codes are 6 characters")
    .regex(/^[a-z0-9]+$/i, "Invite codes are letters and numbers only")
    .transform((value) => value.toUpperCase()),
});

export type JoinGroupSchema = z.infer<typeof joinGroupSchema>;
