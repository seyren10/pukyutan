import z from "zod";

export const loginCredentialSchema = z.object({
  email: z.string().email(),
  password: z.string(),
});

export const resetPasswordSchema = z
  .object({
    email: z.string().email(),
    password: z.string().min(8),
    password_confirmation: z.string(),
    token: z.string(),
  })
  .refine(
    (data) => {
      const { password, password_confirmation } = data;
      return password === password_confirmation;
    },
    {
      message: "Password didn't match",
      path: ["password"],
    },
  );
