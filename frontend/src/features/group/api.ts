import { httpClient } from "@/services/axios/axios";
import type { SimplePaginatedResponse, LaravelPaginatedResponse } from "@/types/paginate";
import type {
  CreateGroupSchema,
  Group,
  GroupActivity,
  GroupDetail,
  GroupQueryParams,
  GroupRoundSummary,
} from "./type";

export const getGroups = async (params?: GroupQueryParams) => {
  const res = await httpClient.get<SimplePaginatedResponse<Group>>(
    "/api/v1/groups",
    { params },
  );
  return res.data;
};

export const getGroupDetail = async (groupId: number) => {
  const res = await httpClient.get<{ data: GroupDetail }>(
    `/api/v1/groups/${groupId}`,
  );
  return res.data;
};

export const getSharedGroups = async (params?: GroupQueryParams) => {
  const res = await httpClient.get<SimplePaginatedResponse<Group>>(
    "/api/v1/groups/shared",
    { params },
  );
  return res.data;
};

export const createGroup = async (payload: CreateGroupSchema) => {
  const res = await httpClient.post<{ data: Group }>("/api/v1/groups", payload);
  return res.data;
};

export const activateGroup = async (groupId: number) => {
  const res = await httpClient.post<{ data: Group }>(
    `/api/v1/groups/${groupId}/activate`,
  );

  return res.data;
};

/**
 * GROUP ROUNDS
 */

export const getGroupRoundSummary = async (groupId: number, round: number) => {
  const res = await httpClient.get<GroupRoundSummary>(
    `/api/v1/groups/${groupId}/rounds/${round}/summary`,
  );

  return res.data;
};

export const startNewRound = async (groupId: number) => {
  const res = await httpClient.post<{ data: Group }>(
    `/api/v1/groups/${groupId}/rounds`,
  );

  return res.data;
};

/**
 * GROUP ACTIVITY
 */

export const getGroupActivities = async (
  groupId: number,
  params?: GroupQueryParams,
) => {
  const res = await httpClient.get<LaravelPaginatedResponse<GroupActivity>>(
    `/api/v1/groups/${groupId}/activities`,
    { params },
  );

  return res.data;
};