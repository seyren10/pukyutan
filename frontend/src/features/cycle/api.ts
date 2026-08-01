import type { DisburseCycleSchema } from "./type";
import { httpClient } from "@/services/axios/axios";

export const disburseCycle = async (
  cycleId: number,
  payload: DisburseCycleSchema,
) => {
  const res = await httpClient.post(
    `/api/v1/cycles/${cycleId}/disburse`,
    payload,
  );
  res.data;
};
