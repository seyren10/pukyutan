import { httpClient } from "@/services/axios/axios";
import type {
  CreateMemberPayload,
  DownloadMemberLedgerPdfPayload,
  Member,
  MemberLedgerCycle,
  MemberWithSummary,
  UpdateMemberPayload,
} from "./type";
import { filenameFromContentDisposition } from "@/lib/helpers";

export const getGroupMembers = async (groupId: number) => {
  const res = await httpClient.get<{ data: Member[] }>(
    `/api/v1/groups/${groupId}/members`,
  );

  return res.data;
};

// Same endpoint as getGroupMembers, opted into the per-member ledger
// summary via `include=summary` — kept as its own function/query key so the
// plain member list used elsewhere doesn't pay for a calculation it never
// asked for.
export const getGroupMembersWithSummary = async (groupId: number) => {
  const res = await httpClient.get<{ data: MemberWithSummary[] }>(
    `/api/v1/groups/${groupId}/members`,
    { params: { include: "summary" } },
  );

  return res.data;
};

export const getMemberLedger = async (memberId: number) => {
  const res = await httpClient.get<MemberLedgerCycle[]>(
    `/api/v1/members/${memberId}/ledger`,
  );

  return res.data;
};
export const addMember = async (
  groupId: number,
  payload: CreateMemberPayload,
) => {
  const res = await httpClient.post(
    `/api/v1/groups/${groupId}/members`,
    payload,
  );

  return res.data;
};

export const updateMember = async (
  memberId: number,
  payload: UpdateMemberPayload,
) => {
  const res = await httpClient.put<{ data: Member }>(
    `/api/v1/members/${memberId}`,
    payload,
  );

  return res.data;
};

export const removeMember = async (memberId: number) => {
  await httpClient.delete(`/api/v1/members/${memberId}`);
};

export const reorderMembers = async (groupId: number, memberIds: number[]) => {
  const res = await httpClient.put<{ data: Member[] }>(
    `/api/v1/groups/${groupId}/members/reorder`,
    {
      member_ids: memberIds,
    },
  );

  return res.data;
};

export const downloadMemberLedgerPdf = async ({
  memberId,
  memberName,
}: DownloadMemberLedgerPdfPayload) => {
  const response = await httpClient.get(`/api/v1/members/${memberId}/ledger/pdf`, {
    responseType: "blob",
  });

  const filename = filenameFromContentDisposition(
    response.headers["content-disposition"],
    `${memberName}-ledger.pdf`,
  );

  const url = URL.createObjectURL(response.data);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  link.click();
  URL.revokeObjectURL(url);
};
