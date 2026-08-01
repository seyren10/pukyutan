import { httpClient } from "@/services/axios/axios";
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
