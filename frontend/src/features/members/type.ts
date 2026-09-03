import type z from "zod";
import type { memberSchema } from "./schema";
import type { TimeStamp } from "@/types/common";
import type { MEMBER_LEDGER_PDF_EXPORT_STATUS } from "./constant";

export type Member = TimeStamp & {
  id: number;
  name: string;
  email?: string | null;
  payout_order: number;
  deleted_at: null | string;
  group_id: number;
  dicebear_seed: string | null;
};

export type RecentMember = {
  id: number;
  name: string;
  email: string;
  group_id: number;
  dicebear_seed: string | null;
};

export type MemberWithLedger = Member & {
  expected_total: number;
  paid_total: number;
  balance: number;
  laravel_through_key: number;
};

// Lifetime totals as of today — see LedgerCalculatorService::balanceForMember().
// Only present on members fetched with `?include=summary`.
export type MemberLedgerSummary = {
  expected_total: number;
  paid_total: number;
  balance: number;
};

export type MemberWithSummary = Member & {
  summary: MemberLedgerSummary;
};

// One row per cycle from MemberLedgerController — see
// LedgerCalculatorService::ledgerForMember().
export type MemberLedgerCycle = {
  cycle_number: number;
  round_number: number;
  due_date: string;
  expected: number;
  paid: number;
  running_balance: number;
  contributions: {
    id: number;
    amount: number;
    paid_at: string | null;
    notes: string | null;
  }[];
};

export type MemberLedgerPdfExportStatus =
  (typeof MEMBER_LEDGER_PDF_EXPORT_STATUS)[number];
  
export type GenerateMemberLedgerPdfResponse = {
  export_id: number;
  status: MemberLedgerPdfExportStatus;
};

export type MemberLedgerPdfExportStatusResponse = {
  status: MemberLedgerPdfExportStatus;
  download_url: string | null;
  error: string | null;
};

export type MemberSchema = z.infer<typeof memberSchema>;
export type CreateMemberPayload = MemberSchema;
export type UpdateMemberPayload = MemberSchema;
export type DownloadMemberLedgerPdfPayload = {
  memberId: number;
  memberName: string;
};
