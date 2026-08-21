import {
  Coins,
  HandCoins,
  RefreshCw,
  Rocket,
  PartyPopper,
  UserCheck,
  UserX,
  History,
  type LucideIcon,
  Undo,
} from "@lucide/vue";
import type { GroupActivityEvent } from "@/features/activity/constants";

type EventTone = "money" | "lifecycle" | "share" | "default";

const TONE_CLASS: Record<EventTone, string> = {
  money: "bg-primary/10 text-primary",
  lifecycle: "bg-accent text-accent-foreground",
  share: "bg-muted text-muted-foreground",
  default: "bg-muted text-muted-foreground",
};

const EVENT_VISUAL: Record<
  GroupActivityEvent,
  { icon: LucideIcon; tone: EventTone }
> = {
  "contribution.recorded": { icon: Coins, tone: "money" },
  "contribution.undo": { icon: Undo, tone: "default" },
  "cycle.disbursed": { icon: HandCoins, tone: "money" },
  "round.started": { icon: RefreshCw, tone: "lifecycle" },
  "group.activated": { icon: Rocket, tone: "lifecycle" },
  "group.completed": { icon: PartyPopper, tone: "lifecycle" },
  "share.accepted": { icon: UserCheck, tone: "share" },
  "share.rejected": { icon: UserX, tone: "share" },
};

export function visualForEvent(event: string | null) {
  const match = event ? EVENT_VISUAL[event as GroupActivityEvent] : undefined;
  const tone = match?.tone ?? "default";

  return {
    icon: match?.icon ?? History,
    class: TONE_CLASS[tone],
  };
}
