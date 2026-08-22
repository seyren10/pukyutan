import z from "zod";

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

export const registrationSchema = z.object(registrationBase.shape).refine(
  (data) => {
    const { password, password_confirmation } = data;
    return password === password_confirmation;
  },
  {
    message: "Password didn't match",
    path: ["password"],
  },
);

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
