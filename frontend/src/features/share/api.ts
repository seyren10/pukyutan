import { httpClient } from "@/services/axios/axios";
import type { PaginatedResponse } from "@/types/paginate";
import type { GroupShare, GroupShareStatus } from "./type";
import type { JoinGroupSchema } from "./schema";
import type { QueryParams } from "@/types/common";

export const joinGroup = async (payload: JoinGroupSchema) => {
  const res = await httpClient.post<{ data: GroupShare }>(
    `/api/v1/groups/join/${payload.invite_code}`,
  );

  return res.data;
};

export const getGroupShareRequests = async (
  groupId: number,
  params?: { status?: GroupShareStatus },
) => {
  const res = await httpClient.get<{ data: GroupShare[] }>(
    `/api/v1/groups/${groupId}/share-requests`,
    { params },
  );

  return res.data;
};

export const getPendingShareRequests = async (params?: QueryParams) => {
  const res = await httpClient.get<PaginatedResponse<GroupShare>>(
    "/api/v1/share-requests/pending",
    { params },
  );

  return res.data;
};

export const acceptShareRequest = async (shareId: number) => {
  const res = await httpClient.post<{ data: GroupShare }>(
    `/api/v1/share-requests/${shareId}/accept`,
  );

  return res.data;
};

export const rejectShareRequest = async (shareId: number) => {
  const res = await httpClient.post<{ data: GroupShare }>(
    `/api/v1/share-requests/${shareId}/reject`,
  );

  return res.data;
};

export const revokeShareRequest = async (shareId: number) => {
  await httpClient.delete(`/api/v1/share-requests/${shareId}`);
};

export const leaveGroup = async (groupId: number) => {
  await httpClient.delete(`/api/v1/groups/${groupId}/leave`);
};
