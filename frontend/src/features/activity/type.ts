import type { TimeStamp } from "@/types/common";
import type { GroupActivityEvent } from "./constants";

export type UserActivity = TimeStamp & {
  id: number;
  log_name: string | null;
  description: string;
  event: GroupActivityEvent | null;
  causer_type: string | null;
  causer_id: number | null;
  subject_type: string | null;
  subject_id: number | null;
  properties: Record<string, unknown>;
  group_id: number;
  group_name: string | null;
};