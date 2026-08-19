import type { TimeStamp } from "@/types/common";
import type { NOTIFICATION_TYPES } from "./constant";

type AppNotificationType = (typeof NOTIFICATION_TYPES)[number];

type NotificationPayloadMap = {
  "share.requested": {
    share_id: number;
    group_id: number;
    group_name: string;
    requester_id: number;
    requester_name: string;
  };
  "share.responded": {
    share_id: number;
    group_id: number;
    group_name: string;
    status: "accepted" | "rejected";
  };
  "share.revoked": {
    share_id: number;
    group_id: number;
    group_name: string;
  };
  "cycle.reminder": {
    cycle_id: number;
    cycle_number: number;
    due_date: string;
    group_id: number;
    group_name: string;
    contribution_amount: number;
    message: string;
  };
};

export type AppNotificationData = {
  [K in AppNotificationType]: {
    type: K;
  } & NotificationPayloadMap[K];
}[AppNotificationType];

export type AppNotification = TimeStamp & {
  id: string;
  type: AppNotificationType;
  data: AppNotificationData;
  read_at: string | null;
};
