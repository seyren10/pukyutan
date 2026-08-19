import type { TimeStamp } from "@/types/common";

type ShareRequestedData = {
  type: "share.requested";
  share_id: number;
  group_id: number;
  group_name: string;
  requester_id: number;
  requester_name: string;
};

type ShareRespondedData = {
  type: "share.responded";
  share_id: number;
  group_id: number;
  group_name: string;
  status: "accepted" | "rejected";
};

type ShareRevokedData = {
  type: "share.revoked";
  share_id: number;
  group_id: number;
  group_name: string;
};

export type AppNotificationData =
  | ShareRequestedData
  | ShareRespondedData
  | ShareRevokedData;

export type AppNotification = TimeStamp & {
  id: string;
  type: string;
  data: AppNotificationData;
  read_at: string | null;
};
