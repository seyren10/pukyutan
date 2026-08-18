export { default as GroupCard } from "./GroupCard.vue";
export { default as GroupCardEmpty } from "./GroupCardEmpty.vue";
export { default as GroupCardSkeleton } from "./GroupCardSkeleton.vue";
export { default as GroupCycleVisual } from "./GroupCardCycleVisual.vue";
export { default as GroupCycleSummaryCard } from "./GroupCycleSummaryCard.vue";

export const GROUPCARD_DROPDOWN_EVENT = ["add-member", "edit-group", 'rename-group'] as const;
export type GroupCardDropdownEvent = (typeof GROUPCARD_DROPDOWN_EVENT)[number];
