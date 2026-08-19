export { default as GroupCard } from "./GroupCard.vue";
export { default as GroupCardEmpty } from "./GroupCardEmpty.vue";
export { default as GroupCardSkeleton } from "./GroupCardSkeleton.vue";
export { default as GroupCycleVisual } from "./GroupCardCycleVisual.vue";
export { default as GroupCycleSummaryCard } from "./GroupCycleSummaryCard.vue";

// "copy-invite-code" isn't listed here — GroupCardDropdown handles it
// entirely on its own (clipboard + toast), it doesn't need to open a
// dialog owned by the parent the way the others below do.
export const GROUPCARD_DROPDOWN_EVENT = ["add-member", "edit-group", 'rename-group', 'delete-group'] as const;
export type GroupCardDropdownEvent = (typeof GROUPCARD_DROPDOWN_EVENT)[number];
