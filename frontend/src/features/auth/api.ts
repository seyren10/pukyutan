import { httpClient } from "@/services/axios/axios";
import type {
  LoginCredentials,
  RegistrationPayload,
  ResetPasswordSchema,
  User,
} from "./type";

export const login = async (payload: LoginCredentials) => {
  await getCsrfCookie();
  const res = await httpClient.post<User>("/login", payload);

  return res.data;
};

export const getUser = async () => {
  const res = await httpClient.get<User>("/api/user");
  return res.data;
};

export const logout = async () => {
  await httpClient.post("/logout");
};

export const register = async (payload: RegistrationPayload) => {
  const res = await httpClient.post<User>("/register", payload);
  return res.data;
};

export const getCsrfCookie = async () => {
  await httpClient.get("/sanctum/csrf-cookie");
};

export const sendEmailVerification = async () => {
  await httpClient.post("/email/verification-notification");
};

export const forgotPassword = async (
  payload: Pick<LoginCredentials, "email">,
) => await httpClient.post("/forgot-password", payload);

export const resetPassword = async (payload: ResetPasswordSchema) => {
  await httpClient.post("/reset-password", payload);
};
