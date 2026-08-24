import z from "zod";

export const passwordsMatch = <
  T extends { password: string; password_confirmation: string },
>(
  data: T,
) => data.password === data.password_confirmation;

const passwordConfirmationOptions = {
  message: "Password didn't match",
  path: ["password"],
};

export const loginCredentialSchema = z.object({
  email: z.string().email(),
  password: z.string(),
});

const registrationBase = z.object({
  name: z.string().max(255),
  email: z.string().email(),
  password: z.string().min(8),
  password_confirmation: z.string(),
});

export const registrationSchema = z
  .object(registrationBase.shape)
  .refine(passwordsMatch, passwordConfirmationOptions);

export const userInfoSchema = registrationBase.pick({
  name: true,
  email: true,
});

export const resetPasswordSchema = z
  .object({
    ...registrationBase.omit({
      name: true,
    }).shape,
    token: z.string(),
  })
  .refine(passwordsMatch, passwordConfirmationOptions);

export const updatePasswordSchema = z
  .object({
    current_password: z.string(),
    ...registrationBase.pick({
      password: true,
      password_confirmation: true,
    }).shape,
  })
  .refine(passwordsMatch, passwordConfirmationOptions);

export const deleteAccountSchema = z.object({
  password: z.string(),
});
