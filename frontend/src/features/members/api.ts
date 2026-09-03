import { httpClient } from "@/services/axios/axios";
import type {
  CreateMemberPayload,
  DownloadMemberLedgerPdfPayload,
  GenerateMemberLedgerPdfResponse,
  Member,
  MemberLedgerCycle,
  MemberLedgerPdfExportStatusResponse,
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

// #region PDF Export
export const generateMemberLedgerPdf = async (memberId: number) => {
  const res = await httpClient.post<GenerateMemberLedgerPdfResponse>(
    `/api/v1/members/${memberId}/ledger-pdf`,
  );

  return res.data;
};

export const getMemberLedgerPdfExportStatus = async (
  memberId: number,
  exportId: number,
) => {
  const res = await httpClient.get<MemberLedgerPdfExportStatusResponse>(
    `/api/v1/members/${memberId}/ledger-pdf/${exportId}`,
  );

  return res.data;
};

const wait = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

// Matches the queue's retry_after (120s) with a little headroom, so we
// never give up on a job the backend is still legitimately retrying.
const POLL_INTERVAL_MS = 2000;
const MAX_POLL_ATTEMPTS = 65;

const pollMemberLedgerPdfExport = async (
  memberId: number,
  exportId: number,
): Promise<MemberLedgerPdfExportStatusResponse> => {
  for (let attempt = 0; attempt < MAX_POLL_ATTEMPTS; attempt++) {
    const result = await getMemberLedgerPdfExportStatus(memberId, exportId);

    if (result.status === "completed" || result.status === "failed") {
      return result;
    }

    await wait(POLL_INTERVAL_MS);
  }

  throw new Error("Timed out waiting for the PDF to finish generating.");
};

const downloadMemberLedgerPdfFile = async (
  memberId: number,
  exportId: number,
  memberName: string,
) => {
  const response = await httpClient.get(
    `/api/v1/members/${memberId}/ledger-pdf/${exportId}/download`,
    { responseType: "blob" },
  );

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

export const generateAndDownloadMemberLedgerPdf = async ({
  memberId,
  memberName,
}: DownloadMemberLedgerPdfPayload) => {
  const { export_id } = await generateMemberLedgerPdf(memberId);
  const result = await pollMemberLedgerPdfExport(memberId, export_id);

  if (result.status === "failed") {
    throw new Error(result.error ?? "PDF generation failed.");
  }

  await downloadMemberLedgerPdfFile(memberId, export_id, memberName);
};

// #endregion PDF Export
