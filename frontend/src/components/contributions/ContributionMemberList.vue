<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { CheckCircle2, ChevronsUpDownIcon, CirclePlus } from '@lucide/vue'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '../ui/collapsible'
import ContributionForm from './ContributionForm.vue'
import type { GroupDetail } from '@/features/group/type.ts'
import { useGroupDetail } from '@/features/group/composables/use-group.ts'
import { Tooltip, TooltipContent, TooltipTrigger } from '../ui/tooltip/index.ts'
import AppAvatar from '../app/AppAvatar.vue'

const { groupDetail } = defineProps<{
    loading?: boolean
    groupDetail: GroupDetail
}>()

const emit = defineEmits<{ submit: [payload: { member_id: number; amount: number }[]] }>()
const { nextCycle } = useGroupDetail(() => groupDetail)

// Per-member controlled open state, keyed by member id — a plain ref
// holding an object is still deep-reactive in Vue 3, so assigning a
// new key here (openStates.value[id] = false) triggers updates fine.
const openStates = ref<Record<number, boolean>>({})

function closeCollapsible(memberId: number) {
    openStates.value[memberId] = false
}
</script>

<template>
    <div class="flex flex-col gap-2 py-1">
        <Collapsible v-for="member in nextCycle?.members" :key="member.id" :open="openStates[member.id] ?? false"
            class="rounded-lg border border-border p-2.5" #="{ open }"
            @update:open="(value) => (openStates[member.id] = value)">
            <div class="flex items-center gap-4">
                <AppAvatar class="size-8 shrink-0" :seed="member.dicebear_seed" :fallback="member.name"/>

                <div class="flex min-w-0 flex-1 flex-col">
                    <span class="truncate text-sm font-medium text-foreground">{{ member.name }}</span>

                    <span v-if="member.balance === member.expected_total" class="text-xs text-muted-foreground">
                        No payments yet
                    </span>

                    <div v-else-if="member.balance <= 0" class="flex gap-2 text-xs text-success">
                        <span class="flex items-center gap-1">
                            <CheckCircle2 class="size-3" />
                            All caught up
                        </span>
                        <Tooltip v-if="member.balance < 0">
                            <TooltipTrigger>
                                <span class="flex items-center gap-1">
                                    <CirclePlus class="size-3" />
                                    ₱{{ Math.abs(member.balance).toLocaleString() }}
                                </span>
                            </TooltipTrigger>
                            <TooltipContent class="max-w-xs">
                                <p>
                                    ₱{{ Math.abs(member.balance).toLocaleString() }} in credit, automatically applied to
                                    whatever they owe next.
                                </p>
                            </TooltipContent>
                        </Tooltip>
                    </div>

                    <span v-else class="text-xs text-accent-foreground">
                        Owes ₱{{ member.balance.toLocaleString() }}
                    </span>
                </div>

                <CollapsibleTrigger as-child>
                    <Button :variant="open ? 'secondary' : 'ghost'" size="icon">
                        <ChevronsUpDownIcon />
                    </Button>
                </CollapsibleTrigger>
            </div>

            <CollapsibleContent v-if="nextCycle" class="p-4">
                <ContributionForm :cycle-id="nextCycle.id" :remaining-amount="Math.max(member.balance, 0)"
                    :group-id="groupDetail.id" :member-id="member.id" @recorded="closeCollapsible(member.id)" />
            </CollapsibleContent>
        </Collapsible>
    </div>
</template>