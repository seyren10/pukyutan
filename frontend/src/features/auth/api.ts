import { httpClient } from "@/services/axios/axios";
import type {
  DeleteAccountPayload,
  LoginCredentials,
  RegistrationPayload,
  ResetPasswordSchema,
  UpdatePasswordPayload,
  User,
  UserInfoPayload,
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

export const updateProfile = async (payload: UserInfoPayload) => {
  const res = await httpClient.put<User>("/api/user", payload);
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

export const updatePassword = async (payload: UpdatePasswordPayload) => {
  await httpClient.put("/update-password", payload);
};

export const deleteAccount = async (payload: DeleteAccountPayload) => {
  await httpClient.post("/api/user", { ...payload, _method: "DELETE" });
};
/* DICEBEAR */
export const seedDicebear = async () => {
  const res = await httpClient.put<User>("/api/v1/dicebear");
  return res.data;
};

export const deleteDicebearSeed = async () => {
  const res = await httpClient.delete<User>("/api/v1/dicebear");
  return res.data;
};
