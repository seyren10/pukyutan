import { LayoutDashboard, Share2, Activity } from "@lucide/vue";
import type { Component } from "vue";
import type { RouteLocationRaw } from "vue-router";

export interface NavItem {
  label: string;
  to: RouteLocationRaw;
  icon: Component;
  /** Extra route-record names (e.g. a parent route) that should also count as this tab being active. */
  matchNames?: string[];
}

// Single source of truth for the app's primary sections — the desktop
// header nav and the mobile bottom tab bar both render from this list so
// adding/renaming a section only ever happens in one place. Kept to the
// sections that already lived in the desktop nav; account-level links
// (Profile, logout) stay wherever each surface already puts them.
export const useNavItems = (): NavItem[] => [
  {
    label: "Dashboard",
    to: { name: "groups.index" },
    icon: LayoutDashboard,
    // "groups" is the parent record shared by groups.index AND groups.detail
    // (and its children) — viewing a single group should still light up
    // the Dashboard tab, since that's the section it was reached from.
    matchNames: ["groups"],
  },
  { label: "Shared Groups", to: { name: "shared-groups.index" }, icon: Share2 },
  { label: "Activities", to: { name: "activities.index" }, icon: Activity },
];