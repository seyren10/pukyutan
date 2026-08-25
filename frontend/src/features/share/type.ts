import type { TimeStamp } from "@/types/common";
import type { GROUP_SHARE_STATUS } from "./constant";

export type GroupShareStatus = (typeof GROUP_SHARE_STATUS)[number];

export type GroupShare = TimeStamp & {
  id: number;
  status: GroupShareStatus;
  requested_at: string;
  responded_at: string | null;
  group_id: number;
  // Only present on the cross-group pending feed — the per-group list
  // already has group context from the page it's rendered on.
  group_name?: string | null;
  user: {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    dicebear_seed: string| null;
  };
};
