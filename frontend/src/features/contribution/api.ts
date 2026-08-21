import { httpClient } from "@/services/axios/axios";
import type { SimplePaginatedResponse } from "@/types/paginate";
import type { AddContributionPayload, Contribution } from "./type";

export const addContribution = async (
  cycleId: number,
  payload: AddContributionPayload,
) => {
  const res = await httpClient.post<{ data: Contribution }>(
    `/api/v1/cycles/${cycleId}/contributions`,
    payload,
  );

  return res.data;
};

export const getCycleContributions = async (cycleId: number, page?: number) => {
  const res = await httpClient.get<SimplePaginatedResponse<Contribution>>(
    `/api/v1/cycles/${cycleId}/contributions`,
    { params: { page } },
  );

  return res.data;
};

export const deleteContribution = async (contributionId: number) => {
  await httpClient.delete(`/api/v1/contributions/${contributionId}`);
};
