import { UserPlus, CheckCircle2, XCircle, UserMinus } from "@lucide/vue";
import type { RouteLocationRaw } from "vue-router";
import type { AppNotification } from "./type";

export function describeNotification(notification: AppNotification) {
  const { data } = notification;

  switch (data.type) {
    case "share.requested":
      return {
        icon: UserPlus,
        text: `${data.requester_name} requested to join "${data.group_name}"`,
        to: {
          name: "groups.detail.access.index",
          params: { id: data.group_id },
        } satisfies RouteLocationRaw,
      };
    case "share.responded":
      return data.status === "accepted"
        ? {
            icon: CheckCircle2,
            text: `Your request to join "${data.group_name}" was accepted`,
            to: {
              name: "groups.detail",
              params: { id: data.group_id },
            } satisfies RouteLocationRaw,
          }
        : {
            icon: XCircle,
            text: `Your request to join "${data.group_name}" was declined`,
            to: null,
          };
    case "share.revoked":
      return {
        icon: UserMinus,
        text: `Your access to "${data.group_name}" was removed`,
        to: null,
      };
  }
}
