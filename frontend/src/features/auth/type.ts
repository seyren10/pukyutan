import type z from "zod";
import type {
  loginCredentialSchema,
  registrationSchema,
  resetPasswordSchema,
  userInfoSchema,
} from "./schema";

export type LoginCredentials = {
  email: string;
  password: string;
};

export type User = {
  id: number;
  name: string;
  email: string;
  email_verified_at: string;
  google_id: null | string;
  avatar: null | string;
  dicebear_seed: string | null;
};

export type LoginCredential = z.infer<typeof loginCredentialSchema>;
export type ResetPasswordSchema = z.infer<typeof resetPasswordSchema>;
export type RegistrationPayload = z.infer<typeof registrationSchema>;
export type UserInfoPayload = z.infer<typeof userInfoSchema>;
